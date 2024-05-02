<?php

namespace App\Http\Controllers\Transactions;

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
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TransactionService;

/*
|--------------------------------------------------------------------------
| Resources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\TransactionResource;

class FetchTransactions extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected TransactionService $transactionService
    ){}

    /*
    |--------------------------------------------------------------------------
    | Fetch All Transactions
    |--------------------------------------------------------------------------
    */
    public function __invoke()
    {
        /*
        |--------------------------------------------------------------------------
        | fetch transactions
        |--------------------------------------------------------------------------
        */
        $transactions = $this->transactionService->getAll();

        /*
        |--------------------------------------------------------------------------
        | if transaction request fails
        |--------------------------------------------------------------------------
        */
        if ($transactions["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$transactions["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        |if transaction not found
        |--------------------------------------------------------------------------
        */
        if ($transactions["is_successful"] == true && $transactions["status"] == ServiceResponseMessageEnum::SUCCESSFUL->value && !count($transactions["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_NOT_FOUND);
        }

        /*
        |--------------------------------------------------------------------------
        | return transactions response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(TransactionResource::collection($transactions["response"]), ResponseCodeEnums::TRANSACTION_REQUEST_SUCCESSFUL);
    }
}
