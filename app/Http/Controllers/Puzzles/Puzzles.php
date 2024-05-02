<?php

namespace App\Http\Controllers\Puzzles;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\CacheService;
use App\Services\LevelService;
use App\Services\PuzzlesService;

class Puzzles extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected CacheService $cacheService,
        protected LevelService $levelService,
        protected PuzzlesService $puzzlesService
    ){}

    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | Fetch By Level
        |--------------------------------------------------------------------------
        */
        $fetch_level_response = $this->levelService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if level not found
        |--------------------------------------------------------------------------
        */
        if(!$fetch_level_response["is_successful"] || !count($fetch_level_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::LEVEL_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch puzzle
        |--------------------------------------------------------------------------
        */
        $fetch_puzzle_response = $this->puzzlesService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$fetch_puzzle_response['is_successful']){
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if no puzzle found
        |--------------------------------------------------------------------------
        */
        if (count($fetch_puzzle_response["response"]) < 1) {
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $puzzle_response = [];

        /*
        |--------------------------------------------------------------------------
        | group and filter puzzles
        |--------------------------------------------------------------------------
        */
        $groupedPuzzles = collect($fetch_puzzle_response['response'])
        ->map(function ($puzzle) {
            return $puzzle;
        })
        ->groupBy(['level_id', 'level_number'])
        ->toArray();

        /*
        |--------------------------------------------------------------------------
        | group puzzle by level
        |--------------------------------------------------------------------------
        */
        foreach ($fetch_level_response['response'] as $key => $level) {
            /*
            |--------------------------------------------------------------------------
            | check if level id is present
            |--------------------------------------------------------------------------
            */
            if(isset($groupedPuzzles[$level['id']])){
                $level['puzzles'] = $groupedPuzzles[$level['id']];
                array_push($puzzle_response, $level);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | update cache
        |--------------------------------------------------------------------------
        */
        $save_puzzles_response = $this->cacheService->saveRecord('puzzles', $puzzle_response);

        /*
        |--------------------------------------------------------------------------
        | check if products have been saved
        |--------------------------------------------------------------------------
        */
        if(!$save_puzzles_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse($puzzle_response, ResponseCodeEnums::PUZZLE_REQUEST_SUCCESSFUL);
    }
}
