<?php

namespace App\Jobs\Games;


/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use App\Services\GameService;
use App\Services\UserService;
use App\Services\CacheService;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

/*
|--------------------------------------------------------------------------
| Enums Namespace
|--------------------------------------------------------------------------
*/
use App\Enums\ResponseCodeEnums;
use App\Enums\ServiceResponseMessageEnum;

class SaveRedisGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /*
    |--------------------------------------------------------------------------
    | Save Redis Games
    |--------------------------------------------------------------------------
    */
    public function handle(
        GameService $gameService,
        UserService $userService,
        CacheService $cacheService,
    ) {
        /*
        |--------------------------------------------------------------------------
        | fetch games from redis
        |--------------------------------------------------------------------------
        */
        $find_games = $cacheService->getAll("game_sessions:*");

        /*
        |--------------------------------------------------------------------------
        | filter the completed games
        |--------------------------------------------------------------------------
        */
        foreach ($find_games as $value) {

            /*
            |--------------------------------------------------------------------------
            | check if the game is completed
            |--------------------------------------------------------------------------
            */
            if ($value["completed"]) {
                /*
                |--------------------------------------------------------------------------
                | find player 1
                |--------------------------------------------------------------------------
                */
                $find_player_1_response = $userService->findWhere(["player_id" => $value["player_1"]]);

                /*
                |--------------------------------------------------------------------------
                | check if service request fails
                |--------------------------------------------------------------------------
                */
                if (!$find_player_1_response["is_successful"] && $find_player_1_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value) {
                   return [
                        "status" => ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR,
                        "response" => [],
                        "is_successful" => false
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | check if user not found
                |--------------------------------------------------------------------------
                */
                if ($find_player_1_response["is_successful"] && !count($find_player_1_response["response"])) {
                    return [
                        "status" => ResponseCodeEnums::USER_NOT_FOUND,
                        "response" => [],
                        "is_successful" => false
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | find player 2
                |--------------------------------------------------------------------------
                */
                $find_player_2_response = $userService->findWhere(["player_id" => $value["player_2"]]);

                /*
                |--------------------------------------------------------------------------
                | check if service request fails
                |--------------------------------------------------------------------------
                */
                if (!$find_player_2_response["is_successful"] && $find_player_2_response["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value) {
                    return [
                        "status" => ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR,
                        "response" => [],
                        "is_successful" => false
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | check if user not found
                |--------------------------------------------------------------------------
                */
                if ($find_player_2_response["is_successful"] && !count($find_player_2_response["response"])) {
                    return [
                        "status" => ResponseCodeEnums::USER_NOT_FOUND,
                        "response" => [],
                        "is_successful" => false
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | check for winner
                |--------------------------------------------------------------------------
                */
                if($value["winner"] === $value["player_1"]){
                    /*
                    |--------------------------------------------------------------------------
                    | set payload
                    |--------------------------------------------------------------------------
                    */
                    $update_user_payload =
                    [
                        "padi_play_wins" => $find_player_1_response['response'][0]["padi_play_wins"] === "null"
                        ? strval(null + 1)
                        : strval((int) $find_player_1_response['response'][0]["padi_play_wins"] + 1)
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | update winner
                    |--------------------------------------------------------------------------
                    */
                    $player_1_wins = $userService->update("player_id='{$value["player_1"]}'", $update_user_payload);

                    /*
                    |--------------------------------------------------------------------------
                    | check id request fails
                    |--------------------------------------------------------------------------
                    */
                    if(!$player_1_wins["is_successful"] && $player_1_wins["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value){
                        return [
                            "status" => ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR,
                            "response" => [],
                            "is_successful" => false
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | set payload
                    |--------------------------------------------------------------------------
                    */
                    $update_user_payload =
                    [
                        "padi_play_losses" => $find_player_2_response['response'][0]["padi_play_losses"] === "null"
                        ? strval(null + 1)
                        : strval((int) $find_player_2_response['response'][0]["padi_play_losses"] + 1)
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | update loser
                    |--------------------------------------------------------------------------
                    */
                    $player_2_loss = $userService->update("player_id='{$value["player_2"]}'", $update_user_payload);

                    /*
                    |--------------------------------------------------------------------------
                    | check id request fails
                    |--------------------------------------------------------------------------
                    */
                    if(!$player_2_loss["is_successful"] && $player_2_loss["status"] === ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value){
                        return [
                            "status" => ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR,
                            "response" => [],
                            "is_successful" => false
                        ];
                    }

                }

                if($value["winner"] === $value["player_2"]){
                    /*
                    |--------------------------------------------------------------------------
                    | set playload
                    |--------------------------------------------------------------------------
                    */
                    $update_user_payload =
                    [
                        "padi_play_wins" => $find_player_2_response['response'][0]["padi_play_wins"] === "null"
                        ? strval(null + 1)
                        : strval((int) $find_player_2_response['response'][0]["padi_play_wins"] + 1)
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | update winner
                    |--------------------------------------------------------------------------
                    */
                    $player_2_wins = $userService->update("player_id='{$value["player_2"]}'", $update_user_payload);

                    /*
                    |--------------------------------------------------------------------------
                    | check id request fails
                    |--------------------------------------------------------------------------
                    */
                    if(!$player_2_wins["is_successful"] && $player_2_wins["status"] === ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value){
                        return [
                            "status" => ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR,
                            "response" => [],
                            "is_successful" => false
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | set playload
                    |--------------------------------------------------------------------------
                    */
                    $update_user_payload =
                    [
                        "padi_play_losses" => $find_player_1_response['response'][0]["padi_play_losses"] === "null"
                        ? strval(null + 1)
                        : strval((int) $find_player_1_response['response'][0]["padi_play_losses"] + 1)
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | update loser
                    |--------------------------------------------------------------------------
                    */
                    $player_1_loss = $userService->update("player_id='{$value["player_1"]}'", $update_user_payload);

                    /*
                    |--------------------------------------------------------------------------
                    | check id request fails
                    |--------------------------------------------------------------------------
                    */
                    if(!$player_1_loss["is_successful"] && $player_1_loss["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value){
                        return [
                            "status" => ResponseCodeEnums::USER_SERVICE_REQUEST_ERROR,
                            "response" => [],
                            "is_successful" => false
                        ];
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | remove the puzzles
                |--------------------------------------------------------------------------
                */
                unset($value["puzzles"]);

                /*
                |--------------------------------------------------------------------------
                | generate an id for the game
                |--------------------------------------------------------------------------
                */
                $value["id"] = generateUUID();

                /*
                |--------------------------------------------------------------------------
                | save to bigquery
                |--------------------------------------------------------------------------
                */
                $create_game = $gameService->create($value);

                /*
                |--------------------------------------------------------------------------
                | if validation error
                |--------------------------------------------------------------------------
                */
                if ($create_game["status"] == ServiceResponseMessageEnum::VALIDATION_ERROR->value && !$create_game["is_successful"]) {
                    return [
                        "status" => ResponseCodeEnums::GAME_SERVICE_VALIDATION_ERROR,
                        "response" => $create_game["response"],
                        "is_successful" => false
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | if request fails
                |--------------------------------------------------------------------------
                */
                if ($create_game["status"] == ServiceResponseMessageEnum::BIG_QUERY_PROVIDER_SERVICE_ERROR->value && !$create_game["is_successful"]) {
                    return [
                        "status" => ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR,
                        "response" => [],
                        "is_successful" => false
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | delete from redis after the game is saved
                |--------------------------------------------------------------------------
                */
                if ($create_game["status"] == ServiceResponseMessageEnum::SUCCESSFUL->value && $create_game["is_successful"]) {
                    /*
                    |--------------------------------------------------------------------------
                    | generate game session query
                    |--------------------------------------------------------------------------
                    */
                    $query = "game_sessions:{$value['player_1']}:{$value['player_2']}:{$value['session_id']}";

                    /*
                    |--------------------------------------------------------------------------
                    | delete the session from redis
                    |--------------------------------------------------------------------------
                    */
                    $cacheService->deleteWhere($query);
                }
            }
        }
    }
}
