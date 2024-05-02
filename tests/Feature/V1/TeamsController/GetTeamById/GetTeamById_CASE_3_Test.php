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
| Test Case 3 -> check for team player points service request errors
|--------------------------------------------------------------------------
*/
class GetTeamById_CASE_3_Test extends TestCase
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
                    "player_id" => Mocks::EXISTING_USER_PLAYER_ID->value,
                ]
            ],
            "is_successful" => true,
        ];

        $team_player_points_payload = [
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
        $get_team_id_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get("/api/v1/team.getTeamById/{$team_id}");


        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_team_id_response->assertStatus(200)
            ->assertJson([
                'status' => 400,
                'message' => ResponseCodeEnums::TEAM_PLAYER_POINTS_SERVICE_REQUEST_ERROR->name,
                'response_code' => ResponseCodeEnums::TEAM_PLAYER_POINTS_SERVICE_REQUEST_ERROR->value,
            ]);
    }
}
