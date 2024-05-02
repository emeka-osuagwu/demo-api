<?php

namespace Tests\Feature\AuthController;

use App\Enums\Mocks;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case 2 -> Test for an existing account
|--------------------------------------------------------------------------
| This test ensures an existing account is not created twice
*/
class Login_CASE_2_Test extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
    |--------------------------------------------------------------------------
    | Test for Valid Bussiness Account Type
    |--------------------------------------------------------------------------
    */
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/v1/login', [
            "email" => Mocks::EXISTING_USER_EMAIL->value,
            "auth_id" => Mocks::EXISTING_USER_AUTH_ID->value,
            "full_name" => Mocks::EXISTING_USER_FULL_NAME->value,
            "push_token" => Mocks::PUSH_TOKEN->value,
            "authorization_token" => Mocks::EXISTING_USER_AUTH_TOKEN->value,
            "authorization_provider" => Mocks::GOOGLE_AUTH_PROVIDER->value,
        ]);

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
        ->assertJson([
            "status" => 200,
            "message" => "AUTH_USER_REQUEST_SUCCESSFUL",
            "response_code" => 1000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        $response->assertJsonStructure([
            "data" => [
                'token',
            ]
        ]);
    }
}
