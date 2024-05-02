<?php

namespace App\Http\Controllers\Teams;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TeamService;

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

class GetAllTeams extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TeamService $teamService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | get all available teams
    |--------------------------------------------------------------------------
    */
    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | get all teams
        |--------------------------------------------------------------------------
        */
        $find_teams_response = $this->teamService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$find_teams_response["is_successful"] || !count($find_teams_response["response"])){
            return $this->sendResponse([],ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(TeamResource::collection($find_teams_response["response"]), ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL);
    }
}
