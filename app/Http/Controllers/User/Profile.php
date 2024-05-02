<?php

namespace App\Http\Controllers\User;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;

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

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\UserResource;

/*
|--------------------------------------------------------------------------
| Illumnate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class Profile extends Controller
{
    use ResponseTrait;
    public function __construct(
        protected UserService $userService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Find User
    |--------------------------------------------------------------------------
    */
    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | find user
        |--------------------------------------------------------------------------
        */
        $user_response = $this->userService->findWhere(['id' => request()->auth_user["payload"]["id"]]);

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $user_response = $user_response['response'][0];

        /*
        |--------------------------------------------------------------------------
        | return sucessful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(UserResource::make($user_response), ResponseCodeEnums::USER_REQUEST_SUCCESSFUL);
    }
}
