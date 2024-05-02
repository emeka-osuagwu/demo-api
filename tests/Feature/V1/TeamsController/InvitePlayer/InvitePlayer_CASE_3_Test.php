<?php

namespace Tests\Feature\v1\TeamsController\InvitePlayer;

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
use App\Services\TeamPlayerService;

/*
|--------------------------------------------------------------------------
| Test Case 2 -> check if the team player is not an admin
|--------------------------------------------------------------------------
*/
class InvitePlayer_CASE_3_Test extends TestCase
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
        $team_name = "gbasgbos";
        $player_id = Mocks::EXISTING_USER_PLAYER_ID->value;

        $team_player_response_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [],
            "is_successful" => true,
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
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.invitePlayer", [
            "team_name" => $team_name,
            "player_id" => $player_id
        ]);

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)
        ->assertJson([
            'status' => 400,
            'message' => ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN->name,
            'response_code' => ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN->value,
        ]);
    }
}
