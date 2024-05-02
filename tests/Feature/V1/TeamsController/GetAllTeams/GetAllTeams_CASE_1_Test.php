<?php

namespace Tests\Feature\v1\TeamsController\GetAllTeams;

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
use App\Services\TeamService;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> check if user request error
|--------------------------------------------------------------------------
*/
class GetAllTeams_CASE_1_Test extends TestCase
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
        $get_all_team_response_payload = [
            "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
            "response" => [],
            "is_successful" => false,
        ];

        /*
        |--------------------------------------------------------------------------
        | Mock Team Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamService::class, function ($mock) use ($get_all_team_response_payload ) {
            $mock->shouldReceive('getAll')->andReturn($get_all_team_response_payload );
        });

        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get('/api/v1/teams');

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR->name,
            "response_code" => ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR->value,
        ]);
    }
}
