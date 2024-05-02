<?php

namespace App\Http\Controllers\Events;

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

class AllEvent extends Controller
{
    use ResponseTrait;

    public function __construct
    (
        protected EventService $eventService,
    ) {}

    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | fetch events
        |--------------------------------------------------------------------------
        */
        $fetch_event_response = $this->eventService->getAll();

        /*
        |--------------------------------------------------------------------------
        | check if request failed
        |--------------------------------------------------------------------------
        */
        if(!$fetch_event_response['is_successful']) {
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
