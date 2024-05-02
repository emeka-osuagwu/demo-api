<?php

namespace App\Http\Controllers\TeamPlayers;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/

use App\Contracts\PushNotificationContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TeamService;
use App\Services\TeamPlayerService;


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
use App\Services\CacheService;

/*
|--------------------------------------------------------------------------
| Remove Team Player
|--------------------------------------------------------------------------
*/
class RemoveTeamPlayer extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TeamService $teamService,
        protected CacheService $cacheService,
        protected TeamPlayerService $teamPlayerService,
        protected PushNotificationContract $pushNotificationContract
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "team_id" => "string|required",
            'player_id' => "string|required",
        ]);
    }

    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate payload
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find team
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["id" => $request->team_id]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if team not found
        |--------------------------------------------------------------------------
        */
        if (!count($find_team_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | verify if the authenticated user is a team player and has admin permission
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $this->teamPlayerService->findWhere([
            "is_admin" => "1",
            "team_id" => $find_team_response["response"][0]["id"], 
            "player_id" => $request->auth_user["payload"]["player_id"], 
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_player_admin_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if team player is not found
        |--------------------------------------------------------------------------
        */
        if (!count($find_team_player_admin_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN);
        }

        /*
        |--------------------------------------------------------------------------
        | check if player_id is same as authenticated user id
        |--------------------------------------------------------------------------
        */
        if ($request->player_id === $request->auth_user["payload"]["player_id"]) {
            return $this->sendResponse([], ResponseCodeEnums::UNABLE_TO_REMOVE_SELF_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $find_team_player_admin_response["response"][0];

        /*
        |--------------------------------------------------------------------------
        |  check if player to be removed is in the same team as the admin
        |--------------------------------------------------------------------------
        */
        $find_team_player_response = $this->teamPlayerService->findWhere([
            "player_id" => $request->player_id,
            "team_id" => $find_team_player_admin_response["team_id"], 
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_player_response["is_successful"]) {
            return $this->sendResponse([$find_team_player_response["response"]], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if auth user is not a team player
        |--------------------------------------------------------------------------
        */
        if (!count($find_team_player_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | remove team player
        |--------------------------------------------------------------------------
        */
        $remove_team_player_response = $this->teamPlayerService->deleteWhere("player_id='{$request->player_id}'");

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if (!$remove_team_player_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set push token payload
        |--------------------------------------------------------------------------
        */
        $push_token = $this->cacheService->findWhere("push_tokens:{$find_team_player_response["response"][0]["id"]}")["push_token"] ?? '';

        /*
        |--------------------------------------------------------------------------
        | send push notification to player 2
        |--------------------------------------------------------------------------
        */
        $notification_remark = "Player removed from team -> " . now()->format('Y-m-d H:i:s');
        $this->pushNotificationContract
            ->setType('In_app_purchase_notification')
            ->setBody($notification_remark)
            ->setIcon('stock_ticker_update')
            ->setTokens([$push_token])
            ->setTitle('System Check')
            ->setPayload([])
            ->sendNotification();

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL);
    }
}
