<?php

namespace Tests\Feature\v1\GameController\AcceptInvite;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Enums\ResponseCodeEnums;

class AcceptInvite_CASE_1_Test extends TestCase
{
   /*
    |--------------------------------------------------------------------------
    | Test Case 1 -> test accept invite endpoint for validation response
    |--------------------------------------------------------------------------
    */
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | login request
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
        | set variable
        |--------------------------------------------------------------------------
        */
        $token = $login_response['data']['token'];

        /*
        |--------------------------------------------------------------------------
        | get user profile
        |--------------------------------------------------------------------------
        */
        $accept_player_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/game.acceptInvite");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $accept_player_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => ResponseCodeEnums::GAME_REQUEST_VALIDATION_ERROR->name,
            "response_code" => ResponseCodeEnums::GAME_REQUEST_VALIDATION_ERROR->value,
        ])
        ->assertJsonPath('data.session_id.0', "The session id field is required.");

    }
}
