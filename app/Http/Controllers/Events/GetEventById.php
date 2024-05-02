<?php

namespace App\Http\Controllers\Events;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
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
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\EventResource;

class GetEventById extends Controller
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
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Get Event By Id
    |--------------------------------------------------------------------------
    */
    public function __invoke($event_id)
    {
        /*
        |--------------------------------------------------------------------------
        | get event by id
        |--------------------------------------------------------------------------
        */
        $find_event_response = $this->eventService->findWhere(["id" => $event_id]);

        /*
        |--------------------------------------------------------------------------
        | check if service response fails
        |--------------------------------------------------------------------------
        */
        if(!$find_event_response["is_successful"]){
            return $this->sendResponse([], ResponseCodeEnums::EVENT_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | check if no events are found
        |--------------------------------------------------------------------------
        */
        if(!count($find_event_response["response"])){
            return $this->sendResponse([], ResponseCodeEnums::EVENT_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | send a successful response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(EventResource::make($find_event_response["response"][0]), ResponseCodeEnums::EVENT_REQUEST_SUCCESSFUL);
    }
}
