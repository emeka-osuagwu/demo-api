<?php

namespace Tests\Feature\v1\TeamsController\GetAllTeams;

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
| Test Case 1 -> check for successful response
|--------------------------------------------------------------------------
*/
class GetAllTeams_CASE_2_Test extends TestCase
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

        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get('/api/v1/teams');

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response->assertStatus(200)
            ->assertJson([
                "status" => 200,
                "message" => ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL->name,
                "response_code" => ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL->value,
            ])
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "id",
                        "team_name",
                        "created_at",
                        "updated_at",
                    ]
                ]
            ]);
    }
}
