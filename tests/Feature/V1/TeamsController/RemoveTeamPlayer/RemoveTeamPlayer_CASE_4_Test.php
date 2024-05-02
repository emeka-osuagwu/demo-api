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
use App\Services\TeamPlayerService;
use App\Services\TeamService;

/*
|--------------------------------------------------------------------------
| Test Case 4 -> test only admin can remove team player
|--------------------------------------------------------------------------
*/
class RemoveTeamPlayer_CASE_4_Test extends TestCase
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
        $player_id = 'jnefnfn';
        
        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $find_team_response = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [
               [ 
                    "id" => "f2295d63-8880-46c8-a0aa-8a880ebb9099",
                    "team_name" => "agbasgbos",
                    "owner_id" => null,
                    "event_id" => "c69428ee-888d-45d2-bc19-dfa26ba7e397",
                    "created_at" => "2024-04-26 11:00:23",
                    "updated_at" => "2024-04-26 11:00:23"
                ]
            ],
            "is_successful" => true,
        ];
        
        $find_team_player_response = [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => [],
            "is_successful" => true,
        ];

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
        | Mock Team Service
        |--------------------------------------------------------------------------
        */
        $this->mock(TeamPlayerService::class, function ($mock) use ($find_team_player_response) {
            $mock->shouldReceive('findWhere')->andReturn($find_team_player_response);
        });

        /*
        |--------------------------------------------------------------------------
        | when we attempt get user puzzles
        |--------------------------------------------------------------------------
        */
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/team.removePlayer", [
            "team_id" => $team_name,
            "player_id" => $player_id
        ]);


        $response->assertStatus(200)
        ->assertJson([
            'status' => 400,
            'message' => ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN->name,
            'response_code' => ResponseCodeEnums::AUTH_USER_IS_NOT_A_TEAM_ADMIN->value,
        ]);
    }
}
