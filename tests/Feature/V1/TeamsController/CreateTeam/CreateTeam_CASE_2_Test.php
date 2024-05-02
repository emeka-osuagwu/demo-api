<?php

namespace Tests\Feature\v1\TeamsController\CreateTeam;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Enums\ResponseCodeEnums;


class CreateTeam_CASE_2_Test extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Test Case 2 -> check if authenticated user already belongs to a team or is a team admin
    |--------------------------------------------------------------------------
    */
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
        |  when we attempt to get user puzzles
        |--------------------------------------------------------------------------
        */
        $create_team_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post('/api/v1/team.create', [
            "team_name" => "agbasgbos",
            "event_id" => Mocks::EVENT_ID->value
        ]);

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $create_team_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => ResponseCodeEnums::TEAM_PLAYER_IS_ALREADY_ON_A_TEAM->name,
            "response_code" => ResponseCodeEnums::TEAM_PLAYER_IS_ALREADY_ON_A_TEAM->value,
        ]);
    }
}
