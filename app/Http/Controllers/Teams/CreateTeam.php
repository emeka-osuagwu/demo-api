<?php

namespace App\Http\Controllers\Teams;

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
use App\Services\TeamService;
use App\Services\UserService;
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
use App\Enums\ServiceResponseMessageEnum;

class CreateTeam extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependeny Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected TeamService $teamService,
        protected UserService $userService,
        protected TeamPlayerService $teamPlayerService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            'event_id' => "string|required",
            'team_name' => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Team
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | set request payload
        |--------------------------------------------------------------------------
        */
        $player_id = $request->auth_user["payload"]["player_id"];
        $request_payload = [
            "event_id" => $request->event_id,
            "team_name" => $request->team_name,
        ];

        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find where user is in a team
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
        | check if team player is already on the team
        |--------------------------------------------------------------------------
        */
        if(count($find_team_player_response["response"]) > 0){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_IS_ALREADY_ON_A_TEAM);
        }

        /*
        |--------------------------------------------------------------------------
        | set team payload
        |--------------------------------------------------------------------------
        */
        $create_team_payload = [
            'id' => generateUUID(),
            'event_id' => $request_payload["event_id"],
            'team_name' => $request_payload["team_name"],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        /*
        |--------------------------------------------------------------------------
        | create team
        |--------------------------------------------------------------------------
        */
        $create_team_response = $this->teamService->create($create_team_payload);

        /*
        |--------------------------------------------------------------------------
        | if service validation fails
        |--------------------------------------------------------------------------
        */
        if (!$create_team_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set team player payload
        |--------------------------------------------------------------------------
        */
        $create_team_admin_payload = [
            "id" => generateUUID(),
            "team_id" => $create_team_response["response"]["id"],
            "is_admin" => "1",
            "player_id" => $player_id,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        /*
        |--------------------------------------------------------------------------
        | save team admin
        |--------------------------------------------------------------------------
        */
        $team_admin_response = $this->teamPlayerService->create($create_team_admin_payload);

        /*
        |--------------------------------------------------------------------------
        | if service validation fails
        |--------------------------------------------------------------------------
        */
        if (!$team_admin_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set response payload
        |--------------------------------------------------------------------------
        */
        $team_response_payload = [
            "team" => $create_team_response["response"],
            "admin" => $team_admin_response["response"],
        ];

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse($team_response_payload, ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL);
    }
}
