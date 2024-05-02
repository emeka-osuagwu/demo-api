<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TeamService;
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
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\TeamResource;

class FindTeam extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TeamService $teamService,
        protected UserService $userService
    ) {}

    public function __invoke(Request $request)
    {



        /*
        |--------------------------------------------------------------------------
        | get all teams
        |--------------------------------------------------------------------------
        */
        $find_teams_response = $this->teamService->findWhere();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$find_teams_response["is_successful"]){
            return $this->sendResponse([],ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if no team is found
        |--------------------------------------------------------------------------
        */
        if($find_teams_response["is_successful"] && $find_teams_response["status"] == ServiceResponseMessageEnum::SUCCESSFUL && !count($find_teams_response)){
            return $this->sendResponse([],ResponseCodeEnums::TEAM_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | get all users
        |--------------------------------------------------------------------------
        */
        return $find_user_response = $this->userService->findWhereOr(['id' => 'c6ea5340-64ac-49db-901f-b28f25918c7f']);

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(TeamResource::collection($find_teams_response["response"]), ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL);
    }
}
