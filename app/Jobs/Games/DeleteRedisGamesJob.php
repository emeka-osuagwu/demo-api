<?php

namespace App\Jobs\Games;

/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Services\CacheService;
use App\Enums\ResponseCodeEnums;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Providers\CacheProvider;

/*
|--------------------------------------------------------------------------
| Services Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class DeleteRedisGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
    CacheService $cacheService,
    CacheProvider $cacheProvider,
    )
    {
        /*
        |--------------------------------------------------------------------------
        | fetch games from redis
        |--------------------------------------------------------------------------
        */
        $find_games = $cacheProvider->getKeys("game_sessions:*");
        dd($find_games);
        foreach ($find_games as $game) {
            $fields = explode("_", $game);
            $data = $cacheService->findWhere("{$fields[2]}_{$fields[3]}");

            /*
            |--------------------------------------------------------------------------
            | check if player_2 has not accepted and if its more than 24 hours
            |--------------------------------------------------------------------------
            */
            if(!$data["challenge_accepted"] && Carbon::parse($data["created_at"])->addHours(24)->isPast()){
            $cacheService->deleteWhere("{$fields[2]}_{$fields[3]}");
            consoleLogger([
                "status" => ResponseCodeEnums::GAME_REQUEST_SUCCESSFUL,
                "response" =>[],
                "is_successful" => true
            ]);
            }
        }
    }
}
