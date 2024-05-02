<?php

namespace App\Services\Providers;

use App\Enums\Mocks;
use App\Contracts\FirestoreContract;
use App\Enums\ServiceResponseMessageEnum;

class FirestoreProviderMock implements FirestoreContract
{
    protected $database;

	/*
    |--------------------------------------------------------------------------
    | Get Frestore
    |--------------------------------------------------------------------------
    */
    public function getFirestore()
    {
        return $this->database;
    }

	/*
    |--------------------------------------------------------------------------
    | Update a record in a Firestore collection by document ID
    |--------------------------------------------------------------------------
    */
    public function updateRecord(string $collection, string $document_id, array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | check if payload is empty
        |--------------------------------------------------------------------------
        */
        if (!count($payload)) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                'response' => [],
                'is_successful' => false
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | record not found
        |--------------------------------------------------------------------------
        */
        if ($document_id === Mocks::EMPTY_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                'response' => [],
                'is_successful' => false
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | fail response
        |--------------------------------------------------------------------------
        */
        if ($document_id === Mocks::INVALID_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            'response' => $payload,
            'is_successful' => true
        ];
    }

	/*
    |--------------------------------------------------------------------------
    | Delete a record from a Firestore collection by document ID
    |--------------------------------------------------------------------------
    */
    public function delete(string $collection, string $document_id): array
    {
        /*
        |--------------------------------------------------------------------------
        | record not found
        |--------------------------------------------------------------------------
        */
        if ($document_id === Mocks::EMPTY_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                'response' => [],
                'is_successful' => false
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | fail response
        |--------------------------------------------------------------------------
        */
        if ($document_id === Mocks::INVALID_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            'response' => [],
            'is_successful' => true
        ];
    }

	/*
    |--------------------------------------------------------------------------
    | Read a record from a Firestore collection by document ID
    |--------------------------------------------------------------------------
    */
    public function readRecord(string $collection, string $document_id): array
    {        
        /*
        |--------------------------------------------------------------------------
        | record not found
        |--------------------------------------------------------------------------
        */
        if ($document_id === Mocks::EMPTY_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | fail response
        |--------------------------------------------------------------------------
        */
        if ($document_id === Mocks::INVALID_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            'response' => [
                'id' => '123456789',
                'name' => 'Quadri',
                'email' => 'example@gmail.com',
                'phone' => '08123456789',
            ],
            'is_successful' => true
        ];
    }

	/*
    |--------------------------------------------------------------------------
    | find where
    |--------------------------------------------------------------------------
    */
    public function findWhere(string $collection, string $field, string $value): array
    {        
        /*
        |--------------------------------------------------------------------------
        | record not found
        |--------------------------------------------------------------------------
        */
        if ($value === Mocks::EMPTY_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | fail response
        |--------------------------------------------------------------------------
        */
        if ($value === Mocks::INVALID_FIRESTORE_ID->value) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            'response' => [
                [
                    'id' => '123456789',
                    'name' => 'Quadri',
                    'email' => 'example@gmail.com',
                    'phone' => '08123456789',
                ]
            ],
            'is_successful' => true
        ];
    }

	/*
	|--------------------------------------------------------------------------
	| Create a new record in a Firestore collection
	|--------------------------------------------------------------------------
	*/
    public function create(string $collection, array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | fail response
        |--------------------------------------------------------------------------
        */
        if ($collection === Mocks::INVALID_FIRESTORE_COLLECTION->value) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => [],
                'is_successful' => false
            ];
        }
        
        /*
        |--------------------------------------------------------------------------
        | send success response
        |--------------------------------------------------------------------------
        */
        return [
            "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
            'response' =>  [
                'id' => '123456789',
                'name' => 'Quadri',
                'email' => 'example@gmail.com',
                'phone' => '08123456789',
            ],
            'is_successful' => true
        ];
    }
}
