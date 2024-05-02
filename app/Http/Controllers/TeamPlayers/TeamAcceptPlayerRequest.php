<?php

namespace App\Http\Controllers\TeamPlayers;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TeamService;
use App\Services\CacheService;
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

class TeamAcceptPlayerRequest extends Controller
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
    ) {}
    public function __invoke($player_id, Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | find where team player is auth user
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $this->teamPlayerService->findWhere(["player_id" => $request->auth_user["payload"]["player_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_team_player_admin_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if auth user is not a team player
        |--------------------------------------------------------------------------
        */
        if(!count($find_team_player_admin_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_PLAYER);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $find_team_player_admin_response["response"][0];

        /*
        |--------------------------------------------------------------------------
        | check if auth user is not an admin
        |--------------------------------------------------------------------------
        */
        if(!boolval((int) $find_team_player_admin_response["is_admin"])){
            return $this->sendResponse([], ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN);
        }

        /*
        |--------------------------------------------------------------------------
        | find team player with player_id
        |--------------------------------------------------------------------------
        */
        $find_team_player_response = $this->teamPlayerService->findWhere(["player_id" => $player_id]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_team_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if player_id is already on a team
        |--------------------------------------------------------------------------
        */
        if(count($find_team_player_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_IS_ALREADY_ON_A_TEAM);
        }

        /*
        |--------------------------------------------------------------------------
        | find player request by team id and player id
        |--------------------------------------------------------------------------
        */
        $selector = "players_invitation_requests:{$find_team_player_admin_response["team_id"]}:{$player_id}";
        $fetch_player_response = $this->cacheService->findWhere($selector);

        /*
        |--------------------------------------------------------------------------
        | check if the request failed
        |--------------------------------------------------------------------------
        */
        if(!$fetch_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        };

        /*
        |--------------------------------------------------------------------------
        | check if team player in the cache is not found
        |--------------------------------------------------------------------------
        */
        if(!count($fetch_player_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $fetch_player_response = $fetch_player_response["response"];

        /*
        |--------------------------------------------------------------------------
        | check if the invitation has been accepted
        |--------------------------------------------------------------------------
        */
        if($fetch_player_response["invite_accepted"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_ALREADY_ACCEPTED);
        }

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $create_team_player_payload = [
            "id" => generateUUID(),
            "team_id" => $find_team_player_admin_response["team_id"],
            "is_admin" => strval($fetch_player_response["is_admin"]),
            "player_id" => "oa84LKyvTT",
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString()
        ];

        /*
        |--------------------------------------------------------------------------
        | save player request to team player table in the database
        |--------------------------------------------------------------------------
        */
        $create_team_player_response = $this->teamPlayerService->create($create_team_player_payload);

        /*
        |--------------------------------------------------------------------------
        | check if service validation error occurred
        |--------------------------------------------------------------------------
        */
        if(!$create_team_player_response["is_successful"]){
            return $this->sendResponse([],ResponseCodeEnums::TEAM_PLAYER_SERVICE_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if(!$create_team_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | update accept invite in the player cache to true
        |--------------------------------------------------------------------------
        */
        $cache_invitation_requests = "players_invitation_requests:{$find_team_player_admin_response["team_id"]}:{$player_id}";
        $update_invitation_response = $this->cacheService->updateWhere($cache_invitation_requests, ["invite_accepted" => true]);

        /*
        |--------------------------------------------------------------------------
        | check if the service request is successful
        |--------------------------------------------------------------------------
        */
        if(!$update_invitation_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL);
    }

}
