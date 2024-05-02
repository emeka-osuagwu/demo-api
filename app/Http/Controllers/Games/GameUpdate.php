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
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;
use App\Services\CacheService;

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
| Jobs Namespace
|--------------------------------------------------------------------------
*/
use App\Jobs\Games\CalculateGameResultJob;

class GameUpdate extends Controller
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
        protected CacheService $cacheService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation(array $data)
    {
        return Validator::make($data, [
            "session_id" => "string|required",
            "game_data" => "array|required|min:1",
            'game_data.*.id' => 'required|string',
            'game_data.*.word' => 'required|string',
            'game_data.*.level_id' => 'required|string',
            'game_data.*.description' => 'required|string',
            'game_data.*.level_number' => 'required|numeric',
        ]);
    }

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
        | fetch game data from redis
        |--------------------------------------------------------------------------
        */
        $find_game_response = $this->cacheService->getAll("game_sessions:*:*:{$request->session_id}");

        /*
        |--------------------------------------------------------------------------
        | check if game exists
        |--------------------------------------------------------------------------
        */
        if (!$find_game_response["is_successful"] && !count($find_game_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_game_response = $find_game_response["response"][0];

        /*
        |--------------------------------------------------------------------------
        | check if user as permission to accept update game
        |--------------------------------------------------------------------------
        */
        if (($find_game_response['player_1'] || $find_game_response['player_2']) != $request->auth_user["payload"]["player_id"]) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | check if the game is completed
        |--------------------------------------------------------------------------
        */
        if ($find_game_response["completed"]) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_REQUEST_FAILED);
        }

        /*
        |--------------------------------------------------------------------------
        | check if player 1 has completed the game
        |--------------------------------------------------------------------------
        */
        if($find_game_response['player_1'] == $request->auth_user["payload"]["player_id"] && $find_game_response["player_1_completed"]){
            return $this->sendResponse([], ResponseCodeEnums::UNABLE_TO_UPDATE_GAME_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $update_game_payload = [
            "player_1_games" => $request->game_data,
            "player_1_completed" => true,
        ];

        /*
        |--------------------------------------------------------------------------
        | update for player 2
        |--------------------------------------------------------------------------
        */
        if ($find_game_response['player_1'] != $request->auth_user["payload"]["player_id"]) {
            $update_game_payload = [
                "player_2_games" => $request->game_data,
                "player_2_completed" => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | update game payload in redis
        |--------------------------------------------------------------------------
        */
        $game_cache_key = "game_sessions:{$find_game_response['player_1']}:{$find_game_response['player_2']}:{$request->session_id}";
        $update_game_response = $this->cacheService->updateWhere($game_cache_key, $update_game_payload);

        /*
        |--------------------------------------------------------------------------
        | check if updat game was successful
        |--------------------------------------------------------------------------
        */
        if(!$update_game_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::UNABLE_TO_UPDATE_GAME_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch updated game session
        |--------------------------------------------------------------------------
        */
        $selector ="game_sessions:{$find_game_response['player_1']}:{$find_game_response['player_2']}:{$request->session_id}";
        $find_game_response = $this->cacheService->findWhere($selector);

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        if(!$find_game_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if game exists
        |--------------------------------------------------------------------------
        */
        if (!count($find_game_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_game_response = $find_game_response["response"];

        /*
        |--------------------------------------------------------------------------
        | send push notification if both player has completed the game
        |--------------------------------------------------------------------------
        */
        if($find_game_response["challenge_accepted"] == true && $find_game_response["player_1_completed"] && $find_game_response["player_2_completed"])
        {
            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $selector = "game_sessions:{$find_game_response['player_1']}:{$find_game_response['player_2']}:{$request->session_id}";
            $update_game_payload = ["completed" => true];

            /*
            |--------------------------------------------------------------------------
            | update game payload in redis
            |--------------------------------------------------------------------------
            */
            $update_game_response = $this->cacheService->updateWhere($selector, $update_game_payload);

            /*
            |--------------------------------------------------------------------------
            | check if update game was successful
            |--------------------------------------------------------------------------
            */
            if(!$update_game_response["is_successful"]){
                return $this->sendResponse([], ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR);
            }

            /*
            |--------------------------------------------------------------------------
            | fetch player 1
            |--------------------------------------------------------------------------
            */
            $fetch_player_1_response = $this->userService->findWhere(["player_id" => $find_game_response["player_1"]]);

            /*
            |--------------------------------------------------------------------------
            | check if request fails
            |--------------------------------------------------------------------------
            */
            if (!$fetch_player_1_response["is_successful"]) {
                return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
            }

            /*
            |--------------------------------------------------------------------------
            | check if user not found
            |--------------------------------------------------------------------------
            */
            if ($fetch_player_1_response["is_successful"] && !count($fetch_player_1_response["response"])) {
                return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
            }

            /*
            |--------------------------------------------------------------------------
            | fetch player 1 token
            |--------------------------------------------------------------------------
            */
            $player_1_token = $this->cacheService->findWhere("push_tokens:{$fetch_player_1_response["response"][0]["id"]}")["push_token"] ?? [];

            /*
            |--------------------------------------------------------------------------
            | if player 1 token not found
            |--------------------------------------------------------------------------
            */
            if (count($player_1_token["response"]) < 1) {
                return $this->sendResponse([], ResponseCodeEnums::PUSH_TOKEN_NOT_FOUND);
            }

            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $player_1_token = $player_1_token["response"];

            /*
            |--------------------------------------------------------------------------
            | fetch player 2
            |--------------------------------------------------------------------------
            */
            $fetch_player_2_response = $this->userService->findWhere(["player_id" => $find_game_response["player_2"]]);

            /*
            |--------------------------------------------------------------------------
            | check if request fails
            |--------------------------------------------------------------------------
            */
            if (!$fetch_player_2_response["is_successful"]) {
                return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
            }

            /*
            |--------------------------------------------------------------------------
            | check if user not found
            |--------------------------------------------------------------------------
            */
            if ($fetch_player_2_response["is_successful"] && !count($fetch_player_2_response["response"])) {
                return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
            }

            /*
            |--------------------------------------------------------------------------
            | find player 2 token
            |--------------------------------------------------------------------------
            */
            $player_2_token = $this->cacheService->findWhere("push_tokens:{$fetch_player_2_response["response"][0]["id"]}")["push_token"] ?? [];

            /*
            |--------------------------------------------------------------------------
            | if player 2 token not found
            |--------------------------------------------------------------------------
            */
            if (count($player_2_token["response"]) < 1) {
                return $this->sendResponse([], ResponseCodeEnums::PUSH_TOKEN_NOT_FOUND);
            }

            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $player_2_token = $player_2_token["response"];


            /*
            |--------------------------------------------------------------------------
            | set payload
            |--------------------------------------------------------------------------
            */
            $push_tokens = [$player_1_token, $player_2_token];

            /*
            |--------------------------------------------------------------------------
            | Send game result to both players
            |--------------------------------------------------------------------------
            */
            CalculateGameResultJob::dispatch(session_id: $request->session_id, token: $push_tokens);
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::GAME_REQUEST_SUCCESSFUL);
    }
}
