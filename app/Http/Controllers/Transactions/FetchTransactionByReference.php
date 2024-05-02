<?php

namespace App\Http\Controllers\Transactions;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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

class FetchTransactionByReference extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            'transaction_reference' => 'required|string',
        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Fetch Transaction By Reference
    |--------------------------------------------------------------------------
    */
    public function __invoke($reference)
    {

        /*
        |--------------------------------------------------------------------------
        | request validation
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(['transaction_reference' => $reference]);

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::TRANSACTION_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch data
        |--------------------------------------------------------------------------
        */
        $transaction = $this->transactionService->findWhere(['transaction_reference' => $reference]);

        /*
        |--------------------------------------------------------------------------
        | if transaction request fails
        |--------------------------------------------------------------------------
        */
        if ($transaction["status"] == "service_request_error" && !$transaction["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        |if transaction not found
        |--------------------------------------------------------------------------
        */
        if ($transaction["is_successful"] == true && $transaction["status"] == "successful" && !count($transaction["response"])) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_NOT_FOUND);
        }


        /*
        |--------------------------------------------------------------------------
        | send response
        |--------------------------------------------------------------------------
        */
        return $this->sendResponse(TransactionResource::collection($transaction["response"]), ResponseCodeEnums::TRANSACTION_REQUEST_SUCCESSFUL);
    }
}
