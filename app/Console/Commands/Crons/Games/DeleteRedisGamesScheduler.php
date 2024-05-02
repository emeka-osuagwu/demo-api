<?php

namespace App\Console\Commands\Crons\Games;

use Illuminate\Console\Command;
use App\Jobs\Games\DeleteRedisGamesJob;

class DeleteRedisGamesScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:deleteRedisGamesSchedulere';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete unsuccessful game sessions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return DeleteRedisGamesJob::dispatchSync();
    }
}
