<?php

namespace Tests\Feature\v1\TeamsController\CreateTeam;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Services\TeamService;
use App\Enums\ResponseCodeEnums;
use App\Services\TeamPlayerService;
use App\Enums\ServiceResponseMessageEnum;


class CreateTeam_CASE_4_Test extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Test Case 4 -> for team player service request errors creating team player
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

        $response_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [],
            "is_successful" => true,
        ];

        $createTeamService_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => ["id" => "185ac6ae-9b31-4c33-beaa-1ac84b7e4fc4"],
            "is_successful" => true,
        ];

        $create_teamPlayerService_payload = [
            "status" => ServiceResponseMessageEnum::VALIDATION_ERROR->value,
            "response" => [],
            "is_successful" => false,
        ];

        /*
        |--------------------------------------------------------------------------
        | Mock Team Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamService::class, function ($mock) use ($createTeamService_payload) {
            $mock->shouldReceive('create')->andReturn($createTeamService_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamPlayerService::class, function ($mock) use ($response_payload, $create_teamPlayerService_payload) {
            $mock->shouldReceive('create')->andReturn($create_teamPlayerService_payload);
            $mock->shouldReceive('findWhere')->andReturn($response_payload);
        });

        /*
        |--------------------------------------------------------------------------
        |  when we attempt to get user puzzles
        |--------------------------------------------------------------------------
        */
        $create_team_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post('/api/v1/team.create', [
            "team_name" => "agbasgbos",
            "event_id" => Mocks::EVENT_ID->value,
        ]);

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $create_team_response->assertStatus(200)
            ->assertJson([
                "status" => 400,
                "message" => ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR->name,
                "response_code" => ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR->value,
            ]);
    }
}
