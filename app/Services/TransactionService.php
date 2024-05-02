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
| Providers Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Providers\BigQueryQueryBuilder;

/*
|--------------------------------------------------------------------------
| Exceptions Namespace
|--------------------------------------------------------------------------
*/
use Throwable;

class TransactionService
{
    use BigQueryQueryBuilder;

    /*
	|--------------------------------------------------------------------------
	| Dependency Injection
	|--------------------------------------------------------------------------
	*/
    public function __construct(
        protected BigQueryProviderContract $bigQueryProvider
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Create Transaction
    |--------------------------------------------------------------------------
    */
    public function create(array $payload)
    {
        /*
        |--------------------------------------------------------------------------
        | validate request
        |--------------------------------------------------------------------------
        */
        $validation = Validator::make($payload,[
            "id" => "string|required",
            'amount' => "string|required",
            'status' => 'string|required',
            'purchase' => 'string|required',
            "created_at" => "string|required",
            "updated_at" => "string|required",
            'payment_method' => 'string|required',
            'payment_channel' => 'string|required',
            'payment_method_id' => 'string|required',
            "transaction_reference" => "string|required",
        ]);

        if ($validation->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validation->errors(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | create transaction
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->insertNewRecordInTable('transactions', $payload);
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
        | successful response
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
    | Get All Transactions
    |--------------------------------------------------------------------------
    */
    public function getAll()
    {
        try {
            $query = $this->findAllInTable("transactions");
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

    /*
    |--------------------------------------------------------------------------
    | Find Transaction Where
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
            "amount" => "string|nullable",
            "status" => "string|nullable",
            "purchase" => "string|nullable",
            "player_id" => "string|nullable",
            "payment_method" => "string|nullable",
            "payment_channel" => "string|nullable",
            "payment_method_id" => "string|nullable",
            "transaction_reference" => "string|nullable",
            "provider_transaction_reference" => "string|nullable",
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
            $query = $this->findInTableWhere("transactions", $payload);
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

    /*
    |--------------------------------------------------------------------------
    | Delete Transaction
    |--------------------------------------------------------------------------
    */
    public function deleteTransactionWhere(string $payload):array
    {
        try {
            $query = $this->deleteTableRecordWhereIn("transactions",$payload);
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
