<?php

namespace App\Http\Controllers\User;

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
use App\Services\LevelService;
use App\Services\PuzzlesService;

/*
|--------------------------------------------------------------------------
| providers Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Providers\CacheProvider;

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
use App\Contracts\PushNotificationContract;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

class UpdateUserCompletedPuzzles extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected UserService $userService,
        protected LevelService $levelService,
        protected PuzzlesService $puzzlesService,
        protected CacheProvider $cacheProvider,
        public PushNotificationContract $pushNotificationContract
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function validateRequest($data)
    {
        return Validator::make($data, [
            "completed_puzzles" => "string",
            "completed_puzzle_levels" => "string"
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $auth_user_id = $request->auth_user["payload"]["id"];

        /*
        |--------------------------------------------------------------------------
        | check if user exists
        |--------------------------------------------------------------------------
        */
        $find_single_user = $this->userService->findWhere(["id" => $auth_user_id]);

        /*
        |--------------------------------------------------------------------------
        | if user request fails
        |--------------------------------------------------------------------------
        */
        if ($find_single_user["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$find_single_user["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | if user not found
        |--------------------------------------------------------------------------
        */
        if ($find_single_user["status"] == ServiceResponseMessageEnum::SUCCESSFUL->value && $find_single_user["is_successful"] && !count($find_single_user["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set update user payload
        |--------------------------------------------------------------------------
        */
        $user_payload = [
            'completed_puzzles' => $request->completed_puzzles,
            "completed_puzzle_levels" => $request->completed_puzzle_levels,
        ];

        /*
        |--------------------------------------------------------------------------
        | filter incoming requests
        |--------------------------------------------------------------------------
        */
        foreach ($user_payload as $key => $value) {
            /*
            |--------------------------------------------------------------------------
            | removing fields with empty values
            |--------------------------------------------------------------------------
            */
            if (empty($value)) {
                unset($user_payload[$key]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | if the request is empty
        |--------------------------------------------------------------------------
        */
        if (count($user_payload) < 1) {
            return $this->sendResponse([], ResponseCodeEnums::USER_REQUEST_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | update user payload
        |--------------------------------------------------------------------------
        */
        $update_user = $this->userService->update("id='{$auth_user_id}'", $user_payload);

        /*
        |--------------------------------------------------------------------------
        | if update user request fails
        |--------------------------------------------------------------------------
        */
        if ($update_user["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$update_user["is_successful"]) {
            return $this->sendResponse($update_user["response"], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send notification of the user updated
        |--------------------------------------------------------------------------
        | Need fix -> use the right comment here
        */
        $notification_remark = "Schedule is working and user successfully updated at time -> " . now()->format('Y-m-d H:i:s');

        $this->pushNotificationContract
            ->setType('update_user_notification')
            ->setBody($notification_remark)
            ->setIcon('update_user_update')
            ->setTokens([$request->auth_user["payload"]["id"]])
            ->setTitle('System Check')
            ->setPayload([])
            ->sendNotification();

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::USER_REQUEST_SUCCESSFUL);
    }
}
