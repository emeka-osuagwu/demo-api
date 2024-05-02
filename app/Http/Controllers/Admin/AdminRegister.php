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
use App\Enums\ServiceResponseMessageEnum;
use App\Enums\ResponseCode\AuthResponseCode;
use App\Enums\RolesEnums;

class AdminRegister extends Controller
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
            "email" => "string|email|required",
            "password" => "string|required",
            "full_name" => "string|required",
            "push_token" => "string|nullable",
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
        $admin_profile = $this->userService->findWhere(['email' => $request->email]);

        /*
        |--------------------------------------------------------------------------
        | check if auth user service request failed
        |--------------------------------------------------------------------------
        */
        if (!$admin_profile['is_successful'] || count($admin_profile["response"]) > 0) {
            return $this->sendResponse([], AuthResponseCode::AUTH_USER_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $create_admin_payload = [
            "role" => RolesEnums::ADMIN,
            "email" => $request->email,
            "password" => $request->password,
            "full_name" => $request->full_name,
        ];

        /*
        |--------------------------------------------------------------------------
        | create admin user
        |--------------------------------------------------------------------------
        */
        $create_admin_response = $this->userService->create($create_admin_payload);

        /*
        |--------------------------------------------------------------------------
        | check if validation failed
        |--------------------------------------------------------------------------
        */
        if (!$create_admin_response['is_successful'] || $create_admin_response['status'] == ServiceResponseMessageEnum::VALIDATION_ERROR->value) {
            return $this->sendResponse($create_admin_response['response'], AuthResponseCode::AUTH_USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set push token
        |--------------------------------------------------------------------------
        */
        if($request->has('push_token')){
            $save_push_token = $this->cacheService->saveRecord("push_tokens:{$create_admin_response["response"]['id']}", ['push_token' => $request->push_token]);

            /*
            |--------------------------------------------------------------------------
            | check an error occurred while saving the push_tokens
            |--------------------------------------------------------------------------
            */
            if(!$save_push_token["is_successful"]){
                return $this->sendResponse([], AuthResponseCode::AUTH_USER_SERVICE_REQUEST_ERROR);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | generate token
        |--------------------------------------------------------------------------
        */
        $token = $this->authService->createAuthentication($create_admin_response['response']);

        /*
        |--------------------------------------------------------------------------
        | response data
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(['token' => $token], AuthResponseCode::AUTH_USER_REQUEST_SUCCESSFUL);
    }
}
