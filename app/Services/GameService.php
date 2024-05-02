<?php

namespace App\Services;



/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Throwable;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\BigQueryProviderContract;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ServiceResponseMessageEnum;
/*
|--------------------------------------------------------------------------
| Providers Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Providers\BigQueryQueryBuilder;


class GameService
{
    use BigQueryQueryBuilder;

    /*
	|--------------------------------------------------------------------------
	| Dependency Injection
	|--------------------------------------------------------------------------
	*/
    public function __construct(
        protected BigQueryProviderContract $bigQueryProvider
    ) {
    }

    /*
	|--------------------------------------------------------------------------
	| Get All Games
	|--------------------------------------------------------------------------
	*/
    public function getAll(): array
    {
        try {
            $query = $this->findAllInTable("games");
            $response = $this->bigQueryProvider->query($query)->tojson();
        } catch (Throwable $exception) {
            report($exception);
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
	| Create Game
	|--------------------------------------------------------------------------
	*/
    public function create(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | validate request response
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload, [
            'id' => "string|required",
            "winner"=> "string|required",
            "player_1" => "string|required",
            "player_2" => "string|required",
            "completed"=> "boolean|required",
            "game_time"=> "numeric|required",
            "session_id" => "string|required",
            'created_at' => "string|required",
            'updated_at' => "string|required",
            "player_1_games" => "array|nullable",
            "player_2_games" => "array|nullable",
            "challenge_accepted" => "boolean|required",
            "player_1_completed" => "boolean|required",
            "player_2_completed" => "boolean|required",
        ]);

        if ($validator->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validator->errors(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | convert response array to strings
        |--------------------------------------------------------------------------
        */
        $payload["game_time"] = strval($payload["game_time"]);
        $payload["completed"] = strVal($payload["completed"]);
        $payload["player_1_games"] =  json_encode($payload["player_1_games"]);
        $payload["player_2_games"] =  json_encode($payload["player_2_games"]);
        $payload["challenge_accepted"] = strval($payload["challenge_accepted"]);
        $payload["player_1_completed"] = strVal($payload["player_1_completed"]);
        $payload["player_2_completed"] = strVal($payload["player_2_completed"]);

        /*
        |--------------------------------------------------------------------------
        | create game
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->insertNewRecordInTable('games', $payload);
            $this->bigQueryProvider->query($query)->tojson();
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | return successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $payload,
            "is_successful" => true,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Game Find Where
    |--------------------------------------------------------------------------
    */
    public function findWhere(array $payload): array
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
            "winner" => "string|nullable",
            "player_1" => "email|nullable",
            "player_2" => "string|nullable",
            "completed" => "string|nullable",
            "event_id" => "string|nullable",
            "game_time" => "string|nullable",
            "session_id" => "string|nullable",
            "player_1_games" => "string|nullable",
            "player_2_games" => "string|nullable",
            "challenge_accepted" => "string|nullable",
            "player_1_completed" => "string|nullable",
            "player_2_completed" => "string|nullable",
        ]);

        /*
        |--------------------------------------------------------------------------
        | validate request payload
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
        | generate query
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->findInTableWhere("games", $payload);
            $response = $this->bigQueryProvider->query($query)->tojson();
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
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
    | Game Update
    |--------------------------------------------------------------------------
    */
    public function update(string $selector, array $payload): object|array
    {
        /*
        |--------------------------------------------------------------------------
        | validate request payload
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($payload, [
            "winner" => "string|nullable",
            "player_1" => "email|nullable",
            "player_2" => "string|nullable",
            "completed" => "string|nullable",
            "game_time" => "string|nullable",
            "session_id" => "string|nullable",
            "player_1_games" => "string|nullable",
            "player_2_games" => "string|nullable",
            "challenge_accepted" => "string|nullable",
            "player_1_completed" => "string|nullable",
            "player_2_completed" => "string|nullable",
        ]);

        /*
        |--------------------------------------------------------------------------
        | check validation
        |--------------------------------------------------------------------------
        */
        if ($validator->fails()) {
            return [
                "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
                "response" => $validator->errors(),
                "is_successful" => false,
            ];
        }

        try {
            $payload["updated_at"] = now()->toDateTimeString();
            $query = $this->updateTableRecordWhereIn("games", $selector, $payload);
            $this->bigQueryProvider->query($query);

        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | successful response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $payload,
            "is_successful" => true,
        ];
    }
}
