<?php

namespace App\Http\Controllers\Events;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Service Namespace
|--------------------------------------------------------------------------
*/
use App\Services\GameService;
use App\Services\UserService;

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
use App\Http\Resources\SoloLeaderBoardResource;

class SoloLeaderBoard extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected UserService $userService,
        protected GameService $gameService,
    ){}

    /*
    |--------------------------------------------------------------------------
    | Resources Namespace
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | fetch players
        |--------------------------------------------------------------------------
        */
        $users = $this->userService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if (!$users['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if game not found
        |--------------------------------------------------------------------------
        */
        if (count($users["response"]) < 1) {
            return $this->sendResponse([], ResponseCodeEnums::GAME_NOT_FOUND);
        }
        
        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $users = $users['response'];

        /*
        |--------------------------------------------------------------------------
        | set response
        |--------------------------------------------------------------------------
        */
        $sorted_leaderboard = sortLeaderBoardByScore($users->toArray());

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(SoloLeaderBoardResource::collection($sorted_leaderboard), ResponseCodeEnums::LEADERBOARD_REQUEST_SUCCESSFUL);
    }
}
