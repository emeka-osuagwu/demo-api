<?php

namespace App\Services\Providers;

use App\Contracts\CardContract;
use Throwable;

/*
|--------------------------------------------------------------------------
| Exceptions Namespace
|--------------------------------------------------------------------------
*/
use App\Exceptions\AppException;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\PaystackResponseMessageEnum;


class PaystackServiceProvider implements CardContract
{


    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    protected $paystack_url;
    protected $paystack_secret_key;

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function __construct()
    {
        $this->paystack_url = env("PAYSTACK_API_URL");
        $this->paystack_secret_key = env("PAYSTACK_SECRET_KEY");
    }

    /*
    |--------------------------------------------------------------------------
    | Contracts Namespace
    |--------------------------------------------------------------------------
    */
    public function getProviderName(): string
    {
        return 'paystack';
    }

    /*
    |--------------------------------------------------------------------------
    | add comment
    |--------------------------------------------------------------------------
    */
    public function fetchTransactionByReference(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        if(!isset($payload["transaction_reference"])) {
            throw new AppException(
                'PaystackServiceProvider@addCard',
                'invalid method argument'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | make request
        |--------------------------------------------------------------------------
        */
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->paystack_secret_key,
            ])->get("{$this->paystack_url}/transaction/verify/{$payload['transaction_reference']}");
        } catch (Throwable $exception) {
            return [
                'status' => PaystackResponseMessageEnum::PROVIDER_SERVICE_CONNECTION_ERROR->value,
                'response' => $exception->getMessage(),
                'is_successful' => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | success response
        |--------------------------------------------------------------------------
        */
        if($response->object()->status && $response->object()->message === "Verification successful"){
            return [
                'status' => PaystackResponseMessageEnum::SUCCESSFUL->value,
                'response' => $response->json(),
                'is_successful' => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | response when transaction reference not found.
        |--------------------------------------------------------------------------
        */
        if(!$response->object()->status && $response->object()->message === 'Transaction reference not found'){
            return [
                'status' => PaystackResponseMessageEnum::TRANSACTION_REFERENCE_NOT_FOUND->value,
                'response' => $response->json(),
                'is_successful' => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | response for failed request
        |--------------------------------------------------------------------------
        */
        if($response->failed()){
            return [
                'status' => PaystackResponseMessageEnum::FAILED->value,
                'response' => $response->json(),
                'is_successful' => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | failed response
        |--------------------------------------------------------------------------
        */
        return [
            'status' => PaystackResponseMessageEnum::FAILED->value,
            'response' => $response->json(),
            'is_successful' => false,
        ];
    }
}
