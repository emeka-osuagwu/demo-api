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

class UpdateUser extends Controller
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
            'juju' => "numeric|nullable",
            'begi' => "numeric|nullable",
            'jara' => "numeric|nullable",
            'score' => "numeric|nullable", // remove score
            'level' => "string|nullable", // Need fix -> this is the current you are on.
            'points' => "numeric|nullable", // Need fix -> why do we have points and score at the same time
            "password" => "string|nullable",
            'game_won' => "numeric|nullable", 
            'device_id' => "string|nullable",
            "giraffing" => "numeric|nullable",
            "full_name" => "string|nullable",
            'game_played' => "numeric|nullable", // Need fix -> this should be dirived from the total number of completed_puzzles, take this out 
            'average_score' => "numeric|nullable",
            'highest_score' => "numeric|nullable",
            'longest_streak' => "numeric|nullable",
            'current_streak' => "numeric|nullable",
            "completed_puzzles" => "string|nullable",
            "completed_puzzle_levels" => "string|nullable", // Need fix -> reachout to emeka about what to do with this 
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
        // Need fix -> not all the values here are in the request
        $user_payload = [
            "juju" => $request->juju,
            "begi" => $request->begi,
            'jara' => $request->jara,
            'score' => $request->score,
            'level' => $request->level,
            "totem" => $request->totem,
            'points' => $request->points, 
            "cowries" => $request->cowries, // this is missing in request
            'game_won' => $request->game_won,
            "password" => $request->password,
            "full_name" => $request->full_name,
            'device_id' => $request->device_id,
            "giraffing" => $request->giraffing,
            'game_played' => $request->game_played,
            'average_score' => $request->average_score,
            'highest_score' => $request->highest_score,
            "padi_play_wins" => $request->padi_play_wins, // Need fix -> this should not be here, it should be generated from the game result
            'longest_streak' => $request->longest_streak, // Need fix -> this should not be here, it should be generated from the game result
            'current_streak' => $request->current_streak, // Need fix -> this should not be here, it should be generated from the game result
            "padi_play_losses" => $request->padi_play_losses, // Need fix -> this should not be here, it should be generated from the game result
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
        */
        // $notification_remark = "Schedule is working and user successfully updated at time -> " . now()->format('Y-m-d H:i:s');

        // $this->pushNotificationContract
        //     ->setType('update_user_notification')
        //     ->setBody($notification_remark)
        //     ->setIcon('update_user_update')
        //     ->setTokens([$request->auth_user["payload"]["id"]])
        //     ->setTitle('System Check')
        //     ->setPayload([])
        //     ->sendNotification();

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::USER_REQUEST_SUCCESSFUL);
    }
}
