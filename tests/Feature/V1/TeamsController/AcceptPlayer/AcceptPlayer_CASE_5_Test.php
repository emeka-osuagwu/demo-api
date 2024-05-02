<?php

namespace Tests\Feature\v1\TeamsController\AcceptPlayer;


use Tests\TestCase;
use App\Enums\Mocks;
use App\Services\TeamService;
use App\Services\CacheService;
use App\Enums\ResponseCodeEnums;
use App\Services\TeamPlayerService;
use App\Enums\ServiceResponseMessageEnum;

/*
|--------------------------------------------------------------------------
| Test Case 5 -> test create team player request error
|--------------------------------------------------------------------------
*/
class AcceptPlayer_CASE_5_Test extends TestCase
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
            "full_name" => Mocks::FULL_NAME->value,
            "authorization_token" => Mocks::INVALID_AUTHORIZATION_CODE->value,
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
        $team_invitation_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [
                "team_id" => generateUUID(),
                "is_admin" => false,
                "player_id" => Mocks::EXISTING_USER_PLAYER_ID->value,
            ],
            "is_successful" => true,
        ];
        $find_team_response = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [
                "id" => "f2295d63-8880-46c8-a0aa-8a880ebb9099",
                "team_name" => "agbasgbos",
                "owner_id" => null,
                "event_id" => "c69428ee-888d-45d2-bc19-dfa26ba7e397",
                "created_at" => "2024-04-26 11:00:23",
                "updated_at" => "2024-04-26 11:00:23"
            ],
            "is_successful" => true,
        ];
        $create_teamPlayerService_payload = [
            "status" => ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value,
            "response" => [],
            "is_successful" => false,
        ];
        $delete_team_invitation_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [],
            "is_successful" => true,
        ];

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamPlayerService::class, function ($mock) use ($response_payload, $create_teamPlayerService_payload) {
            $mock->shouldReceive('findWhere')->andReturn($response_payload);
            $mock->shouldReceive('create')->andReturn($create_teamPlayerService_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(CacheService::class, function ($mock) use ($team_invitation_payload, $delete_team_invitation_payload) {
            $mock->shouldReceive('findWhere')->andReturn($team_invitation_payload);
            $mock->shouldReceive('deleteWhere')->andReturn($delete_team_invitation_payload);
        });


        /*
        |--------------------------------------------------------------------------
        | Mock Team Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamService::class, function ($mock) use ($find_team_response) {
            $mock->shouldReceive('findWhere')->andReturn($find_team_response);
        });

        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.acceptInvite");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response
        ->assertStatus(200)
        ->assertJson([
            'status' => 400,
            'message' => ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR->name,
            'response_code' => ResponseCodeEnums::TEAM_PLAYER_SERVICE_REQUEST_ERROR->value,
        ]);
    }
}
