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

class ProductService
{
    use BigQueryQueryBuilder;
    
    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected BigQueryProviderContract $bigQueryProvider
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Get all
    |--------------------------------------------------------------------------
    */
    public function getAll(): array
    {
        try {
            $query = $this->findAllInTable("products");
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
        | convert necessary string data into the right datatype
        |--------------------------------------------------------------------------
        */
        $response = $response->toArray();

        foreach ($response as &$data) {
            $data["amount"] = intval($data["amount"]);
            $data["content"] = json_decode($data["content"], true);
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
    | Create Product
    |--------------------------------------------------------------------------
    */
    public function create(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | request validation
        |--------------------------------------------------------------------------
        */
        $validation = Validator::make($payload, [
            "id" => "string|required",
            "status" => "string|required",
            "amount" => "numeric|required",
            "content" => "array|required",
            "image_url" => "url|required",
            "created_at" => "string|required",
            "updated_at" => "string|required",
            "value_type" => "string|required",
            "content.begi" => "numeric|required",
            "content.juju" => "numeric|required",
            "content.totem" => "numeric|required",
            "content.cowries" => "numeric|required",
            "content.giraffing" => "numeric|required",

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
        | encode and string cast array properties
        |--------------------------------------------------------------------------
        */
        $payload["amount"] = strval($payload["amount"]);
        $payload["content"] = json_encode($payload["content"]);

        /*
        |--------------------------------------------------------------------------
        | create product
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->insertNewRecordInTable('products', $payload);
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
    | Find where
    |--------------------------------------------------------------------------
    */
    public function findWhere(array $payload): array|object
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
            "name" => "string|nullable",
            "status" => "string|nullable",
            "amount" => "string|nullable",
            "content" => "string|nullable",
            "image_url" => "string|nullable",
            "value_type" => "string|nullable",
            "description" => "string|nullable",
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
            $query = $this->findInTableWhere("products", $payload);
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
        | set variable
        |--------------------------------------------------------------------------
        */
        $response = $response[0];
        
        /*
        |--------------------------------------------------------------------------
        | decode and int cast array properties
        |--------------------------------------------------------------------------
        */
        $response["amount"] = intval($response["amount"]);
        $response["content"] = json_decode($response["content"], true);

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
