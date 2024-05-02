<?php

namespace App\Http\Controllers\TeamPlayers;

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


class AcceptPlayer extends Controller
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
        protected TeamPlayerService $teamPlayerService
    ){}


    /*
    |--------------------------------------------------------------------------
    | Player Accepts Team Invitation Request
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | find where authenticaed user is a team player
        |--------------------------------------------------------------------------
        */
        $find_team_player_response = $this->teamPlayerService->findWhere(["player_id" => $request->auth_user["payload"]["player_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_player_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if auth user is already a team player
        |--------------------------------------------------------------------------
        */
        if(count($find_team_player_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::USER_IS_ALREADY_IN_A_TEAM);
        }

        /*
        |--------------------------------------------------------------------------
        | find a cached team invitation
        |--------------------------------------------------------------------------
        */
        $invitation = $this->cacheService->findWhere("team_invitations:{$request->auth_user["payload"]["id"]}");

        /*
        |--------------------------------------------------------------------------
        | check if no team invitation
        |--------------------------------------------------------------------------
        */
        if(!count($invitation["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_INVITATION_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | check if invitation player_id is same as auth user
        |--------------------------------------------------------------------------
        */
        if($invitation["response"]["player_id"] !== $request->auth_user["payload"]["player_id"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_INVITATION_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | find team
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["id" => $invitation["response"]["team_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if the request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_team_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        };

        /*
        |--------------------------------------------------------------------------
        | check if request not found
        |--------------------------------------------------------------------------
        */
        if(!count($find_team_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set payoad
        |--------------------------------------------------------------------------
        */
        $create_team_player_payload = [
            "id" => generateUUID(),
            "team_id" => $invitation["response"]["team_id"],
            "is_admin" => (string) $invitation["response"]["is_admin"],
            "player_id" => $invitation["response"]["player_id"],
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString()
        ];

        /*
        |--------------------------------------------------------------------------
        | create team player
        |--------------------------------------------------------------------------
        */
        $create_team_player_response = $this->teamPlayerService->create($create_team_player_payload);

        /*
        |--------------------------------------------------------------------------
        | check if service request error
        |--------------------------------------------------------------------------
        */
        if(!$create_team_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | clear cache
        |--------------------------------------------------------------------------
        */
        $delete_invitation_response = $this->cacheService->deleteWhere("team_invitations:{$request->auth_user["payload"]["id"]}");

        /*
        |--------------------------------------------------------------------------
        | check if request fails
        |--------------------------------------------------------------------------
        */
        if(!$delete_invitation_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR);
        }
        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL);
    }
}
