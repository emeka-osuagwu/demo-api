<?php

namespace App\Services;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\BigQueryProviderContract;
use App\Enums\RolesEnums;
/*
|--------------------------------------------------------------------------
| Exception Namespace
|--------------------------------------------------------------------------
*/
use Throwable;

/*
|--------------------------------------------------------------------------
| Providers Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Providers\BigQueryQueryBuilder;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ServiceResponseMessageEnum;


class UserService
{
    use BigQueryQueryBuilder;

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function __construct(
        protected BigQueryProviderContract $bigQueryProvider
    ) {
    }

    /*
	|--------------------------------------------------------------------------
	| Get All Users
	|--------------------------------------------------------------------------
	*/
    public function getAll(): array
    {
        try {
            $query = $this->findAllInTable("users");
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
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function create(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $request_payload = [
            'id' => generateUUID(),
			'role' => $payload['role'] ?? RolesEnums::USER,
            'device_id' => generateUUID(),
            'player_id' => generatePlayerId(),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

		$request_payload = array_merge($payload, $request_payload);

        /*
        |--------------------------------------------------------------------------
        | validate request payload
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($request_payload, [
            "id" => "string|required",
            'jara' => 'string|required_if:section,jara',
            "role" => 'string|required',
            "image" => 'url|nullable',
            "juju" => 'string|required_if:section,juju',
            "begi" => 'string|required_if:section,begi',
            "email" => "string|required",
            'totem' => 'string|required_if:section,totem',
            'score' => 'string|required_if:section,score',
            'points' => 'string|required_if:section,points',
            'auth_id' => 'string|required_if:section,auth_id',
            'cowries' => 'string|required_if:section,cowries',
            'game_won' => 'string|required_if:section,game_won',
            "full_name" => "string|required_if:section,full_name",
            "password" => "string|required_if:section,password",
            'device_id' => "string|required_if:section,device_id",
            "giraffing" => 'string|required_if:section,giraffing',
            'player_id' => "string|required_if:section,player_id",
            "created_at" => "string|required",
            "updated_at" => "string|required",
            'game_played' => 'string|required_if:section,game_played',
            'highest_score' => 'string|required_if:section,highest_score',
            'average_score' => 'string|required_if:section,average_score',
            'longest_streak' => 'string|required_if:section,longest_streak',
            'current_streak' => 'string|required_if:section,current_streak',
            "padi_play_wins" => "string|required_if:section,padi_play_wins",
            "padi_play_losses" => "string|required_if:section,padi_play_losses",
            "completed_puzzles" => "array|nullable",
            "completed_puzzle_levels" => "array|nullable",
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
        | convert arrays to string
        |--------------------------------------------------------------------------
        */
        $payload["completed_puzzles"] = implode(",", $payload["completed_puzzles"]);
        $payload["completed_puzzle_levels"] = implode(",", $payload["completed_puzzle_levels"]);

        /*
        |--------------------------------------------------------------------------
        | create user
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->insertNewRecordInTable('users', $payload);
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
        | successful response
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
    | Find User Where
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
            "email" => "email|nullable",
            'auth_id' => 'string|nullable',
            "player_id" => "string|nullable",
            "device_id" => "string|nullable",
            "authorization_token" => "string|nullable",
        ]);

        /*
        |--------------------------------------------------------------------------
        | check if validation fails
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
        | find users where
        |--------------------------------------------------------------------------
        */
        try {
            $query = $this->findInTableWhere("users", $payload);
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
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function update(string $selector, array $payload): array
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
            'jara' => "numeric|nullable",
            "juju" => "numeric|nullable",
            "begi" => "numeric|nullable",
            'totem' => "numeric|nullable",
            'score' => "numeric|nullable",
            'points' => "numeric|nullable",
            'cowries' => "numeric|nullable",
            'game_won' => "numeric|nullable",
            "giraffing" => "numeric|nullable",
            "updated_at" => "string|nullable",
            'game_played' => "string|nullable",
            'highest_score' => "numeric|nullable",
            'average_score' => "numeric|nullable",
            'longest_streak' => "numeric|nullable",
            'current_streak' => "numeric|nullable",
            "padi_play_wins" => "numeric|nullable",
            "padi_play_losses" => "numeric|nullable",
            "completed_puzzles" => "array|nullable",
            "completed_puzzle_levels" => "array|nullable",
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
		| process the array
		|--------------------------------------------------------------------------
		| Need fix -> please add the rest that need to be converted to array 
        */
		$processed_payload = convert_values_to_string($payload, ['jara', 'juju', 'begi', 'score']);
		
        /*
		|--------------------------------------------------------------------------
		| make request
		|--------------------------------------------------------------------------
		*/
        try {
            $query = $this->updateTableRecordWhereIn("users", $selector, $processed_payload);
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
            "response" => $processed_payload,
            "is_successful" => true,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | add comment
    |--------------------------------------------------------------------------
    */
    public function findWhereOr(array $payload): array
    {
        try {
            $query = $this->findInTableWhereOr('users', $payload);
            $payload = $this->bigQueryProvider->query($query)->toJson();
            return $payload;
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
