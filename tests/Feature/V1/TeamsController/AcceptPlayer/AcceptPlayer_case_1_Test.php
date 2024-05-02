<?php

namespace Tests\Feature\v1\TeamsController\AcceptPlayer;


use Tests\TestCase;
use App\Enums\Mocks;
use App\Enums\ResponseCodeEnums;

/*
|--------------------------------------------------------------------------
| Test Case 2 -> check if user with player ID already belongs to a team
|--------------------------------------------------------------------------
*/
class AcceptPlayer_CASE_1_Test extends TestCase
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
            "full_name" => Mocks::FULL_NAME->value,
            "authorization_token" => Mocks::INVALID_AUTHORIZATION_CODE->value,
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
        $get_accept_player_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.acceptInvite");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_accept_player_response->assertStatus(200)
        ->assertJson([
            'status' => 400,
            'message' => ResponseCodeEnums::USER_IS_ALREADY_IN_A_TEAM->name,
            'response_code' => ResponseCodeEnums::USER_IS_ALREADY_IN_A_TEAM->value,
        ]);

        $this->assertEmpty($get_accept_player_response['data']);
    }
}
