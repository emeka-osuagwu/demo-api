<?php

namespace App\Services\Mocks\Handles;

use App\Enums\Mocks;
use Illuminate\Support\Str;

class HandleTeamPlayersQuery
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
    
            if (Str::containsAll($statement, ["SELECT * FROM", "player_id", Mocks::INVALID_PLAYER_ID->value])) {
                $this->queryBuilder = [];
                return $this;
            }

            if (Str::containsAll($statement, ["SELECT * FROM"])) {
                $this->queryBuilder = [
                    [
                        "id" => generateUUID(),
                        "team_id" => generateUUID(),
                        "player_id" => generatePlayerId(),
                        "is_admin" => "1", //"null"
                        "created_at" => now()->toDateTimeString(),
                        "updated_at" => now()->toDateTimeString(),
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
