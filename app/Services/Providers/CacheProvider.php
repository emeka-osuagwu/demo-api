<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Redis;

class CacheProvider
{
    protected $key;
    protected $cacheProvider;

    /*
    |--------------------------------------------------------------------------
    | add Comment
    |--------------------------------------------------------------------------
    */
    function readRecord(): array
    {
        return json_decode(Redis::get($this->key), true) ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | add Comment
    |--------------------------------------------------------------------------
    */
    function deleteRecord(): void
    {
        Redis::del($this->key);
    }

    /*
    |--------------------------------------------------------------------------
    | add Comment
    |--------------------------------------------------------------------------
    */
    function getKeys(string $key): array
    {
        return Redis::keys($key);
    }

    /*
    |--------------------------------------------------------------------------
    | add Comment
    |--------------------------------------------------------------------------
    */
    function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

   /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function updateRecord(array $payload)
    {
        $existing_data = $this->readRecord();

        $updated_data = array_merge($existing_data, $payload);

        Redis::set($this->key, json_encode($updated_data));
    }

    /*
    |--------------------------------------------------------------------------
    | Add Comment
    |--------------------------------------------------------------------------
    */
    public function setRecord($payload)
    {
        Redis::set($this->key, json_encode($payload));
    }
}
