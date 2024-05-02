<?php

namespace Tests\Feature\v1\TeamsController\CreateTeam;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Services\TeamService;
use App\Enums\ResponseCodeEnums;
use App\Services\TeamPlayerService;
use App\Enums\ServiceResponseMessageEnum;


class CreateTeam_CASE_3_Test extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Test Case 3 -> test for team service error fail on creating a team
    |--------------------------------------------------------------------------
    */
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
        $team_player_response_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [],
            "is_successful" => true,
        ];

        $team_response_payload = [
            "is_successful" => false,
        ];

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamPlayerService::class, function ($mock) use ($team_player_response_payload) {
            $mock->shouldReceive('findWhere')->andReturn($team_player_response_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | Mock Team Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamService::class, function ($mock) use ($team_response_payload) {
            $mock->shouldReceive('create')->andReturn($team_response_payload);
        });


        /*
        |--------------------------------------------------------------------------
        | when we attempt to get user puzzles
        |--------------------------------------------------------------------------
        */
        $create_team_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post('/api/v1/team.create', [
            "event_id" => Mocks::EVENT_ID->value,
            "team_name" => "agbasgbos",
        ]);

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $create_team_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR->name,
            "response_code" => ResponseCodeEnums::TEAM_SERVICE_REQUEST_ERROR->value,
        ]);
    }
}
