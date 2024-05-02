<?php

namespace Tests\Feature\v1\UserController\Profile;

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
| Test Case 1 -> test user.profile endpoint
|--------------------------------------------------------------------------
*/
class Profile_CASE_1_Test extends TestCase
{

    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | login request
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
        | set variable
        |--------------------------------------------------------------------------
        */
        $token = $login_response['data']['token'];

        /*
        |--------------------------------------------------------------------------
        | get user profile
        |--------------------------------------------------------------------------
        */
        $get_user_profile_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get("/api/v1/user.profile");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_user_profile_response->assertStatus(200)
        ->assertJson([
            "status" => 200,
            "message" => ResponseCodeEnums::USER_REQUEST_SUCCESSFUL->name,
            "response_code" => ResponseCodeEnums::USER_REQUEST_SUCCESSFUL->value,
        ])
        ->assertJsonStructure([
            "data" => [
                'id',
                'juju',
                'begi',
                'jara',
                'level',
                "email",
                'score',
                "totem",
                'points',
                'cowries',
                'game_won',
                'player_id',
                "full_name",
                'device_id',
                'giraffing',
                'updated_at',
                'game_played',
                'highest_score',
                'average_score',
                'longest_streak',
                'current_streak',
                "padi_play_wins",
                "padi_play_losses",
                "completed_puzzles",
                "completed_puzzle_levels"
                ]
            ]);
    }
}
