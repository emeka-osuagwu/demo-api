<?php

namespace App\Http\Controllers\Products;

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
use App\Enums\ServiceResponseMessageEnum;
use App\Enums\PaystackResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;
use App\Services\CacheService;
use App\Services\ProductService;
use App\Services\TransactionService;


class Payment extends Controller
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
        protected CacheService $cacheService,
        protected ProductService $productService,
        protected TransactionService $transactionService,
        public PushNotificationContract $pushNotificationContract,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "product_id" => "string|required",
            "provider_transaction_reference" => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Make Product Payment
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | set payment payload
        |--------------------------------------------------------------------------
        */
        $payment_payload = [
            "player_id" => $request->auth_user["payload"]["player_id"],
            "product_id" => $request->product_id,
            "provider_transaction_reference" => $request->provider_transaction_reference,
        ];

        /*
        |--------------------------------------------------------------------------
        | validate requests
        |--------------------------------------------------------------------------
        */
        $validation = $this->requestValidation($payment_payload);
        
        if ($validation->fails()) {
            return $this->sendResponse($validation->errors(), ResponseCodeEnums::PRODUCT_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find transacaction by reference
        |--------------------------------------------------------------------------
        */
        $find_transaction_response = $this->transactionService->findWhere(["provider_transaction_reference" => $payment_payload["provider_transaction_reference"]]);

        /*
        |--------------------------------------------------------------------------
        | check if transaction request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_transaction_response["is_successful"] && $find_transaction_response["status"] === ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if transaction already exist
        |--------------------------------------------------------------------------
        */
        if ($find_transaction_response["is_successful"] && $find_transaction_response["status"] === ServiceResponseMessageEnum::SUCCESSFUL->value && count($find_transaction_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_ALREADY_EXISTS);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch transaction by reference from paystack
        |--------------------------------------------------------------------------
        */
        $find_paystack_transaction_response = $this->cardContract->fetchTransactionByReference(["transaction_reference" => $payment_payload['provider_transaction_reference']]);

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
        | find product
        |--------------------------------------------------------------------------
        */
        $find_product_response = $this->productService->findWhere(["id" => $payment_payload["product_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_product_response["is_successful"] && $find_product_response["status"] === ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value) {
            return $this->sendResponse([], ResponseCodeEnums::PRODUCT_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if product does not exist
        |--------------------------------------------------------------------------
        */
        if ($find_product_response["is_successful"] && $find_product_response["status"] === ServiceResponseMessageEnum::SUCCESSFUL->value && !count($find_product_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::PRODUCT_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set create transaction payload
        |--------------------------------------------------------------------------
        */
        $create_transaction_payload = [
            "id" => generateUUID(),
            'amount' => strVal(convertKoboToNaira($find_paystack_transaction_response["response"]["data"]["amount"])),
            'status' => 'active',
            'purchase' => 'in_store_purchase',
            'player_id' => $payment_payload['player_id'],
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString(),
            'payment_method' => 'card',
            'payment_channel' => $find_paystack_transaction_response["response"]["data"]["channel"],
            // Need fix => plase call this provider_transaction_reference and use reference from the paystack response
            'payment_method_id' => strVal($find_paystack_transaction_response["response"]["data"]["id"]),
            'transaction_reference' => generateUUID(),
            'provider_transaction_reference' => $payment_payload['provider_transaction_reference'],
        ];

        /*
        |--------------------------------------------------------------------------
        | create transaction
        |--------------------------------------------------------------------------
        */
        $create_transaction_response = $this->transactionService->create($create_transaction_payload);

        /*
        |--------------------------------------------------------------------------
        | check if transaction service validation fails
        |--------------------------------------------------------------------------
        */
        if (!$create_transaction_response["is_successful"] && $create_transaction_response['status'] == ServiceResponseMessageEnum::VALIDATION_ERROR->value) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if transanction service request fails
        |--------------------------------------------------------------------------
        */
        if (!$create_transaction_response["is_successful"] && $create_transaction_response['status'] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find auth user
        |--------------------------------------------------------------------------
        */
        $find_user_response = $this->userService->findWhere(["id" => $request->auth_user["payload"]["id"]]);

        /*
        |--------------------------------------------------------------------------
        | if user request fails
        |--------------------------------------------------------------------------
        */
        if ($find_user_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$find_user_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | if user not found
        |--------------------------------------------------------------------------
        */
        if ($find_user_response["status"] == ServiceResponseMessageEnum::SUCCESSFUL->value && $find_user_response["is_successful"] && !count($find_user_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $auth_user_id = $request->auth_user["payload"]["id"];
        $find_user_response = $find_user_response["response"][0];
        $update_user_payload = $find_product_response["response"]["content"];

        /*
        |--------------------------------------------------------------------------
        | filter the user respone data to match product payload
        |--------------------------------------------------------------------------
        */
        foreach ($update_user_payload as $key => &$value) {
            /*
            |--------------------------------------------------------------------------
            | add the user existing points to the update user payload
            |--------------------------------------------------------------------------
            */
            if ($find_user_response[$key] && ($find_user_response[$key] !== "null" || $find_user_response[$key] !== null)) {
                $update_user_payload[$key] = strval($update_user_payload[$key] + (int) $find_user_response[$key]);
            }

            /*
            |--------------------------------------------------------------------------
            | cast any payload int value to string
            |--------------------------------------------------------------------------
            */
            if (!is_string($value)) {
                $value = strval($value);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | update user
        |--------------------------------------------------------------------------
        */
        $update_user_response = $this->userService->update("id='{$auth_user_id}'", $update_user_payload);

        /*
        |--------------------------------------------------------------------------
        | if update user request fails
        |--------------------------------------------------------------------------
        */
        if ($update_user_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$update_user_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send push notification of data being successfully purchased
        |--------------------------------------------------------------------------
        */
        // $push_token = $this->cacheService->findWhere("push_tokens:{$request->auth_user["payload"]["id"]}")["push_token"];
        // $notification_remark = "Schedule is working and payment made successfully at time -> " . now()->format('Y-m-d H:i:s');
        // $this->pushNotificationContract
        //     ->setType('In_app_purchase_notification')
        //     ->setBody($notification_remark)
        //     ->setIcon('stock_ticker_update')
        //     ->setTokens([$push_token])
        //     ->setTitle('System Check')
        //     ->setPayload([])
        //     ->sendNotification();

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::PRODUCT_REQUEST_SUCCESSFUL);
    }
}

