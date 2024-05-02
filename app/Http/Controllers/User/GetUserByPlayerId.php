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
use App\Enums\ServiceResponseMessageEnum;

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
use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GetUserByPlayerId extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected UserService $userService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($payload)
    {
        return Validator::make($payload,[
            "player_id" => "string|required"
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get User By Player Id
    |--------------------------------------------------------------------------
    */
    public function __invoke($player_id)
    {
        /*
        |--------------------------------------------------------------------------
        | validate payload
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(['player_id' => $player_id]);

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::USER_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find user
        |--------------------------------------------------------------------------
        */
        $find_user_response = $this->userService->findWhere(["player_id" => $player_id]);

        /*
        |--------------------------------------------------------------------------
        | check if user requuest fails
        |--------------------------------------------------------------------------
        */
        if(!$find_user_response["is_successful"] || !count($find_user_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | return success response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(UserResource::make($find_user_response["response"][0]), ResponseCodeEnums::USER_REQUEST_SUCCESSFUL);
    }
}
