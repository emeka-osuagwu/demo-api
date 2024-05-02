<?php

namespace App\Services\Providers;

use Kreait\Firebase\Factory;
use App\Exceptions\AppException;
use App\Enums\ServiceResponseCode;
use App\Contracts\PushNotificationContract;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\FirebaseMessaging;


class FirebaseNotificationProvider implements PushNotificationContract
{
    /*
    |--------------------------------------------------------------------------
    | Set Variables
    |--------------------------------------------------------------------------
    */
    protected $type;
    protected $body;
    protected $icon;
    protected $sound;
    protected $title;
    protected $badge;
    protected $token;
    protected $payload;
    protected $channel;

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setSound(string $sound): self
    {
        $this->sound = $sound;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setBadge(string $badge): self
    {
        $this->badge = $badge;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setChannelId(string $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

	/*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function setTokens(array $token): self
    {
        $this->token = $token;
        return $this;
    }

	/*
	|--------------------------------------------------------------------------
	| Add Comment
	|--------------------------------------------------------------------------
	*/
    public function sendNotification()
    {
        $factory = (new Factory)->withServiceAccount(getFirebaseCreds());
        $messaging = $factory->createMessaging();

        $message = [
            'notification' => [
                "body" => $this->body ?? '',
				"icon" => $this->icon ?? '',
				"sound" => $this->sound ?? '',
				"title" => $this->title ?? '',
			],
			'data' => [
                'type' => $this->type ?? '',
				'payload' => isset($this->payload) ? json_encode($this->payload) : json_encode([])
            ]
        ];

        try {
            $messaging->sendMulticast($message, $this->token);
        } catch (\Exception $exception) {
            throw new AppException(
				'FirebaseNotificationProvider@sendNotification',
				$exception->getMessage(),
				ServiceResponseCode::FIREBASE_REQUEST_FAILED
			);
        }
    }
}
