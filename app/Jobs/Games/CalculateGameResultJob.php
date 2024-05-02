<?php

namespace App\Jobs\Games;

use Illuminate\Bus\Queueable;
use App\Services\CacheService;
use App\Enums\ResponseCodeEnums;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use App\Contracts\PushNotificationContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use SebastianBergmann\Type\FalseType;

class CalculateGameResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /*
    |--------------------------------------------------------------------------
    | Set Variable
    |--------------------------------------------------------------------------
    */
    private string $winner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct
    (
        public array $token,
        public string $session_id
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        CacheService $cacheService,
        PushNotificationContract $pushNotificationContract,
    ) {
        /*
        |--------------------------------------------------------------------------
        | fetch game from redis
        |--------------------------------------------------------------------------
        */
        $find_game_response = $cacheService->getAll("game_sessions:*:*:{$this->session_id}");
        $find_game_response = $find_game_response[0];

        /*
        |--------------------------------------------------------------------------
        | check if game exists
        |--------------------------------------------------------------------------
        */
        if (!count($find_game_response)) {
            consoleLogger([
                "status" => ResponseCodeEnums::GAME_NOT_FOUND,
                "response" => [],
                "is_successful" => false
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | check if game has been completed
        |--------------------------------------------------------------------------
        */
        if (!$find_game_response["completed"]) {
            consoleLogger([
                "status" => ResponseCodeEnums::GAME_IN_PROGRESS,
                "response" => [],
                "is_successful" => true
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | calculate winner
        |--------------------------------------------------------------------------
        */
        $result = count($find_game_response["player_1_games"]) <=> count($find_game_response["player_2_games"]);

        switch ($result) {
            case 1:
                $this->winner = $find_game_response["player_1"];
                break;
            case 0:
                $this->winner = "draw";
                break;
            case -1:
                $this->winner = $find_game_response["player_2"];
                break;
            default:
                $this->winner = null;
                break;
        }

        if($this->winner != null) {
            /*
            |--------------------------------------------------------------------------
            | update the winner in the game session
            |--------------------------------------------------------------------------
            */
            $update_game_response = $cacheService->updateWhere("game_sessions:{$find_game_response['player_1']}:{$find_game_response['player_2']}:{$this->session_id}", ["winner" => $this->winner]);

            /*
            |--------------------------------------------------------------------------
            | check if game has been completed
            |--------------------------------------------------------------------------
            */
            if (!$update_game_response["is_successful"]) {
                consoleLogger([
                    "status" => ResponseCodeEnums::GAME_SERVICE_REQUEST_ERROR,
                    "response" => [],
                    "is_successful" => true
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | set payload
            |--------------------------------------------------------------------------
            */
            $notification_payload = [
                "winner" => $this->winner,
                "session_id" => $this->session_id,
            ];

            /*
            |--------------------------------------------------------------------------
            | send push notification to the players
            |--------------------------------------------------------------------------
            */
            try {
            //      $push_token = $this->cacheService->findWhere("push_tokens:{$request->auth_user["payload"]["id"]}")["push_token"];
            //      $notification_remark = "Schedule is working and payment made successfully at time -> " . now()->format('Y-m-d H:i:s');
            //      $pushNotificationContract
            //         ->setType('Game Notification')
            //         ->setBody("")
            //         ->setIcon('stock_ticker_update')
            //         ->setTokens($this->token)
            //         ->setTitle('Game Result')
            //         ->setPayload($notification_payload)
            //         ->sendNotification();
            // } catch (\Throwable $th) {
            //     consoleLogger([
            //         "status" => ResponseCodeEnums::USER_PUSH_NOTIFICATION_ERROR,
            //         "response" => [],
            //         "is_successful" => false
            //     ]);
            // }
        }
    }
}
