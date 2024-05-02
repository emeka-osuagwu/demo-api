<?php

namespace App\Http\Controllers\Puzzles;

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
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\LevelService;
use App\Services\PuzzlesService;

/*
|--------------------------------------------------------------------------
| Resource Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\PuzzleResource;

class PuzzleCreate extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected LevelService $levelService,
        protected PuzzlesService $puzzlesService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data){
        return Validator::make($data,[
            'word' => 'string|required',
            "level_id" => "string|required",
            'description' => 'string|required',
            'level_number' => 'string|required',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Store Single Puzzle Word
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | request validation
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::PUZZLE_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | find the level id
        |--------------------------------------------------------------------------
        */
        $find_level_response = $this->levelService->findWhere(["id" => $request->level_id]);

        /*
        |--------------------------------------------------------------------------
        | check if request fails
        |--------------------------------------------------------------------------
        */
        if(!$find_level_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::LEVEL_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if level not found
        |--------------------------------------------------------------------------
        */
        if(count($find_level_response["response"]) < 1){
            return $this->sendResponse([], ResponseCodeEnums::LEVEL_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $create_puzzle_payload =[
            'id' => generateUUID(),
            "status" => "active",
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
            ...$request->only(["word", "level_number", "level_id","description"])
        ];

        /*
        |--------------------------------------------------------------------------
        | create the puzzle on big query
        |--------------------------------------------------------------------------
        */
        $puzzle = $this->puzzlesService->create($create_puzzle_payload);

        /*
        |--------------------------------------------------------------------------
        | check service validation error
        |--------------------------------------------------------------------------
        */
        if($puzzle["status"] == ServiceResponseMessageEnum::VALIDATION_ERROR->value && !$puzzle["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_SERVICE_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | service request error
        |--------------------------------------------------------------------------
        */
        if (!$puzzle["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::PUZZLE_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(PuzzleResource::make($puzzle["response"]), ResponseCodeEnums::PUZZLE_REQUEST_SUCCESSFUL);
    }
}
