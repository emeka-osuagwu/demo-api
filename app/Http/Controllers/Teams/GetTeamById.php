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
use App\Services\TeamPlayerService;
use App\Services\TeamPlayerPointService;


class GetTeamById extends Controller
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
        protected TeamPlayerPointService $teamPlayerPointService
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
            'id' => 'string|required'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Team By ID
    |--------------------------------------------------------------------------
    */
    public function __invoke($team_id, Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(["id" => $team_id]);

        if ($validator->fails()) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find team
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["id" => $team_id]);

        /*
        |--------------------------------------------------------------------------
        | check if the request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_response["is_successful"] || !count($find_team_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find all team players under the team id
        |--------------------------------------------------------------------------
        */
        $find_team_players_response = $this->teamPlayerService->findWhere(['team_id' => $team_id]);

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
        | find team player point
        |--------------------------------------------------------------------------
        */
        $find_team_players_points_response = $this->teamPlayerPointService->findWhere(["team_id" => $team_id]);

        /*
        |--------------------------------------------------------------------------
        | check if request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_team_players_points_response["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TEAM_PLAYER_POINTS_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $response_payload = [
            "team" => $find_team_response["response"][0],
            "team_players" => []
        ];

        /*
        |--------------------------------------------------------------------------
        | group team player points to their respective player id
        |--------------------------------------------------------------------------
        */
        $team_player_point_collection = collect($find_team_players_points_response["response"])->groupBy(["player_id"])->toArray();

        /*
        |--------------------------------------------------------------------------
        | group points according to the players
        |--------------------------------------------------------------------------
        */
        foreach ($find_team_players_response["response"] as $key => $player) {
            if(isset($team_player_point_collection[$player["player_id"]])){
                $player["points"] = $team_player_point_collection[$player["player_id"]];
                array_push($response_payload["team_players"], $player);
            }
            else{
                array_push($response_payload["team_players"], $player);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse($response_payload, ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL);
    }
}
