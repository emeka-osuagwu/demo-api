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
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\TransactionService;

/*
|--------------------------------------------------------------------------
| Ressources Namespace
|--------------------------------------------------------------------------
*/
use App\Http\Resources\TransactionResource;

class FetchByPlayerId extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Dependency Injection
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected TransactionService $transactionService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */
    public function requestValidation($data)
    {
        return Validator::make($data, [
            'player_id' => 'required|string',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch Transaction by reference
    |--------------------------------------------------------------------------
    */
    public function __invoke($player_id)
    {
        /*
        |--------------------------------------------------------------------------
        | request validation
        |--------------------------------------------------------------------------
        */
        $validator = $this->requestValidation(['player_id' => $player_id]);

        if ($validator->fails()) {
            return $this->sendResponse($validator->errors(), ResponseCodeEnums::TRANSACTION_REQUEST_VALIDATION_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        | fetch data
        |--------------------------------------------------------------------------
        */
        $transaction = $this->transactionService->findWhere(['player_id' => $player_id]);

        /*
        |--------------------------------------------------------------------------
        | if transaction request fails
        |--------------------------------------------------------------------------
        */
        if ($transaction["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$transaction["is_successful"]) {
            return $this->sendResponse([], ResponseCodeEnums::TRANSACTION_SERVICE_REQUEST_ERROR);
        }

        /*
        |--------------------------------------------------------------------------
        |if transaction not found
        |--------------------------------------------------------------------------
        */
        if($transaction["is_successful"] == true && $transaction["status"] == ServiceResponseMessageEnum::SUCCESSFUL->value && !count($transaction["response"])){
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
