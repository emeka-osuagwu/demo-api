<?php

namespace App\Console\Commands\Crons\Games;

use Illuminate\Console\Command;
use App\Jobs\Games\SaveRedisGamesJob;

class SaveRedisGamesScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:saveRedisGamesScheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save games that is cached from redis to bigquery';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return SaveRedisGamesJob::dispatchSync();
    }
}
