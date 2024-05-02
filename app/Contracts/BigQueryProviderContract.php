<?php

namespace App\Contracts;

interface BigQueryProviderContract
{
    public function toJson(): object | array;
    public function query(string $statement): object | array;
}
