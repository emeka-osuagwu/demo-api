<?php

namespace Tests\Feature\AuthController;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> Test for a new account
|--------------------------------------------------------------------------
| This test is to test the validation for the incoming request payload
*/
class Register_CASE_1_Test extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
    |--------------------------------------------------------------------------
    | testing the validation for the incoming request payload
    |--------------------------------------------------------------------------
    */
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | endpoint response
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/v1/register', []);

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => "AUTH_USER_REQUEST_VALIDATION_ERROR",
            "response_code" => 1002,
        ]);
    }
}
