<?php

namespace App\Services;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\BigQueryProviderContract;


/*
|--------------------------------------------------------------------------
| Exception Namespace
|--------------------------------------------------------------------------
*/
use Throwable;

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

class TeamPlayerPointService
{
    use BigQueryQueryBuilder;

    /*
	|--------------------------------------------------------------------------
	| Dependency Injection
	|--------------------------------------------------------------------------
	*/
    public function __construct(
        protected BigQueryProviderContract $bigQueryProvider
    ){}

    /*
	|--------------------------------------------------------------------------
	| Get All
	|--------------------------------------------------------------------------
	*/
    public function getAll(): array
    {
        try {
            $query = $this->findAllInTable("team_player_points");
            $payload = $this->bigQueryProvider->query($query)->tojson();
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
            "response" => $payload,
            "is_successful" => true,
        ];
    }
    /*
	|--------------------------------------------------------------------------
	| Create Team
	|--------------------------------------------------------------------------
	*/
    public function create(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | validate request payload
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload, [
            'id' => "string|required",
            'created_at' => "string|required",
            'updated_at' => "string|required"
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if validation fails
        |--------------------------------------------------------------------------
        */
        if ($validator->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validator->errors(),
                "is_successful"=> false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | create team
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->insertNewRecordInTable('team_player_points', $payload);
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
	| find where
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
            "points" => "string|nullable",
            "team_id" => "string|nullable",
            "player_id" => "string|nullable",
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if validation fails
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
        | find in table where
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->findInTableWhere("team_player_points", $payload);
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
	| update
	|--------------------------------------------------------------------------
	*/
    public function update(string $selector, array $payload): array
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
            "points" => "string|nullable",
            "team_id" => "string|nullable",
            "player_id" => "string|nullable",
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if validation fails
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
        | update in table where
        |--------------------------------------------------------------------------
        */
        try {
            $payload["updated_at"] = now()->toDateTimeString();
            $query = $this->updateTableRecordWhereIn("team_player_points", $selector, $payload);
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
    | Delete Team
    |--------------------------------------------------------------------------
    */
    public function deleteWhere(string $payload):array
    {
        try {
            $query = $this->deleteTableRecordWhereIn("team_player_points",$payload);
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
        | successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $response,
            "is_successful" => true,
        ];
    }
}
