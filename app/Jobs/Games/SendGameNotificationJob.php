<?php

namespace App\Jobs\Games;

use App\Contracts\PushNotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGameNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct
    (
        public array $payload
    ){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        PushNotificationContract $pushNotificationContract,
    )
    {
        /*
        |--------------------------------------------------------------------------
        | send push notification got games
        |--------------------------------------------------------------------------
        */
        $pushNotificationContract
            ->setType('Game Notification')
            ->setBody($this->payload["notification_remark"])
            ->setIcon('stock_ticker_update')
            ->setTokens($this->payload["push_token"])
            ->setTitle('Game Update')
            ->setPayload([])
            ->sendNotification();
    }
}
