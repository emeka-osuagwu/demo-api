<?php

namespace Tests\Feature\AdminController;

use App\Enums\Mocks;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> Test admin can login
|--------------------------------------------------------------------------
*/
class Admin_Login_CASE_2_Test extends TestCase
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
        | login
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/v1/admin.login', [
            'email' => Mocks::EXISTING_USER_EMAIL->value,
            'password' => 'password',
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
    }
}
