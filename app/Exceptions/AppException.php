<?php

namespace App\Exceptions;

use Log;
use Exception;
use App\Models\User;
use App\Enums\ServiceResponseCode;
use App\Events\Logs\ProcessLogEvent;
use App\Notifications\SofriLogNotification;

class AppException extends Exception
{
    protected $data;
    protected $payload;
    protected $service;
    protected $context_code;

    public function __construct
    (
        $service,
        $message,
        $context_code = ServiceResponseCode::DEFAULT_LOG, object | array $payload = null
    )
	{
        $this->message = $message;
        $this->service = $service;
        $this->payload = $payload;
        $this->context_code = $context_code;
	}

    /**
     * Get the exception's context information.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context_code->value ?? '000';
    }

    public function getMeta()
    {
        return $this->context_code->getMeta();
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        $payload = [
            'env' => env('APP_ENV'),
            'context' => $this->getContext(),
            'client' => request()->ip(),
            'endpoint' => request()->url(),
            'service' => $this->service,
            'message' => $this->message,
            'line' => $this->getLine(),
            'file' => $this->getFile(),
            'date' => now()->toDateTimeString(),
            'payload' => $this->payload,
            'initiator' => request()->user() ? request()->user()->id : null
        ];

        // consoleLogger($payload);
    }
}
