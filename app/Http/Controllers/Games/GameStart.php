<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
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
use App\Services\UserService;
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
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\PushNotificationContract;
/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\GameResource;

class GameStart extends Controller
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
        protected PuzzlesService $puzzlesService,
        public PushNotificationContract $pushNotificationContract
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "player_id" => "string|required",
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
        | check if user has totem
        |--------------------------------------------------------------------------
        */
        if($request->auth_user["payload"]["totem"] == "null" || $request->auth_user["payload"]["totem"] == 0){
            return $this->sendResponse([], ResponseCodeEnums::GAME_TOTEM_EXHAUSTED);
        }

        /*
        |--------------------------------------------------------------------------
        | make sure player cant invite him self
        |--------------------------------------------------------------------------
        */
        if($request->auth_user["payload"]["player_id"] == $request->player_id){
            return $this->sendResponse([], ResponseCodeEnums::USER_CANT_INVITE_SELF_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find player
        |--------------------------------------------------------------------------
        */
        $find_player_response = $this->userService->findWhere(["player_id" => $request->player_id]);

        /*
        |--------------------------------------------------------------------------
        | check if player request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if player not found
        |--------------------------------------------------------------------------
        */
        if(!count($find_player_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch all player game sessions with auth_user and player 2
        |--------------------------------------------------------------------------
        */
        $selector = "game_sessions:{$request->auth_user["payload"]["player_id"]}:{$request->player_id}:*";
        $game_sessions = $this->cacheService->getAll($selector);

        /*
        |--------------------------------------------------------------------------
        | check if game session is active
        |--------------------------------------------------------------------------
        */
        // Neeed FIx -> refactor the code to stop using [0]
        if(count($game_sessions['response'])){
            foreach ($game_sessions['response'] as $session) {
                if(!$session["completed"] && $session["challenge_accepted"]){
                    return $this->sendResponse([],ResponseCodeEnums::ACTIVE_GAME_INVITE_ALREADY_EXISTS);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | fetch all player game sessions with player 2 and auth_user
        |--------------------------------------------------------------------------
        */
        $selector = "game_sessions:{$request->player_id}:{$request->auth_user["payload"]["player_id"]}:*";
        $game_sessions = $this->cacheService->getAll($selector);

        /*
        |--------------------------------------------------------------------------
        | check if game session is active
        |--------------------------------------------------------------------------
        */
        if(count($game_sessions['response'])){
            foreach ($game_sessions as $session) {
                if(!$session["completed"] && $session["challenge_accepted"]){
                    return $this->sendResponse([],ResponseCodeEnums::ACTIVE_GAME_INVITE_ALREADY_EXISTS);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | fetch puzzles
        |--------------------------------------------------------------------------
        */
        $fetch_puzzles_response = $this->puzzlesService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if puzzle request fails
        |--------------------------------------------------------------------------
        */
        if (!$fetch_puzzles_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if puzzle request not found
        |--------------------------------------------------------------------------
        */
        if (!count($fetch_puzzles_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $easy = [];
        $hard = [];

        /*
        |--------------------------------------------------------------------------
        | group puzzle by level
        |--------------------------------------------------------------------------
        */
        foreach ($fetch_puzzles_response['response'] as $key => $val) {
            $strip_val = [
                "id" => $val["id"],
                "word" => $val["word"],
                "description" => $val["description"],
                "level_id" => $val["level_id"],
                "level_number" => $val["level_number"],
        ];
            if (intval($val["level_number"]) <= 5) {
                array_push($easy, $strip_val);
            } else {
                array_push($hard, $strip_val);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | create game payload
        |--------------------------------------------------------------------------
        */
        $create_game_payload = [
            "winner" => null,
            "puzzles" => combineArrays($easy, $hard),
            "player_1" => $request->auth_user["payload"]["player_id"],
            "player_2" => $request->player_id,
            "completed" => false,
            "game_time" => 90,
            "session_id" => generateUUID(),
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString(),
            "player_1_games" => [],
            "player_2_games" => [],
            "challenge_accepted" => false,
            "player_1_completed" => false,
            "player_2_completed" => false,
        ];

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $selector = "game_sessions:{$request->auth_user["payload"]["player_id"]}:{$request->player_id}:{$create_game_payload['session_id']}";

        /*
        |--------------------------------------------------------------------------
        | save game to redis
        |--------------------------------------------------------------------------
        */
        $save_game_response = $this->cacheService->saveRecord($selector, $create_game_payload);

        /*
        |--------------------------------------------------------------------------
        | check if game session is found
        |--------------------------------------------------------------------------
        */
        if (!$save_game_response['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $update_user_payload = ["totem" => strval((int) $request->auth_user["payload"]["totem"] - 1)];
        $update_user_selector = "id='{$request->auth_user["payload"]["id"]}'";
        /*
        |--------------------------------------------------------------------------
        | update/deduct user totem
        |--------------------------------------------------------------------------
        */
        $update_user_totem = $this->userService->update($update_user_selector, $update_user_payload);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$update_user_totem["is_successful"]){
            return $this->sendResponse([],ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $push_token = $this->cacheService->findWhere("push_tokens:{$find_player_response["response"][0]["id"]}")["push_token"] ?? null;

        /*
        |--------------------------------------------------------------------------
        | send push notification to player 2
        |--------------------------------------------------------------------------
        */
        // $notification_remark = "Schedule is working and payment made successfully at time -> " . now()->format('Y-m-d H:i:s');
        // $this->pushNotificationContract
        //     ->setType('In_app_purchase_notification')
        //     ->setBody($notification_remark)
        //     ->setIcon('stock_ticker_update')
        //     ->setTokens([$push_token])
        //     ->setTitle('System Check')
        //     ->setPayload([])
        //     ->sendNotification();

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(GameResource::make($create_game_payload), ResponseCodeEnums::GAME_REQUEST_SUCCESSFUL);
    }
}
