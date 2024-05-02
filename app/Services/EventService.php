<?php

namespace App\Services;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Throwable;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\BigQueryProviderContract;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Providers Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Providers\BigQueryQueryBuilder;

class EventService
{
    use BigQueryQueryBuilder;

    /*
	|--------------------------------------------------------------------------
	| Dependency Injection
	|--------------------------------------------------------------------------
	*/
    public function __construct(
        protected BigQueryProviderContract $bigQueryProvider
    ) {
    }

    /*
	|--------------------------------------------------------------------------
	| Get All Games
	|--------------------------------------------------------------------------
	*/
    public function getAll(): array
    {
        try {
            $query = $this->findAllInTable("events");
            $response = $this->bigQueryProvider->query($query)->tojson();
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | send successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $response,
            "is_successful" => true,
        ];
    }

    /*
	|--------------------------------------------------------------------------
	| EVENT CREATE
	|--------------------------------------------------------------------------
	*/
    public function create(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | validate request response
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload, [
            'id' => "string|required",
            'status' => "string|in:pending,active",
            'created_at' => "string|required",
            'updated_at' => "string|required",
        ]);

        /*
        |--------------------------------------------------------------------------
        | check validation
        |--------------------------------------------------------------------------
        */
        if ($validator->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validator->errors(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | create game
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->insertNewRecordInTable('events', $payload);
            $this->bigQueryProvider->query($query)->tojson();
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $payload,
            "is_successful" => true,
        ];
    }

    /*
	|--------------------------------------------------------------------------
	| EVENT FINDWHERE
	|--------------------------------------------------------------------------
	*/
    public function findWhere(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | check if payload is empty
        |--------------------------------------------------------------------------
        */
        if (!count($payload)) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                'response' => [],
                'is_successful' => false
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | validate request payload
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload, [
            "id" => "string|nullable",
            "title" => "email|nullable",
            'status' => "string|nullable|in:pending,active,close",
            "description" => "string|nullable",
        ]);

        /*
        |--------------------------------------------------------------------------
        | validate request payload
        |--------------------------------------------------------------------------
        */
        if ($validator->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validator->errors(),
                "is_successful" => false,
            ];
        }

        try {
            $query = $this->findInTableWhere("events", $payload);
            $response = $this->bigQueryProvider->query($query)->tojson();
        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $response,
            "is_successful" => true,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(string $selector, array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | validate request payload
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload, [
            "status" => "string",
            "event_id" => "string",
            "description" => "string",
            "updated_at" => "string|required",
        ]);

        /*
        |--------------------------------------------------------------------------
        | check validation
        |--------------------------------------------------------------------------
        */
        if ($validator->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validator->errors(),
                "is_successful" => false,
            ];
        }
        
        try {
            $query = $this->updateTableRecordWhereIn("events", $selector, $payload);
            $this->bigQueryProvider->query($query);
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $payload,
            "is_successful" => true,
        ];
    }
}
