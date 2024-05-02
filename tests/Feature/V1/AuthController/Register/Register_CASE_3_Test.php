<?php

namespace Tests\Feature\AuthController;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Enums\ResponseCode\AuthResponseCode;

/*
|--------------------------------------------------------------------------
| Test Case 3 -> test for successful authentication
|--------------------------------------------------------------------------
|Test to return successful authentication and authentication response tokens
*/
class Register_CASE_3_Test extends TestCase
{
   /*
    |--------------------------------------------------------------------------
    | test case for successful authentication
    |--------------------------------------------------------------------------
    */
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | endpoint test request
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/v1/register', [
            "email" => Mocks::INVALID_EMAIL->value,
            "role" => Mocks::USER_ROLE->value,
            "auth_id" => Mocks::EXISTING_USER_AUTH_ID->value,
            "full_name" => Mocks::FULL_NAME->value,
            "authorization_token" => Mocks::INVALID_AUTHORIZATION_CODE->value,
            "authorization_provider" => Mocks::GOOGLE_AUTH_PROVIDER->value,
        ]);

        /*
        |--------------------------------------------------------------------------
        | expected response structure and status
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
        ->assertJson([
            "status" => 200,
            "message" => AuthResponseCode::AUTH_USER_REQUEST_SUCCESSFUL->name,
            "response_code" => AuthResponseCode::AUTH_USER_REQUEST_SUCCESSFUL->value,
        ])
        ->assertJsonStructure([
            "status",
            "response_code",
            "message",
            "data" => [
                "token"
            ]
        ]);
    }
}
