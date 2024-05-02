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

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\TeamResource;

class GetTeamByName extends Controller
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
    ){}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data){
        return Validator::make($data, [
            'team_name' =>'string|required'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Team By Name
    |--------------------------------------------------------------------------
    */
    public function __invoke($team_name, Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate incoming request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(["team_name" => $team_name]);

        if($validator->fails()){
            return $this->sendResponse([], ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find team
        |--------------------------------------------------------------------------
        */
        $find_team_response = $this->teamService->findWhere(["team_name" => $team_name ]);

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
        | find all team players under the team id
        |--------------------------------------------------------------------------
        */
        $team = $find_team_response['response'][0];

        /*
        |--------------------------------------------------------------------------
        | find all team players under the team id
        |--------------------------------------------------------------------------
        */
        $find_team_players_response = $this->teamPlayerService->findWhere(['team_id' => $team['id']]);

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
        | set variables
        |--------------------------------------------------------------------------
        */
        $collection = collect($find_team_players_response["response"]);

        /*
        |--------------------------------------------------------------------------
        | check if authenticated user is a team player under the team of team id
        |--------------------------------------------------------------------------
        */
        $has_player_id = $collection->some(fn($item) => $item['player_id'] === $request->auth_user["payload"]["player_id"]);

        if (!$has_player_id) {
            return $this->sendResponse([], ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_PLAYER);
        }

        /*
        |--------------------------------------------------------------------------
        | find team player point
        |--------------------------------------------------------------------------
        */
        $find_team_players_points_response = $this->teamPlayerPointService->findWhere(["team_id" => $team['id']]);

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
