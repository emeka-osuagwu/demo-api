<?php

namespace App\Http\Controllers\User;

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
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;
use App\Services\LevelService;
use App\Services\CacheService;
use App\Services\PuzzlesService;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\PuzzleResource;

class UserPuzzles extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected UserService $userService,
        protected LevelService $levelService,
        protected CacheService $cacheService,
        protected PuzzlesService $puzzlesService,
    ) {
    }

    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | check if user exists
        |--------------------------------------------------------------------------
        */
        $find_user_response = $this->userService->findWhere(["id" => request()->auth_user["payload"]["id"]]);

        /*
        |--------------------------------------------------------------------------
        | if user request fails
        |--------------------------------------------------------------------------
        */
        if (!$find_user_response["is_successful"] || !count($find_user_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::USER_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set puzzle variable
        |--------------------------------------------------------------------------
        */
        $completed_puzzles = $find_user_response["response"][0]["completed_puzzles"];

        /*
        |--------------------------------------------------------------------------
        | get completed puzzle ids
        |--------------------------------------------------------------------------
        */
        $completed_puzzles = explode(",", $completed_puzzles);

        /*
        |--------------------------------------------------------------------------
        | fetch puzzle from cache
        |--------------------------------------------------------------------------
        */
        $puzzle_response = $this->cacheService->findWhere('puzzles');
        $uncompleted_puzzles = [];

        /*
        |--------------------------------------------------------------------------
        | filter and update completed puzzle
        |--------------------------------------------------------------------------
        */
        foreach ($puzzle_response['response'] as &$level) {

            /*
            |--------------------------------------------------------------------------
            | strip data from level response
            |--------------------------------------------------------------------------
            */
            unset($level["created_at"], $level["updated_at"]);
            
            foreach ($level["puzzles"] as &$puzzles) {
                foreach ($puzzles as &$puzzle) {

                    /*
                    |--------------------------------------------------------------------------
                    | strip data from level response
                    |--------------------------------------------------------------------------
                    */
                    unset( $puzzle["created_at"],
                    $puzzle["updated_at"],
                    $puzzle["category_id"],
                    $puzzle["status"],
                    $puzzle["puzzle_level"],
                    $puzzle["level_id"],
                    $puzzle["level_number"],
                    $puzzle["puzzle_sub_level"]);

                    $puzzle["completed"] = false;
                    if (in_array($puzzle["id"], $completed_puzzles)) {
                        $puzzle["completed"] = true;
                    } else {
                        if (count($uncompleted_puzzles) < 30) {
                            $word = strtolower($puzzle['word']);
                            $uncompleted_puzzles[$word] = [
                                'id' => $puzzle['id'],
                                'word' => $word,
                                'description' => $puzzle['description']
                            ];
                        }
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([
            'levels' => $puzzle_response['response'],
            'puzzles' => $uncompleted_puzzles,
        ], ResponseCodeEnums::PUZZLE_REQUEST_SUCCESSFUL);
    }
}
