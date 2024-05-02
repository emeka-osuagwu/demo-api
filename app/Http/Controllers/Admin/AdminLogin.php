<?php

namespace App\Http\Controllers\Admin;

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
use App\Enums\ResponseCode\AuthResponseCode;

class AdminLogin extends Controller
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
            "password" => "string|required",
            "push_token" => "string|nullable",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Login
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
        | find admin
        |--------------------------------------------------------------------------
        */
        $admin_profile = $this->userService->findWhere(['email' => $request->email]);

        /*
        |--------------------------------------------------------------------------
        | check if auth user service request failed
        |--------------------------------------------------------------------------
        */
        if (!$admin_profile['is_successful']) {
            return $this->sendResponse([], AuthResponseCode::AUTH_USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | Check if user profile is found
        |--------------------------------------------------------------------------
        */
        if (count($admin_profile) < 1) {
            return $this->sendResponse([], AuthResponseCode::INVALID_LOGIN_CREDENTIALS);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $admin = $admin_profile['response'][0];

        /*
        |--------------------------------------------------------------------------
        | Verify user password
        |--------------------------------------------------------------------------
        */
        if (!hashCheck($request->password, $admin['password'])) {
            return $this->sendResponse([], AuthResponseCode::INVALID_LOGIN_CREDENTIALS);
        }

        /*
        |--------------------------------------------------------------------------
        | set push token
        |--------------------------------------------------------------------------
        */
        if($request->has('push_token')){
            $this->cacheService->saveRecord("push_tokens:{$admin_profile['response'][0]['id']}", [
                'email' => $admin_profile['response'][0]['email'],
                'push_token' => $request->push_token
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | generate token
        |--------------------------------------------------------------------------
        */
        $token = $this->authService->createAuthentication(UserResource::make($admin_profile['response'][0] ?? $admin_profile['response']));

        /*
        |--------------------------------------------------------------------------
        | response data
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(['token' => $token], AuthResponseCode::AUTH_USER_REQUEST_SUCCESSFUL);
    }
}
