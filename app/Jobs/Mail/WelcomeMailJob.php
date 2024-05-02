<?php

namespace App\Jobs\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
class WelcomeMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public array $payload
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $email_payload = [
            // 'token' => $this->payload['otp'],
            'email' => $this->payload['email'],
            "token"=> $this->payload["push_token"],
        ];

        Mail::send('view.where', $email_payload, function ($message) use ($email_payload) {
            $message->to($email_payload['email']);
            $message->subject('Welcome to the game');
        });
    }
}



