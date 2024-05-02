<?php

namespace Tests\Feature\v1\TeamsController\InvitePlayer;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\Mocks;
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Test Case 2 -> check if user with player ID already belongs to a team
|--------------------------------------------------------------------------
*/
class InvitePlayer_CASE_7_Test extends TestCase
{
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | given that user is authenticated
        |--------------------------------------------------------------------------
        */
        $login_response = $this->post('/api/v1/login', [
            "email" => Mocks::EXISTING_USER_EMAIL->value,
            "auth_id" => Mocks::EXISTING_USER_AUTH_ID->value,
            "full_name" => Mocks::EXISTING_USER_FULL_NAME->value,
            "push_token" => Mocks::PUSH_TOKEN->value,
            "authorization_token" => Mocks::EXISTING_USER_AUTH_TOKEN->value,
            "authorization_provider" => Mocks::GOOGLE_AUTH_PROVIDER->value,
        ]);

        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $token = $login_response['data']['token'];
        $team_id = "111111";
        $team_name = "gbasgbos";
        $player_id = Mocks::INVALID_PLAYER_ID->value;


        $user_response_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [
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
					"password" => "null",
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
            ],
            "is_successful" => true,
        ];

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(UserService::class, function ($mock) use ($user_response_payload) {
            $mock->shouldReceive('findWhere')->andReturn($user_response_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.invitePlayer", [
            "team_name" => $team_name,
            "player_id" => $player_id
        ]);

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'message' => ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL->name,
            'response_code' => ResponseCodeEnums::TEAM_PLAYER_REQUEST_SUCCESSFUL->value,
        ]);
    }
}

