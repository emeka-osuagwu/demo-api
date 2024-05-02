<?php

namespace App\Services\Mocks\Handles;

use App\Enums\Mocks;
use Illuminate\Support\Str;

class HandleTeamPlayerPointsQuery
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

    /*
    |--------------------------------------------------------------------------
    | fetch data
    |--------------------------------------------------------------------------
    */
    if (Str::containsAll($statement, ["SELECT * FROM"])) {
        $this->queryBuilder = [
            [
                "id" => generateUUID(),
                "points" => "100",
                "team_id" => generateUUID(),
                "player_id" => generatePlayerId(),
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
