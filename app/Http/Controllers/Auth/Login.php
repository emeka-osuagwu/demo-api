<?php

namespace App\Http\Controllers\Auth;

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
use App\Http\Resources\UserResource;

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

class Login extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected AuthService $authService,
        protected UserService $userService,
        protected CacheService $cacheService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "email" => "email|required",
            "image" => "url|nullable",
            "auth_id" => "string|required",
            "full_name" => "string|required",
            "push_token" => "string|nullable",
            "authorization_token" => "string|required",
            "authorization_provider" => "string|required|in:apple,google",
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
        $find_user_response = $this->userService->findWhere($request->only('auth_id', 'email'));

        /*
        |--------------------------------------------------------------------------
        | check if auth user service request failed
        |--------------------------------------------------------------------------
        */
        if (!$find_user_response['is_successful'] || !count($find_user_response['response'])) {
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
                'image' => $request->image,
                'auth_id' => $request->auth_id,
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
            $find_user_response = $this->userService->create($create_user_payload);

            if (!$find_user_response['is_successful']) {
                return $this->sendResponse([], AuthResponseCode::AUTH_USER_SERVICE_REQUEST_ERROR);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | set push token
        |--------------------------------------------------------------------------
        */
        if($request->has('push_token')){
            $this->cacheService->saveRecord("push_tokens:{$find_user_response['response'][0]['id']}", [
                'email' => $find_user_response['response'][0]['email'],
                'push_token' => $request->push_token
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | generate token
        |--------------------------------------------------------------------------
        */
        $token = $this->authService->createAuthentication(UserResource::make($find_user_response['response'][0] ?? $find_user_response['response']));

        /*
        |--------------------------------------------------------------------------
        | response data
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(['token' => $token], AuthResponseCode::AUTH_USER_REQUEST_SUCCESSFUL);
    }
}
