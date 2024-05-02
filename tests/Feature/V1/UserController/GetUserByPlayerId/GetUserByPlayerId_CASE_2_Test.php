<?php

namespace Tests\Feature\v1\UserController\GetUserByPlayerId;

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
| Test Case 2 -> test getUserByPlayerId endpoint
|--------------------------------------------------------------------------
*/
class GetUserByPlayerId_CASE_2_Test extends TestCase
{

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
        $player_id = Mocks::INVALID_PLAYER_ID->value;
        $token = $login_response['data']['token'];

        /*
        |--------------------------------------------------------------------------
        | get profile from the login request data
        |--------------------------------------------------------------------------
        */
        $get_profile_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get("/api/v1/user.getUserByPlayerId/{$player_id}");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_profile_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => ResponseCodeEnums::USER_NOT_FOUND->name,
            "response_code" => ResponseCodeEnums::USER_NOT_FOUND->value,
        ]);

    }
}
