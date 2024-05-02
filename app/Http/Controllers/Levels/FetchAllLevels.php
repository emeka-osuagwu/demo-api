<?php

namespace App\Http\Controllers\Levels;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
| Exceptions Namespace
|--------------------------------------------------------------------------
*/
use App\Exceptions\AppException;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\LevelService;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\LevelResource;


class FetchAllLevels extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected LevelService $levelService,
    ) {}

    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Fetch By Level
        |--------------------------------------------------------------------------
        */
        $fetch_level_response = $this->levelService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$fetch_level_response['is_successful']){
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_REQUEST_SUCCESSFUL);
        }

        /*
        |--------------------------------------------------------------------------
        | check if level not found
        |--------------------------------------------------------------------------
        */
        if($fetch_level_response["is_successful"] && $fetch_level_response["status"] == ServiceResponseMessageEnum::SUCCESSFUL && !count($fetch_level_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::LEVEL_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | send all transactions response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(LevelResource::collection($fetch_level_response["response"]), ResponseCodeEnums::LEVEL_REQUEST_SUCCESSFUL);
    }
}
