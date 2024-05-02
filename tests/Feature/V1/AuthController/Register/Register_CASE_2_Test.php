<?php

namespace Tests\Feature\AuthController;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Enums\ResponseCode\AuthResponseCode;

/*
|--------------------------------------------------------------------------
| Test Case 2 -> test to check for existing users and unsuccessful database query response
|--------------------------------------------------------------------------
|
*/
class Register_CASE_2_Test extends TestCase
{
   /*
    |--------------------------------------------------------------------------
    | testing for existing users or unsuccessful database query response
    |--------------------------------------------------------------------------
    */
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | endpoint request
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/v1/register', [
            "email" => Mocks::EXISTING_USER_EMAIL->value,
            "role" => Mocks::USER_ROLE->value,
            "full_name" => Mocks::EXISTING_USER_FULL_NAME->value,
            "authorization_token" => Mocks::EXISTING_USER_AUTH_TOKEN->value,
            "authorization_provider" => Mocks::GOOGLE_AUTH_PROVIDER->value,
            "auth_id" => Mocks::EXISTING_USER_AUTH_ID->value
        ]);

        /*
        |--------------------------------------------------------------------------
        | response
        |--------------------------------------------------------------------------
        */

        $response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => AuthResponseCode::AUTH_USER_REQUEST_ERROR->name,
            "response_code" => AuthResponseCode::AUTH_USER_REQUEST_ERROR->value,
        ]);
    }
}
