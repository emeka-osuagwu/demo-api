<?php

namespace App\Services\Mocks\Handles;

use App\Enums\Mocks;
use Illuminate\Support\Str;

class HandleTeamsQuery
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
    (){
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
    if(Str::containsAll($statement,  ["SELECT * FROM", Mocks::INVALID_TEAM_ID->value]) || Str::containsAll($statement,  ["SELECT * FROM", Mocks::INVALID_TEAM_NAME->value])){
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
                "team_name" => "agbasgbos",
                "created_at" => now()->toDateTimeString(),
                "updated_at" => now()->toDateTimeString()
            ]
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
