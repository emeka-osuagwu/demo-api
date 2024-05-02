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
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TeamService;
use App\Services\CacheService;
use App\Services\TeamPlayerService;

class GetAllPlayersRequests extends Controller
{  use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TeamService $teamService,
        protected CacheService $cacheService,
        protected TeamPlayerService $teamPlayerService
    ){}

    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | find team player
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $this->teamPlayerService->findWhere(["player_id" => $request->auth_user["payload"]["player_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$find_team_player_admin_response["is_successful"] || count($find_team_player_admin_response["response"]) < 1){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
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
        | find the team
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["id" => $find_team_player_admin_response["team_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_team_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if team is not found
        |--------------------------------------------------------------------------
        */
        if(!count($find_team_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | check cache for request to be part of your team
        |--------------------------------------------------------------------------
        */
        $players_invitations = $this->cacheService->getAll("players_invitation_requests:{$find_team_response["response"][0]["id"]}:*");

        /*
        |--------------------------------------------------------------------------
        | check if the player invitation fails
        |--------------------------------------------------------------------------
        */
        if(!$find_team_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if the player invitation is empty
        |--------------------------------------------------------------------------
        */
        if(!count($players_invitations["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse($players_invitations, ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL);
    }
}
