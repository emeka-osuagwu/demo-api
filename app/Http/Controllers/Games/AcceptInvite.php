<?php

namespace App\Http\Controllers\Games;

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
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\CacheService;
use App\Services\PuzzlesService;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\GameResource;

class AcceptInvite extends Controller
{
    use ResponseTrait;
    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected CacheService $cacheService,
        protected PuzzlesService $puzzlesService
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
    | Game Create
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::GAME_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch game from redis
        |--------------------------------------------------------------------------
        */
        $fetch_game_response = $this->cacheService->getAll("game_sessions:*:*:{$request->session_id}");

        /*
        |--------------------------------------------------------------------------
        | check if game exists and if player id is not similar to player 2
        |--------------------------------------------------------------------------
        */
        if (!$fetch_game_response['is_successful'] || $fetch_game_response['status'] == ServiceResponseMessageEnum::EMPTY_PAYLOAD->value) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $fetch_game_response = $fetch_game_response['response'][0];

        /*
        |--------------------------------------------------------------------------
        | check if user as permission to accept invite
        |--------------------------------------------------------------------------
        */
        if ($fetch_game_response['player_2'] != $request->auth_user["payload"]["player_id"]) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_PLAYER_MISMATCH_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if challenge is already accepted
        |--------------------------------------------------------------------------
        */
        if ($fetch_game_response["challenge_accepted"]) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_IN_PROGRESS);
        }

        /*
        |--------------------------------------------------------------------------
        | update game payload in redis
        |--------------------------------------------------------------------------
        */
        $this->cacheService->updateWhere("game_sessions:{$fetch_game_response["player_1"]}:{$fetch_game_response["player_2"]}:{$request->session_id}", ["challenge_accepted" => 1]);

        /*
        |--------------------------------------------------------------------------
        | refetch game from redis
        |--------------------------------------------------------------------------
        */
        $fetch_game_response = $this->cacheService->getAll("game_sessions:*:*:{$request->session_id}");

        /*
        |--------------------------------------------------------------------------
        | check if game exists and if player id is not similar to player 2
        |--------------------------------------------------------------------------
        */
        if (!$fetch_game_response['is_successful'] || $fetch_game_response['status'] == ServiceResponseMessageEnum::EMPTY_PAYLOAD->value) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $fetch_game_response = $fetch_game_response['response'][0];

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(GameResource::make($fetch_game_response), ResponseCodeEnums::GAME_REQUEST_SUCCESSFUL);
    }
}
