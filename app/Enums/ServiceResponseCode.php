<?php

namespace App\Enums;

enum ServiceResponseCode: int
{
    case DEFAULT_LOG = 0000;

    // BIGQUERY SERVICE
    case BIGQUERY_QUERY_BUILDER_ERROR = 1001;
    case BIGQUERY_BAD_REQUEST_ERROR = 1002;
    case BIGQUERY_SERVICE_ERROR = 1003;

    case DICTIONARY_VALIDATION_ERROR = 2001;

    case USER_SERVICE_VALIDATION_ERROR = 3001;

    case FIREBASE_REQUEST_FAILED = 4001;

    public function getCode()
    {
        return $this;
    }

    // log types
    // 0 info logs
    // 1 warning logs
    // 2 emergency logs
    public function getMeta()
    {
        $level1 = 'info';
        $level2 = 'warning';
        $level3 = 'emergency';

        return match ($this) {

            // CORE BANKING SERVICE
            self::DEFAULT_LOG => [
                'log_type' => $level1,
            ],

            // BIGQUERY SERVICE
            self::BIGQUERY_QUERY_BUILDER_ERROR => [
                'log_type' => $level3
            ],
            self::BIGQUERY_BAD_REQUEST_ERROR => [
                'log_type' => $level2
            ],
            self::BIGQUERY_SERVICE_ERROR => [
                'log_type' => $level3
            ],
            self::DICTIONARY_VALIDATION_ERROR => [
                'log_type' => $level3
            ],
            self::USER_SERVICE_VALIDATION_ERROR => [
                'log_type' => $level3
            ],
            self::FIREBASE_REQUEST_FAILED => [
                'log_type'=> $level3
            ]
        };
    }
}
