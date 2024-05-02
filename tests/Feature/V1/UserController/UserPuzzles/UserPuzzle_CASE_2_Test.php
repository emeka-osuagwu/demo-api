<?php

namespace Tests\Feature\v1\UserController\UserPuzzles;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\Mocks;
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\UserService;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> check if user does not exist
|--------------------------------------------------------------------------
*/
class UserPuzzle_CASE_2_Test extends TestCase
{
    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | given that user is authenticated
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
        | set variables
        |--------------------------------------------------------------------------
        */
        $token = $login_response['data']['token'];
        $response_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [],
            "is_successful" => true,
        ];

        /*
        |--------------------------------------------------------------------------
        | Mock User Service
        |--------------------------------------------------------------------------
        */
        $this->mock(UserService::class, function($mock) use ($response_payload) {
            $mock->shouldReceive('findWhere')->andReturn($response_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | make request to get user puzzles
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get('/api/v1/user.puzzle');

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => ResponseCodeEnums::USER_NOT_FOUND->name,
            "response_code" => ResponseCodeEnums::USER_NOT_FOUND->value,
        ]);
    }
}
