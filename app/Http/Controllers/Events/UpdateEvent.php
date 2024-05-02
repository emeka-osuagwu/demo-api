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
use App\Http\Resources\EventResource;

class UpdateEvent extends Controller
{
    use ResponseTrait;

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
            "status" => "required_if:section,status|string|in:pending,active,closed",
            "event_id" => "string|required",
            "description" => "required_if:section,description|string",
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
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::EVENT_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $update_event_payload = [
            'status' => $request->status,
            'updated_at' => now()->toDateTimeString(),
            'description' => $request->description,
        ];

        /*
        |--------------------------------------------------------------------------
        | filter incoming requests
        |--------------------------------------------------------------------------
        */
        foreach ($update_event_payload as $key => $value) {
            if (empty($value)) {
                unset($update_event_payload[$key]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | if the request is empty
        |--------------------------------------------------------------------------
        */
        if (count($update_event_payload) < 1) {
            return $this->sendResponse([], ResponseCodeEnums::EVENT_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch events
        |--------------------------------------------------------------------------
        */
        $update_event_response = $this->eventService->update("id='{$request->event_id}'", $update_event_payload);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$update_event_response['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::EVENT_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(EventResource::make($update_event_response["response"]), ResponseCodeEnums::EVENT_REQUEST_SUCCESSFUL);

    }
}
