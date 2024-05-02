<?php

namespace Tests\Feature\v1\UserController\GetUserByPlayerId;

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\Mocks;
use App\Enums\ResponseCode\AuthResponseCode;

/*
|--------------------------------------------------------------------------
| Test Case 1 -> test getUserByPlayerId for invalid authorization
|--------------------------------------------------------------------------
*/
class GetUserByPlayerId_CASE_1_Test extends TestCase
{

    public function test_case()
    {
        /*
        |--------------------------------------------------------------------------
        | set variables
        |--------------------------------------------------------------------------
        */
        $player_id = Mocks::EXISTING_USER_PLAYER_ID->value;
        $get_profile_response = $this->get("/api/v1/user.getUserByPlayerId/{$player_id}");

        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $get_profile_response->assertStatus(200)
        ->assertJson([
            "status" => 400,
            "message" => AuthResponseCode::INVALID_AUTHORIZATION->name,
            "response_code" => AuthResponseCode::INVALID_AUTHORIZATION->value,
        ]);
    }
}
