<?php

namespace Tests\Feature\v1\GameController\AcceptInvite;

use Tests\TestCase;
use App\Enums\Mocks;
use App\Services\CacheService;
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

class AcceptInvite_CASE_2_Test extends TestCase
{
    /*
     |--------------------------------------------------------------------------
     | Test Case 2 -> test for game not found
     |--------------------------------------------------------------------------
     */
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
        $cache_service_payload = [
            "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
            "response" => [],
            "is_successful" => false,
        ];

        /*
        |--------------------------------------------------------------------------
        | get user profile
        |--------------------------------------------------------------------------
        */
        $accept_player_response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->post("/api/v1/game.acceptInvite",["session_id" => "1j2k34"]);

        /*
        |--------------------------------------------------------------------------
        | Mock Team Player Service
        |--------------------------------------------------------------------------
        */
        $this->mock(CacheService::class, function ($mock) use ($cache_service_payload) {
            $mock->shouldReceive('getAll')->andReturn($cache_service_payload);
        });


        /*
        |--------------------------------------------------------------------------
        | expected response
        |--------------------------------------------------------------------------
        */
        $accept_player_response->assertStatus(200)
            ->assertJson([
                "status" => 400,
                "message" => ResponseCodeEnums::GAME_NOT_FOUND->name,
                "response_code" => ResponseCodeEnums::GAME_NOT_FOUND->value,
                "data" => [],
            ]);
    }
}
