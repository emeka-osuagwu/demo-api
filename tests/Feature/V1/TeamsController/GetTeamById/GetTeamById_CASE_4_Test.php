<?php

namespace Tests\Feature\v1\TeamsController\GetTeamById;

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
use App\Services\TeamPlayerPointService;

/*
|--------------------------------------------------------------------------
| Test Case 4 -> check for TeamById successful request
|--------------------------------------------------------------------------
*/
class GetTeamById_CASE_4_Test extends TestCase
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
        $team_id = "1111111";


        $team_player_response_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [
                [
                    // "id" => generateUUID(),
                    "team_id" => Mocks::INVALID_TEAM_ID->value,
                    "player_id" => Mocks::EXISTING_USER_PLAYER_ID->value,
                    "is_admin" => "1", //"null"
                    "created_at" => now()->toDateTimeString(),
                    "updated_at" => now()->toDateTimeString(),
                ]
            ],
            "is_successful" => true,
        ];

        $team_player_points_payload = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [
                [
                    // "id" => generateUUID(),
                    "points" => "100",
                    "team_id" => Mocks::INVALID_TEAM_ID->value,
                    "player_id" => Mocks::EXISTING_USER_PLAYER_ID->value,
                    "created_at" => now()->toDateTimeString(),
                    "updated_at" => now()->toDateTimeString(),

                ]
            ],
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
       | Mock Team Player Point Service
       |--------------------------------------------------------------------------
       */
        $this->mock(TeamPlayerPointService::class, function ($mock) use ($team_player_points_payload) {
            $mock->shouldReceive('findWhere')->andReturn($team_player_points_payload);
        });


        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get("/api/v1/team.getTeamById/{$team_id}");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_user_puzzle_response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL->name,
                'response_code' => ResponseCodeEnums::TEAM_REQUEST_SUCCESSFUL->value,
            ]);
    }
}
