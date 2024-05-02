<?php

namespace Tests\Feature\v1\TeamsController\InvitePlayer;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\Mocks;
use App\Enums\ResponseCodeEnums;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> check for team service request errors
|--------------------------------------------------------------------------
*/
class InvitePlayer_CASE_1_Test extends TestCase
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

        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.invitePlayer", []);

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
            ->assertJson([
                'status' => 400,
                'message' => ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR->name,
                'response_code' => ResponseCodeEnums::TEAM_REQUEST_VALIDATION_ERROR->value,
            ])
            ->assertJsonStructure([
                "data" => [
                    "team_name",
                    "player_id",
                ]
            ])
            ->assertJsonPath('data.team_name.0', "The team name field is required.")
            ->assertJsonPath('data.player_id.0', "The player id field is required.");

    }
}
