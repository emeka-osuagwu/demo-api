<?php

namespace App\Services;

use Throwable;
use App\Enums\ServiceResponseMessageEnum;
use App\Services\Providers\CacheProvider;

class CacheService
{
    protected $key;

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function __construct
    (
        protected CacheProvider $cacheProvider,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | add Comment
    |--------------------------------------------------------------------------
    */
    public function getAll($selector): array
    {
        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $keys = $this->cacheProvider->getKeys($selector);
        $payload = [];

        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        foreach ($keys as $key => $value) {
            $value = removePrefix($value, env('REDIS_PREFIX'));
            try {
                $payload[$key] = $this->cacheProvider->setKey($value)->readRecord();
            } catch (Throwable $exception) {
                report($exception);
                return [
                    "status" => ServiceResponseMessageEnum::CACHE_SERVICE_PROVIDER_ERROR->value,
                    "response" => $exception->getMessage(),
                    "is_successful" => false,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | check if payload is empty
        |--------------------------------------------------------------------------
        */
        if (count($payload) < 1) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                "response" => $payload,
                "is_successful" => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            "response" => $payload,
            "is_successful" => true,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | save record on redis
    |--------------------------------------------------------------------------
    */
    public function saveRecord(string $selector, array $payload): array
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | get record
            |--------------------------------------------------------------------------
            */
            $keys = $this->cacheProvider->setKey($selector)->setRecord($payload);
            
            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $payload = [$keys];

            /*
            |--------------------------------------------------------------------------
            | check if payload is empty
            |--------------------------------------------------------------------------
            */
            if (count($payload) < 1) {
                return [
                    "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                    "response" => $payload,
                    "is_successful" => false,
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | send success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                "response" => $payload,
                "is_successful" => true,
            ];
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::CACHE_SERVICE_PROVIDER_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | find and update
    |--------------------------------------------------------------------------
    */
    public function updateWhere(string $selector, array $payload): array
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | update record
            |--------------------------------------------------------------------------
            */
            $keys = $this->cacheProvider->setKey($selector)->updateRecord($payload);
            
            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $payload = [$keys];
    
            /*
            |--------------------------------------------------------------------------
            | send success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                "response" => $payload,
                "is_successful" => true,
            ];
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::CACHE_SERVICE_PROVIDER_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function findWhere(string $selector): array
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | get record
            |--------------------------------------------------------------------------
            */
            $payload = $this->cacheProvider->setKey($selector)->readRecord();
            
            /*
            |--------------------------------------------------------------------------
            | check if payload is empty
            |--------------------------------------------------------------------------
            */
            if (count($payload) < 1) {
                return [
                    "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                    "response" => $payload,
                    "is_successful" => false,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | send success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                "response" => $payload,
                "is_successful" => true,
            ];
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::CACHE_SERVICE_PROVIDER_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | find and delete
    |--------------------------------------------------------------------------
    */
    public function deleteWhere(string $selector): array
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | delete record on redis
            |--------------------------------------------------------------------------
            */
            $this->cacheProvider->setKey($selector)->deleteRecord();
            
            /*
            |--------------------------------------------------------------------------
            | send success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                "response" => [],
                "is_successful" => true,
            ];
        } catch (Throwable $exception) {
            report($exception);
            return [
                "status" => ServiceResponseMessageEnum::CACHE_SERVICE_PROVIDER_ERROR->value,
                "response" => $exception->getMessage(),
                "is_successful" => false,
            ];
        }
    }
}
