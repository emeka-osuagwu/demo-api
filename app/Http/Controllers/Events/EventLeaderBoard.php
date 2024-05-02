<?php

namespace App\Http\Controllers\Events;

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
| Service Namespace
|--------------------------------------------------------------------------
*/
use App\Services\GameService;
use App\Services\UserService;
use App\Services\TeamService;
use App\Services\EventService;
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

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\EventLeaderBoardResource;

class EventLeaderBoard extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected GameService $gameService,
        protected UserService $userService,
        protected TeamService $teamService,
        protected EventService $eventService,
        protected TeamPlayerService $teamPlayerService,
    ){}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            'event_id' => "string|required",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Event Leaderboard
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | fetch games
        |--------------------------------------------------------------------------
        */
        $games = $this->gameService->findWhere(['event_id' => $request->event_id]);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if (!$games['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if game not found
        |--------------------------------------------------------------------------
        */
        if (empty($games["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }
        
        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $game = $games['response'];
        $team_leaderboard = [];

        /*
        |--------------------------------------------------------------------------
        | find the winner for each game
        |--------------------------------------------------------------------------
        */
        $teams = $this->teamService->findWhere(['event_id' => $game['event_id']]);
        
        /*
        |--------------------------------------------------------------------------
        | find teams on each event
        |--------------------------------------------------------------------------
        */
        foreach ($teams['response'] as $team) {
            /*
            |--------------------------------------------------------------------------
            | set variables
            |--------------------------------------------------------------------------
            */
            $team_total_score = 0;
            $team_highest_score = 0;

            /*
            |--------------------------------------------------------------------------
            | fetch team players
            |--------------------------------------------------------------------------
            */
            $team_players = $this->teamPlayerService->findWhere(['team_id' => $team['id']]);

            /*
            |--------------------------------------------------------------------------
            | find the winner for each game
            |--------------------------------------------------------------------------
            */
            foreach ($team_players['response'] as $team_player) {
                /*
                |--------------------------------------------------------------------------
                | find user
                |--------------------------------------------------------------------------
                */
                $find_user_response = $this->userService->findWhere(["player_id" => $team_player['player_id']]);

                /*
                |--------------------------------------------------------------------------
                | check if user request fails
                |--------------------------------------------------------------------------
                */
                if(!$find_user_response["is_successful"] || !count($find_user_response["response"])){
                    continue;
                }
                
                /*
                |--------------------------------------------------------------------------
                | set variable
                |--------------------------------------------------------------------------
                */
                $find_user_response = $find_user_response['response'][0];

                /*
                |--------------------------------------------------------------------------
                | set team score aggregrate
                |--------------------------------------------------------------------------
                */
                $team_total_score += (int) $find_user_response['score'];
                $team_highest_score += (int) $find_user_response['points'];
            }

            /*
            |--------------------------------------------------------------------------
            | teams with score
            |--------------------------------------------------------------------------
            */
            $team_leaderboard[] = [
                "score" => $team_total_score,
                "team_id" => $team['id'],
                "team_name" => $team['team_name'],
                "highest_score" => $team_highest_score,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | set response
        |--------------------------------------------------------------------------
        */
        $sorted_leaderboard = sortLeaderBoardByScore($team_leaderboard);

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(EventLeaderBoardResource::collection($sorted_leaderboard), ResponseCodeEnums::LEADERBOARD_REQUEST_SUCCESSFUL);
    }

}
