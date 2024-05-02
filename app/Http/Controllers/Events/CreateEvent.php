<?php

namespace App\Http\Controllers\Events;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Service Namespace
|--------------------------------------------------------------------------
*/
use App\Services\EventService;

/*
|--------------------------------------------------------------------------
| Traits Namespace
|--------------------------------------------------------------------------
*/
use App\Traits\ResponseTrait;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;

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
use App\Http\Resources\EventResource;

class CreateEvent extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected EventService $eventService,
    ){}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            "title" => "string|required",
            "description" => "string|required",
        ]);
    }

    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | validate request
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation($request->all());

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::GAME_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $create_event_payload = [
            'id' => generateUUID(),
            'title' => $request->title,
            'status' => 'pending',
            'updated_at' => now()->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
            'description' => $request->description,
        ];

        /*
        |--------------------------------------------------------------------------
        | create events
        |--------------------------------------------------------------------------
        */
        $create_event_response = $this->eventService->create($create_event_payload);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$create_event_response['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::EVENT_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(EventResource::make($create_event_response["response"]), ResponseCodeEnums::EVENT_REQUEST_SUCCESSFUL);

    }
}
