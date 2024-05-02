<?php

namespace App\Contracts;

interface FirestoreContract
{
    /*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function getFirestore();

    /*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function updateRecord(string $collection, string $documentId, array $payload): array;

    /*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function delete(string $collection, string $document_id): array;

    /*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function readRecord(string $collection, string $document_id): array;

    /*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function findWhere(string $collection, string $field, string $value): array;

    /*
    |--------------------------------------------------------------------------
    | Add comment
    |--------------------------------------------------------------------------
    */
    public function create(string $collection, array $payload): array;
}
