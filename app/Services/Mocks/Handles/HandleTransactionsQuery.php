<?php

namespace App\Services\Mocks\Handles;

use App\Enums\Mocks;
use Illuminate\Support\Str;

class handleTransactionsQuery
{
    /*
    |--------------------------------------------------------------------------
    | Variable Namespace
    |--------------------------------------------------------------------------
    */
    protected $queryBuilder;

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
    ) {
        $this->queryBuilder = null;
    }

    /*
    |--------------------------------------------------------------------------
    | toJson
    |--------------------------------------------------------------------------
    | This functions converts the bigquery result to json
    */
    public function toJson(): object|array
    {
        return collect($this->queryBuilder);
    }

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function mocks($statement)
    {
        if (Str::containsAll($statement, ["SELECT * FROM", Mocks::INVALID_PROVIDER_TRANSACTION_REFERENCE->value])) {
            $this->queryBuilder = [];
            return $this;
        }

        /*
        |--------------------------------------------------------------------------
        | fetch data
        |--------------------------------------------------------------------------
        */
        if (Str::containsAll($statement, ["SELECT * FROM"])) {
            $this->queryBuilder = [
                [
                    "id" => generateUUID(),
                    "player_id" => generatePlayerId(),
                    "amount" => "200",
                    "purchase" => "in_store_purchase",
                    "payment_channel" => "card",
                    "transaction_reference" => generateUUID(),
                    "provider_transaction_reference" => "yjvmsc29r7",
                    "payment_method" => "card",
                    "payment_method_id" => "3433224764",
                    "status" => "active",
                    "created_at" => now()->toDateTimeString(),
                    "updated_at" => now()->toDateTimeString()
                ],
            ];
            return $this;
        }

        /*
        |--------------------------------------------------------------------------
        | default response
        |--------------------------------------------------------------------------
        */
        $this->queryBuilder = [];
        return $this;
    }
}
