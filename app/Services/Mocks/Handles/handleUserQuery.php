<?php

namespace App\Services\Mocks\Handles;

use App\Enums\Mocks;
use Illuminate\Support\Str;

class handleUserQuery
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
	)
	{
		$this->queryBuilder = null;
	}

	/*
	|--------------------------------------------------------------------------
	| toJson
	|--------------------------------------------------------------------------
	| This functions converts the bigquery result to json
	*/
	public function toJson(): object | array
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
        | mock for invalid bank id
        |--------------------------------------------------------------------------
        */
		if(Str::containsAll($statement, ["SELECT * FROM", "authorization_token", Mocks::INVALID_AUTHORIZATION_CODE])){
			$this->queryBuilder = [];
			return $this;
		}

        /*
        |--------------------------------------------------------------------------
        | if you want to test a mock a user found
        |--------------------------------------------------------------------------
        */
		if(Str::containsAll($statement, ["SELECT * FROM"])){

            if(Str::containsAll($statement, [Mocks::INVALID_EMAIL->value]) || Str::containsAll($statement, [Mocks::INVALID_PLAYER_ID->value]) || Str::containsAll($statement, [Mocks::INVALID_ID->value])){
                $this->queryBuilder = [];
                return $this;
            }

			$this->queryBuilder = [
				[
					"id" => generateUUID(),
					"email" => "teffddddddestd@gmail.com",
					"juju" => "11",
					"jara" => "null",
					"begi" => "null",
					"level" => "null",
					"totem" => "10000",
					"score" => "null",
					"points" => "100000",
					"cowries" => "null",
					"game_won" => "null",
					"password" => hashValue("password"),
					"giraffing" => "null",
					"full_name" => "null",
					"player_id" => "8fq5TwbtNz",
					"device_id" => generateUUID(),
					"created_at" => now()->toDateTimeString(),
					"updated_at" => "2024-04-01 14:18:07",
					"push_token" => null,
					"game_played" => "null",
					"highest_score" => "null",
					"average_score" => "null",
					"longest_streak" => "null",
					"current_streak" => "null",
					"padi_play_wins" => "null",
					"padi_play_losses" => "null",
					"completed_puzzles" => "111,111",
					"authorization_token" => "11111111111",
					"authorization_provider" => "null",
					"completed_puzzle_levels" => "null",
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
