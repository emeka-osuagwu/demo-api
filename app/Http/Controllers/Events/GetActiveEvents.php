<?php

namespace App\Http\Controllers\Events;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\EventResource;

class GetActiveEvents extends Controller
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
    | Resources Namespace
    |--------------------------------------------------------------------------
    */
    public function __invoke(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | fetch events
        |--------------------------------------------------------------------------
        */
        $fetch_event_response = $this->eventService->findWhere(["status" => "active"]);

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$fetch_event_response['is_successful']) {
            return $this->sendResponse([], ResponseCodeEnums::EVENT_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if no event was found
        |--------------------------------------------------------------------------
        */
        if(!count($fetch_event_response["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::EVENT_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(EventResource::collection($fetch_event_response["response"]), ResponseCodeEnums::EVENT_REQUEST_SUCCESSFUL);
    }
}
