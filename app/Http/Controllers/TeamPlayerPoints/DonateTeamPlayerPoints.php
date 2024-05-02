<?php

namespace App\Http\Controllers\TeamPlayerPoints;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;
use App\Services\TeamService;
use App\Services\CacheService;
use App\Services\TeamPlayerService;
use App\Services\TransactionService;
use App\Services\PointDonationService;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\CardContract;
use App\Contracts\PushNotificationContract;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\PaystackResponseMessageEnum;

class DonateTeamPlayerPoints extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        public CardContract $cardContract,
        protected UserService $userService,
        protected TeamService $teamService,
        protected CacheService $cacheService,
        protected TeamPlayerService $teamPlayerService,
        protected TransactionService $transactionService,
        protected PointDonationService $pointDonationService,
        public PushNotificationContract $pushNotificationContract,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "points" => "numeric|required",
            "receiver_player_id" => "string|required",
            "provider_transaction_reference" => "string|required"
        ]);
    }
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::POINT_DONATIONS_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if reciever exists
        |--------------------------------------------------------------------------
        */
        $find_receiver_response = $this->userService->findWhere(["player_id" => $request->receiver_player_id]);

        /*
        |--------------------------------------------------------------------------
        | check if the service request is failed
        |--------------------------------------------------------------------------
        */
        if (!$find_receiver_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if user is not found
        |--------------------------------------------------------------------------
        */
        if (!count($find_receiver_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | check if the receiver is self
        |--------------------------------------------------------------------------
        */
        if ($request->receiver_player_id === $request->auth_user["payload"]["player_id"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_CANNOT_DONATE_POINT_TO_SELF);
        }

        /*
        |--------------------------------------------------------------------------
        | find transacaction by reference in transaction table
        |--------------------------------------------------------------------------
        */
        $find_transaction_response = $this->transactionService->findWhere(["provider_transaction_reference" => $request->provider_transaction_reference]);

        /*
        |--------------------------------------------------------------------------
        | check if transaction request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_transaction_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if transaction already exists
        |--------------------------------------------------------------------------
        */
        if (count($find_transaction_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_ALREADY_EXISTS);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch transaction by reference from paystack
        |--------------------------------------------------------------------------
        */
        $find_paystack_transaction_response = $this->cardContract->fetchTransactionByReference([
            "transaction_reference" => $request->provider_transaction_reference
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if request is not successful
        |--------------------------------------------------------------------------
        */
        if (!$find_paystack_transaction_response['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::PAYSTACK_TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check for transaction reference not found
        |--------------------------------------------------------------------------
        */
        if (!$find_paystack_transaction_response['is_successful'] && $find_paystack_transaction_response['status'] == PaystackResponseMessageEnum::TRANSACTION_REFERENCE_NOT_FOUND->value) {
            return $this->sendResponse([], ResponseCodeEnums::PAYSTACK_TRANSACTION_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set transaction payload
        |--------------------------------------------------------------------------
        */
        $create_transaction_payload = [
            "id" => generateUUID(),
            'amount' => (string) convertKoboToNaira($find_paystack_transaction_response["response"]["data"]["amount"]),
            'status' => 'active',
            'purchase' => 'in_store_purchase',
            'player_id' => $request->player_id,
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString(),
            'payment_method' => 'card',
            'payment_channel' => $find_paystack_transaction_response["response"]["data"]["channel"],
            'payment_method_id' => (string) $find_paystack_transaction_response["response"]["data"]["id"],
            'transaction_reference' => generateUUID(),
            'provider_transaction_reference' => $request->provider_transaction_reference,
        ];

        /*
        |--------------------------------------------------------------------------
        | create transaction
        |--------------------------------------------------------------------------
        */
        $create_transaction_response = $this->transactionService->create($create_transaction_payload);

        /*
        |--------------------------------------------------------------------------
        | check if transanction service request fails
        |--------------------------------------------------------------------------
        */
        if (!$create_transaction_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $create_point_donation_payload = [
            "id" => generateUUID(),
            "points" => $request->points,
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString(),
            "transaction_id" => $create_transaction_response["response"]["id"],
            "giver_player_id" => $request->auth_user["payload"]["player_id"],
            "receiver_player_id" => $request->receiver_player_id,
        ];

        /*
        |--------------------------------------------------------------------------
        | save point donation payload
        |--------------------------------------------------------------------------
        */
        $create_point_donation_response = $this->pointDonationService->create($create_point_donation_payload);

        /*
        |--------------------------------------------------------------------------
        | check if point donation service request fails
        |--------------------------------------------------------------------------
        */
        if (!$create_point_donation_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::POINT_DONATIONS_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        |  find sender information
        |--------------------------------------------------------------------------
        */
        $find_sender_response = $this->userService->findWhere(["id" => $request->auth_user["payload"]["id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if the service request is failed
        |--------------------------------------------------------------------------
        */
        if (!$find_sender_response["is_successful"] || !count($find_sender_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $find_sender_response = $find_sender_response["response"][0];
        $sender_points = $find_sender_response["points"] === "null" ? 0 : (int) $find_sender_response["points"];
        $sub = $sender_points - $request->points;

        /*
        |--------------------------------------------------------------------------
        | check if the the sender has sufficient points to send
        |--------------------------------------------------------------------------
        */
        if ($sub < 0) {
            return $this->sendResponse([], ResponseCodeEnums::INSUFFICIENT_POINTS_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $selector = "id='{$request->auth_user["payload"]["id"]}'";
        $update_sender_payload = [
            "points" => (string) $sub
        ];

        /*
        |--------------------------------------------------------------------------
        | update sender data
        |--------------------------------------------------------------------------
        */
        $update_sender_response = $this->userService->update($selector, $update_sender_payload);

        /*
        |--------------------------------------------------------------------------
        | check if the service request is failed
        |--------------------------------------------------------------------------
        */
        if (!$update_sender_response["is_succeessful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_receiver_response = $find_receiver_response["response"][0];
        $receiver_points = $find_receiver_response["points"] === "null" ? 0 : (int) $find_receiver_response["points"];
        $add = $receiver_points + $request->points;

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $add_point_selector = "player_id='{$request->receiver_player_id}'";
        $update_receiver_payload = [
            "points" => strval($add)
        ];

        /*
        |--------------------------------------------------------------------------
        | update receiver data
        |--------------------------------------------------------------------------
        */
        $update_receiver_response = $this->userService->update($add_point_selector, $update_receiver_payload);

        /*
        |--------------------------------------------------------------------------
        | check if the service request is failed
        |--------------------------------------------------------------------------
        */
        if (!$update_receiver_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find sender push token
        |--------------------------------------------------------------------------
        */
        $sender_push_token = $this->cacheService->findWhere("push_tokens:{$request->auth_user["payload"]["id"]}")["push_token"];

        /*
        |--------------------------------------------------------------------------
        | check if products have been saved
        |--------------------------------------------------------------------------
        */
        if (!$sender_push_token["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::PUSH_TOKEN_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        |  find reciever push token
        |--------------------------------------------------------------------------
        */
        $receiver_push_token = $this->cacheService->findWhere("push_tokens:{$find_receiver_response["id"]}")["push_token"];

        /*
        |--------------------------------------------------------------------------
        | check if products have been saved
        |--------------------------------------------------------------------------
        */
        if (!$receiver_push_token["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::PUSH_TOKEN_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $notification_remark = "Schedule is working and payment with point donation has been made successfully at time -> " . now()->format('Y-m-d H:i:s');

        /*
        |--------------------------------------------------------------------------
        | send push notification to sender and receiver
        |--------------------------------------------------------------------------
        */
        $this->pushNotificationContract
            ->setType('In_app_purchase_notification')
            ->setBody($notification_remark)
            ->setIcon('stock_ticker_update')
            ->setTokens([$sender_push_token, $receiver_push_token])
            ->setTitle('System Check')
            ->setPayload([])
            ->sendNotification();

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::POINT_DONATIONS_REQUEST_SUCCESSFUL);
    }
}
