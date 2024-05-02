<?php

namespace Tests\Feature\AdminController;

use App\Enums\Mocks;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> Test admin invalid login credential
|--------------------------------------------------------------------------
*/
class Admin_Login_CASE_3_Test extends TestCase
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
            'password' => 'null',
        ]);

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => "INVALID_LOGIN_CREDENTIALS",
            "response_code" => 1008,
        ]);
    }
}
