<?php

namespace App\Http\Controllers\Puzzles;

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
use App\Services\PuzzlesService;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeletePuzzle extends Controller
{
    use ResponseTrait;
    public function __construct(
        protected PuzzlesService $puzzlesService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            'id' => 'array|required|min:1',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Single Dictionary Entry
    |--------------------------------------------------------------------------
    */
    public function __invoke($id)
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $id = explode(",", $id);

        /*
        |--------------------------------------------------------------------------
        | request validation
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(['id' => $id]);

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::PUZZLE_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | delete entry
        |--------------------------------------------------------------------------
        */
        foreach ($id as $value) {
            $puzzle = $this->puzzlesService->delete("id='{$value}'");

            /*
            |--------------------------------------------------------------------------
            | check if delete puzzle service request error
            |--------------------------------------------------------------------------
            */
            if($puzzle["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$puzzle["is_successful"]){
                report($this->sendResponse([], ResponseCodeEnums::PUZZLE_REQUEST_ERROR));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse([], ResponseCodeEnums::PUZZLE_REQUEST_SUCCESSFUL);

    }
}
