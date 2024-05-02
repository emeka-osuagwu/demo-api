<?php

namespace Tests\Feature\AdminController;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> Test for request validation error
|--------------------------------------------------------------------------
*/
class Admin_Login_CASE_1_Test extends TestCase
{
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
        $response = $this->post('/api/v1/admin.login', []);

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
        ])
        ->assertJsonPath('data.email.0', "The email field is required.")
        ->assertJsonPath('data.password.0', "The password field is required.");
    }
}
