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
| Test Case 4 -> check if team is not found in database
|--------------------------------------------------------------------------
*/
class  AcceptPlayer_CASE_4_Test extends TestCase
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

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamPlayerService::class, function ($mock) use ($response_payload) {
            $mock->shouldReceive('findWhere')->andReturn($response_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(CacheService::class, function ($mock) use ($team_invitation_payload) {
            $mock->shouldReceive('findWhere')->andReturn($team_invitation_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | Mock Team Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamService::class, function ($mock) use ($response_payload) {
            $mock->shouldReceive('findWhere')->andReturn($response_payload);
        });

        /*
        |--------------------------------------------------------------------------
        | when we attempt to accept player
        |--------------------------------------------------------------------------
        */
        $get_accept_player_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.acceptInvite");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */

        $get_accept_player_response
        ->assertStatus(200)
        ->assertJson([
            'status' => 400,
            'message' => ResponseCodeEnums::TEAM_NOT_FOUND->name,
            'response_code' => ResponseCodeEnums::TEAM_NOT_FOUND->value,
        ]);

        $this->assertEmpty($get_accept_player_response['data']);
    }
}
