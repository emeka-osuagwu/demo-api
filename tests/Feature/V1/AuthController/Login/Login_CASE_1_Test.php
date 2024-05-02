<?php

namespace Tests\Feature\AuthController;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> Test for request validation error
|--------------------------------------------------------------------------
*/
class Login_CASE_1_Test extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
    |--------------------------------------------------------------------------
    | Test validation errors
    |--------------------------------------------------------------------------
    */
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/v1/login', []);

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
