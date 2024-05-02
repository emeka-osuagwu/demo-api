<?php

namespace App\Enums\ResponseCode;

enum AuthResponseCode: int
{
    case AUTH_USER_REQUEST_SUCCESSFUL = 1000;
    case AUTH_USER_REQUEST_FAILED = 1001;
    case AUTH_USER_REQUEST_VALIDATION_ERROR = 1002;
    CASE AUTH_USER_SERVICE_VALIDATION_ERROR = 1003;
    case AUTH_USER_NOT_FOUND = 1004;
    case AUTH_USER_REQUEST_ERROR = 1005;
    case INVALID_AUTHORIZATION = 1006;
    case AUTH_USER_SERVICE_REQUEST_ERROR = 1007;
    case INVALID_LOGIN_CREDENTIALS = 1008;



    public function toString()
    {
        return match ($this) {
            self::AUTH_USER_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name,
            ],
            self::AUTH_USER_REQUEST_FAILED => [
                'status' => 400,
                'message' => $this->name,
                'response_code' => $this
            ],
            self::AUTH_USER_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::AUTH_USER_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::AUTH_USER_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::AUTH_USER_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::INVALID_AUTHORIZATION => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::AUTH_USER_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::INVALID_LOGIN_CREDENTIALS => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
        };
    }
}
