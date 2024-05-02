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
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;
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
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use app\Contracts\PushNotificationContract;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;

class InvitePlayer extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected UserService $userService,
        protected TeamService $teamService,
        protected CacheService $cacheService,
        protected TeamPlayerService $teamPlayerService,
        // public PushNotificationContract $pushNotificationContract
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "team_name" => "string|required",
            'player_id' => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Team Admin Sending Team Invitation Request To User
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate requests
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if($validator->fails()){
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find the team by the name
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["team_name" => $request->team_name]);

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
        | find where team player is auth user and has admin permission
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $this->teamPlayerService->findWhere([
            "player_id" => $request->auth_user["payload"]["player_id"],
            "is_admin" => "1"
        ]);

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
        | check if not user is found
        |--------------------------------------------------------------------------
        */
        if(!count($find_team_player_admin_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $find_team_player_admin_response = $find_team_player_admin_response["response"][0];

        /*
        |--------------------------------------------------------------------------
        | check if team player is auth user
        |--------------------------------------------------------------------------
        */
        if($request->auth_user["payload"]["player_id"] === $request->player_id){
            return $this->sendResponse([], ResponseCodeEnums::USER_CANT_INVITE_SELF_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if incoming request player_id belongs to a user
        |--------------------------------------------------------------------------
        */
        $find_user_response = $this->userService->findWhere(["player_id" => $request->player_id]);

        /*
        |--------------------------------------------------------------------------
        | check if service request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_user_response["is_successful"] || !count($find_user_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        |  check if user with the request player_id belongs to a team
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
        if(!$find_team_player_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | if player_id is already in a team
        |--------------------------------------------------------------------------
        */
        if(count($find_team_player_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::USER_IS_ALREADY_IN_A_TEAM);
        }

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $create_team_player_payload = [
            "team_id" => $find_team_player_admin_response["team_id"],
            "is_admin" => false,
            "player_id" => $request->player_id,
        ];

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $selector = "team_invitations:{$find_user_response["response"][0]["id"]}";

        /*
        |--------------------------------------------------------------------------
        | save record on redis
        |--------------------------------------------------------------------------
        */
        $save_team_invitation_response = $this->cacheService->saveRecord($selector, $create_team_player_payload);

        /*
        |--------------------------------------------------------------------------
        | check if team invitation saved returns an error
        |--------------------------------------------------------------------------
        */
        if(!$save_team_invitation_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR);
        };

        /*
        |--------------------------------------------------------------------------
        | set push token payload
        |--------------------------------------------------------------------------
        */
        // $push_token = $this->cacheService->findWhere("push_tokens:{$find_user_response["response"][0]["id"]}")["push_token"];

        /*
        |--------------------------------------------------------------------------
        | send push notification to player 2
        |--------------------------------------------------------------------------
        */
        // $notification_remark = "Team invitation made at time -> " . now()->format('Y-m-d H:i:s');
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
