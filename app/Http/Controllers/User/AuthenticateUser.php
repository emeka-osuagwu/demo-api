<?php

namespace App\Http\Controllers\User;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/

use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\CacheService;
use App\Services\LevelService;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;
use App\Enums\ResponseCode\AuthResponseCode;

class AuthenticateUser extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected AuthService $authService,
        protected UserService $userService,
        protected LevelService $levelService,
        protected CacheService $cacheService,
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
            "email" => "string|email|nullable",
            "full_name" => "string|nullable",
            "push_token" => "string|nullable",
            "authorization_token" => "string|required",
            "authorization_provider" => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create User
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), AuthResponseCode::AUTH_USER_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find user if authorization token is passed or through email
        |--------------------------------------------------------------------------
        */
        if(isset($request->authorization_token)) {
            $find_user_response = $this->userService->findWhere(['authorization_token' => $request->authorization_token]);
        }
        else {
            $find_user_response = $this->userService->findWhere(['email' => $request->email]);
        }

        /*
        |--------------------------------------------------------------------------
        | check if auth user service request failed
        |--------------------------------------------------------------------------
        */
        if (!$find_user_response['is_successful'] && $find_user_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value) {
            return $this->sendResponse([], AuthResponseCode::AUTH_USER_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | if user exists and if the push token is not null
        |--------------------------------------------------------------------------
        */
        if ($find_user_response['is_successful'] && !count($find_user_response['response'])) {
            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $create_user_payload = [
                "id" => generateUUID(),
                'jara' => '0',
                "juju" => '0',
                "begi" => '0',
                'score' => '0',
                "totem" => "0",
                "email" => $request->email,
                'points' => '0',
                'cowries' => '0',
                'game_won' => '0',
                "password" => "0",
                'device_id' => generateUUID(),
                "full_name" => $request->full_name,
                "giraffing" => '0',
                'player_id' => generatePlayerId(),
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString(),
                'game_played' => '0',
                'highest_score' => '0',
                'average_score' => '0',
                'current_streak' => '0',
                'longest_streak' => '0',
                "padi_play_wins" => "0",
                "padi_play_losses" => "0",
                "completed_puzzles" => [],
                "authorization_token" => $request->authorization_token,
                "authorization_provider" => $request->authorization_provider,
                "completed_puzzle_levels" => [],
            ];

            /*
            |--------------------------------------------------------------------------
            | create user
            |--------------------------------------------------------------------------
            */
            $create_user_response = $this->userService->create($create_user_payload);

            /*
            |--------------------------------------------------------------------------
            | check if validation failed
            |--------------------------------------------------------------------------
            */
            if (!$create_user_response['is_successful'] && $create_user_response['status'] == ServiceResponseMessageEnum::VALIDATION_ERROR->value) {
                return $this->sendResponse($create_user_response['response'], AuthResponseCode::AUTH_USER_SERVICE_VALIDATION_ERROR);
            }

            /*
            |--------------------------------------------------------------------------
            | check if request failed
            |--------------------------------------------------------------------------
            */
            if (!$create_user_response['is_successful']) {
                return $this->sendResponse($create_user_response['response'], AuthResponseCode::AUTH_USER_SERVICE_REQUEST_ERROR);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $user = $create_user_response['response'] ?? $find_user_response['response'][0];

        /*
        |--------------------------------------------------------------------------
        | set push token
        |--------------------------------------------------------------------------
        */
        $this->cacheService->saveRecord("push_tokens:{$user['id']}", ['push_token' => $request->push_token]);

        /*
        |--------------------------------------------------------------------------
        | generate token
        |--------------------------------------------------------------------------
        */
        $token = $this->authService->createAuthentication($user);

        /*
        |--------------------------------------------------------------------------
        | response data
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(['token' => $token], AuthResponseCode::AUTH_USER_REQUEST_SUCCESSFUL);
    }
}
