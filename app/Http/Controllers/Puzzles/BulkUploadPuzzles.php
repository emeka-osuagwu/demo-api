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
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\LevelService;
use App\Services\PuzzlesService;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Resource Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\PuzzleResource;

class BulkUploadPuzzles extends Controller
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
        protected PuzzlesService $puzzlesService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validations
    |--------------------------------------------------------------------------
    */
    public function requestValidation(array $data)
    {
       return Validator::make($data, [
           'puzzle_data'=> 'array|min:1',
           'puzzle_data.*.word' => 'string|required',
           'puzzle_data.*.level_id' => 'string|required',
           'puzzle_data.*.description' => 'string|required',
           'puzzle_data.*.level_number' => 'string|required',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Upload Dictionary Entries
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $request_payload = [
            'puzzle_data' => $request->puzzle_data
        ];

        /*
        |--------------------------------------------------------------------------
        | validate request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request_payload);
        
        /*
        |--------------------------------------------------------------------------
        | check if validation failed
        |--------------------------------------------------------------------------
        */
        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::PUZZLE_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | convert any numeric or float value to string
        |--------------------------------------------------------------------------
        */
        foreach ($request_payload["puzzle_data"] as &$entry) {
            foreach ($entry as $key => $value) {
                if (is_int($value) || is_float($value)) {
                    $entry[$key] = (string)$value;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $puzzle = [];

        /*
        |--------------------------------------------------------------------------
        | update entries payload and bulk upload entries
        |--------------------------------------------------------------------------
        */
        foreach ($request_payload["puzzle_data"] as &$entry) {
            /*
            |--------------------------------------------------------------------------
            | check if the level id exist in the database
            |--------------------------------------------------------------------------
            */
            $find_level_response = $this->levelService->findWhere(["id" => $entry["level_id"]]);

            /*
            |--------------------------------------------------------------------------
            | check if the request fails
            |--------------------------------------------------------------------------
            */
            if(!$find_level_response["is_successful"]){
                report($this->sendResponse([], ResponseCodeEnums::LEVEL_REQUEST_ERROR));
            }

            /*
            |--------------------------------------------------------------------------
            | check if level not found
            |--------------------------------------------------------------------------
            */
            if($find_level_response["is_successful"] && !count($find_level_response["response"])){
                report($this->sendResponse([], ResponseCodeEnums::LEVEL_NOT_FOUND));
            }

            /*
            |--------------------------------------------------------------------------
            | save puzzle if level exist
            |--------------------------------------------------------------------------
            */
            if($find_level_response["is_successful"] && count($find_level_response["response"]) > 0){
                /*
                |--------------------------------------------------------------------------
                | set puzzle variables
                |--------------------------------------------------------------------------
                */
                $entry['id'] = generateUUID();
                $entry["status"] = "active";
                $entry['created_at'] = now()->toDateTimeString();
                $entry['updated_at'] = now()->toDateTimeString();

                /*
                |--------------------------------------------------------------------------
                | create puzzle entry
                |--------------------------------------------------------------------------
                */
                $create_puzzle = $this->puzzlesService->create($entry);

                /*
                |--------------------------------------------------------------------------
                | check if puzzle service validation error
                |--------------------------------------------------------------------------
                */
                if ($create_puzzle["status"] == ServiceResponseMessageEnum::VALIDATION_ERROR->value && !$create_puzzle["is_successful"]) {
                    report($this->sendResponse([], ResponseCodeEnums::PUZZLE_SERVICE_VALIDATION_ERROR));
                }

                /*
                |--------------------------------------------------------------------------
                | check if puzzle service request fails
                |--------------------------------------------------------------------------
                */
                if (!$create_puzzle["is_successful"]) {
                    report($this->sendResponse([], ResponseCodeEnums::PUZZLE_SERVICE_REQUEST_ERROR));
                }

                /*
                |--------------------------------------------------------------------------
                | update the puzzle data
                |--------------------------------------------------------------------------
                */
                $puzzle[] = $create_puzzle["response"];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(PuzzleResource::collection($puzzle), ResponseCodeEnums::PUZZLE_REQUEST_SUCCESSFUL);
    }
}
