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
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\BigQueryProviderContract;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Providers\BigQueryQueryBuilder;

/*
|--------------------------------------------------------------------------
| Exception Namespace
|--------------------------------------------------------------------------
*/
use Throwable;

class RewardService
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
	| Create Reward
	|--------------------------------------------------------------------------
	*/
    public function create(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | validate payload
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload,[
            'id' => "string|required",
            "type" => "string|nullable",
            "name" => "string|required",
            "value" => "string|nullable",
            "category" => "string|nullable",
            'created_at' => "string|required",
            'updated_at' => "string|required",
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

        try {
            $query = $this->insertNewRecordInTable('rewards', $payload);
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
            "response" => $response,
            "is_successful" => true,
        ];
    }
}
