<?php

namespace App\Http\Controllers\TeamPlayers;

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
/**
 *
 * @param url - The endpoint url to get the resources you need
 * @param params - This is to add query parameters to the url endpoint
 * @returns
 */
/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TeamService;
use App\Services\UserService;
use App\Services\CacheService;
use App\Services\TeamPlayerService;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\PushNotificationContract;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
| Need fix -> there is issue with this implementation
*/
class PlayerRequestToJoinTeam extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TeamService $teamService,
        protected UserService $userService,
        protected CacheService $cacheService,
        protected TeamPlayerService $teamPlayerService,
        protected PushNotificationContract $pushNotificationContract
    ){}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data){
        return Validator::make($data, [
            'id' =>'string|required'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Player Request To Join A Team
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(["id" => $request->id]);

        if($validator->fails()){
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find team by team_name
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["id" => $request->id]);

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
        | check if team is not found
        |--------------------------------------------------------------------------
        */
        if(!count($find_team_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_team_response = $find_team_response["response"][0];

        /*
        |--------------------------------------------------------------------------
        | find player to check if player already belong to the team
        |--------------------------------------------------------------------------
        */
        $find_team_player_response = $this->teamPlayerService->findWhere(["player_id" => $request->auth_user["payload"]["player_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$find_team_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if auth-user is already on another team
        |--------------------------------------------------------------------------
        */
        if(count($find_team_player_response["response"])){
            return $this->sendResponse([],ResponseCodeEnums::TEAM_PLAYER_IS_ALREADY_ON_A_TEAM);
        }

        /*
        |--------------------------------------------------------------------------
        | find team admin
        |--------------------------------------------------------------------------
        */
        $find_team_admin_response = $this->teamPlayerService->findWhere(["team_id" => $find_team_response["id"], "is_admin" => "1"]);

        /*
        |--------------------------------------------------------------------------
        | check if the request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_team_admin_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        };

        /*
        |--------------------------------------------------------------------------
        | check if request not found
        |--------------------------------------------------------------------------
        */
        if(!count($find_team_admin_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $create_team_player_payload = [
            "team_id" => $find_team_response["id"],
            "is_admin" => false,
            "player_id" => $request->auth_user["payload"]["player_id"],
            "invite_accepted" => false
        ];

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $selector = "players_invitation_requests:{$find_team_response["id"]}:{$request->auth_user["payload"]["player_id"]}";

        /*
        |--------------------------------------------------------------------------
        | cache the auth-user request data to join a team
        |--------------------------------------------------------------------------
        */
        $save_player_invite_response = $this->cacheService->saveRecord($selector, $create_team_player_payload);

        if(!$save_player_invite_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find team admin userdata
        |--------------------------------------------------------------------------
        */
        $find_admin_user_response = $this->userService->findWhere(["player_id" => $find_team_admin_response["response"][0]["player_id"]]);

        /*
        |--------------------------------------------------------------------------
        | check if the request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_admin_user_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        };

        /*
        |--------------------------------------------------------------------------
        | check if request not found
        |--------------------------------------------------------------------------
        */
        if(!count($find_admin_user_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $push_token = $this->cacheService->findWhere("push_tokens:{$find_admin_user_response["response"][0]["id"]}");

        /*
        |--------------------------------------------------------------------------
        | send push notification to admin
        |--------------------------------------------------------------------------
        */
        // $notification_remark = "request sent at time -> " . now()->format('Y-m-d H:i:s');
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
        return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL);
    }
}
