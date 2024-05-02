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

/*
|--------------------------------------------------------------------------
| Test Case 3 -> team with provided id not found
|--------------------------------------------------------------------------
*/
class RemoveTeamPlayer_CASE_3_Test extends TestCase
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
        $player_id = Mocks::INVALID_PLAYER_ID->value;

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
            'message' => ResponseCodeEnums::TEAM_PLAYER_NOT_FOUND->name,
            'response_code' => ResponseCodeEnums::TEAM_PLAYER_NOT_FOUND->value,
        ]);
    }
}
