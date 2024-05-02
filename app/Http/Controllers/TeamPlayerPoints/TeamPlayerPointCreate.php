<?php

namespace App\Http\Controllers\TeamPlayerPoints;

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
use App\Services\TeamPlayerService;
use App\Services\TeamPlayerPointService;

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

class TeamPlayerPointCreate extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TeamService $teamService,
        protected TeamPlayerService $teamPlayerService,
        protected TeamPlayerPointService $teamPlayerPointService,
    ){}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data){
        return Validator::make($data, [
            "points" => "numeric|required",
            'team_id' => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Team Player Points
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if($validator->fails()){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_POINTS_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find team
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["id" => $request->team_id]);

        /*
        |--------------------------------------------------------------------------
        | check if the request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if request not found
        |--------------------------------------------------------------------------
        */
        if (!count($find_team_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch team players
        |--------------------------------------------------------------------------
        */
        $find_team_players_response = $this->teamPlayerService->findWhere([
            'team_id' => $request->team_id,
            'player_id' => $request->auth_user["payload"]["player_id"],
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_players_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if no team players were found
        |--------------------------------------------------------------------------
        */
        if (!count($find_team_players_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $create_team_player_points_payload = [
            "id" => generateUUID(),
            "points" => strval($request->points),
            "team_id" => $request->team_id,
            "player_id" => $request->auth_user["payload"]["player_id"],
            "created_at" => now()->toDateTimeString(),
            "updated_at" => now()->toDateTimeString()
        ];

        /*
        |--------------------------------------------------------------------------
        | save team player points
        |--------------------------------------------------------------------------
        */
        $create_team_player_points_response = $this->teamPlayerPointService->create($create_team_player_points_payload);

        /*
        |--------------------------------------------------------------------------
        | if service validation fails
        |--------------------------------------------------------------------------
        */
        if (!$create_team_player_points_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_POINTS_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_POINTS_REQUEST_SUCCESSFUL);
    }
}
