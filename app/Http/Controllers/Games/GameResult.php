<?php

namespace App\Http\Controllers\Games;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\GameService;
use App\Services\CacheService;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;


class GameResult extends Controller
{
    use ResponseTrait;
    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected GameService $gameService,
        protected CacheService $cacheService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "session_id" => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | validate incoming request
    |--------------------------------------------------------------------------
    */
    public function __invoke($session_id)
    {
        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validation = $this->requestValidation(["session_id" => $session_id]);
        if ($validation->fails()) {
            return $this->sendResponse($validation->errors(), ResponseCodeEnums::GAME_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch game from redis
        |--------------------------------------------------------------------------
        */
        $game_result_response = $this->cacheService->findWhere("game_sessions:{$session_id}");

        /*
        |--------------------------------------------------------------------------
        | check if game exists
        |--------------------------------------------------------------------------
        */
        if (!count($game_result_response)) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | check if game has been completed
        |--------------------------------------------------------------------------
        */
        if(!$game_result_response["completed"]){
            return $this->sendResponse([], ResponseCodeEnums::GAME_IN_PROGRESS);
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse($game_result_response, ResponseCodeEnums::GAME_REQUEST_SUCCESSFUL);
    }
}
