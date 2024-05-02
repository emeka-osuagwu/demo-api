<?php

namespace App\Services\Providers;

use Throwable;
use Kreait\Firebase\Factory;
use App\Contracts\FirestoreContract;
use App\Enums\ServiceResponseMessageEnum;

class FirestoreProvider implements FirestoreContract
{
    /*
    |--------------------------------------------------------------------------
    | Set Variables
    |--------------------------------------------------------------------------
    */
    protected $factory;
    
    /*
    |--------------------------------------------------------------------------
    | Set Variables
    |--------------------------------------------------------------------------
    */
    public function __construct()
    {
        $this->factory = (new Factory)->withServiceAccount(getFirebaseCreds());
    }

	/*
    |--------------------------------------------------------------------------
    | Get Frestore
    |--------------------------------------------------------------------------
    */
    public function getFirestore()
    {
        $firestore = $this->factory->createFirestore();
        $database = $firestore->database();

        return $database;
    }

	/*
    |--------------------------------------------------------------------------
    | Update a record in a Firestore collection by document ID
    |--------------------------------------------------------------------------
    */
    public function updateRecord(string $collection, string $documentId, array $payload): array
    {
        try {
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
            | retrieve documents from Firestore collection by document ID
            |--------------------------------------------------------------------------
            */
            $document_ref = $this->getFirestore()->collection($collection)->document($documentId);
            $document_snapshot = $document_ref->snapshot();
            
            /*
            |--------------------------------------------------------------------------
            | check if document exists
            |--------------------------------------------------------------------------
            */
            if (!$document_snapshot->exists()) {
                return [
                    "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                    'response' => [],
                    'is_successful' => false
                ];
            }
            
            /*
            |--------------------------------------------------------------------------
            | set variable
            |--------------------------------------------------------------------------
            */
            $formatted_payload = [];
            foreach ($payload as $key => $value) {
                $formatted_payload[] = [
                    'path' => $key,
                    'value' => $value
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Update document
            |--------------------------------------------------------------------------
            */
            $document_ref->update($formatted_payload);
            
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
        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => $exception->getMessage(),
                'is_successful' => false
            ];
        }
    }

	/*
    |--------------------------------------------------------------------------
    | Delete a record from a Firestore collection by document ID
    |--------------------------------------------------------------------------
    */
    public function delete(string $collection, string $document_id): array
    {
        try {
            $document_ref = $this->getFirestore()->collection($collection)->document($document_id);
            $document_ref->delete();

            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                'response' => [],
                'is_successful' => true
            ];
        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => $exception->getMessage(),
                'is_successful' => false
            ];
        }
    }

	/*
    |--------------------------------------------------------------------------
    | Read a record from a Firestore collection by document ID
    |--------------------------------------------------------------------------
    */
    public function readRecord(string $collection, string $document_id): array
    {        
        try {
            /*
            |--------------------------------------------------------------------------
            | retrieve documents from Firestore collection by document ID
            |--------------------------------------------------------------------------
            */
            $document_ref = $this->getFirestore()->collection($collection)->document($document_id);
            $document_snapshot = $document_ref->snapshot();
            
            /*
            |--------------------------------------------------------------------------
            | check if document exists
            |--------------------------------------------------------------------------
            */
            if (!$document_snapshot->exists()) {
                return [
                    "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                    'response' => [],
                    'is_successful' => false
                ];
            }
            
            /*
            |--------------------------------------------------------------------------
            | success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                'response' => $document_snapshot->data(),
                'is_successful' => true
            ];
        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => $exception->getMessage(),
                'is_successful' => false
            ];
        }
    }

	/*
    |--------------------------------------------------------------------------
    | find where
    |--------------------------------------------------------------------------
    */
    public function findWhere(string $collection, string $field, string $value): array
    {        
        try {
            /*
            |--------------------------------------------------------------------------
            | Retrieve documents from Firestore collection based on a specific condition
            |--------------------------------------------------------------------------
            */
            $query = $this->getFirestore()->collection($collection)->where($field, '=', $value);
            $query_snapshot = $query->documents();
            
            /*
            |--------------------------------------------------------------------------
            | Initialize an array to store matched documents
            |--------------------------------------------------------------------------
            */
            $matched_documents = [];
            
            /*
            |--------------------------------------------------------------------------
            | Check if any document matches the condition
            |--------------------------------------------------------------------------
            */
            foreach ($query_snapshot as $document_snapshot) {
                if ($document_snapshot->exists()) {
                    $document_data = $document_snapshot->data();
                    $matched_documents[] = $document_data;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Empty payload response if no matching document found
            |--------------------------------------------------------------------------
            */
            if (!count($matched_documents)) {
                return [
                    "status" => ServiceResponseMessageEnum::EMPTY_PAYLOAD->value,
                    'response' => [],
                    'is_successful' => false
                ];
            }
            
            /*
            |--------------------------------------------------------------------------
            | success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                'response' => $matched_documents,
                'is_successful' => true
            ];
        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => $exception->getMessage(),
                'is_successful' => false
            ];
        }
    }

	/*
	|--------------------------------------------------------------------------
	| Create a new record in a Firestore collection
	|--------------------------------------------------------------------------
	*/
    public function create(string $collection, array $payload): array
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | get collection
            |--------------------------------------------------------------------------
            */
            $collection = $this->getFirestore()->collection($collection);
            
            /*
            |--------------------------------------------------------------------------
            | create record
            |--------------------------------------------------------------------------
            */
            $reference = $collection->add($payload);

            /*
            |--------------------------------------------------------------------------
            | send success response
            |--------------------------------------------------------------------------
            */
            return [
                "status" => ServiceResponseMessageEnum::SUCCESSFUL->value,
                'response' => [...$payload, $reference->id()],
                'is_successful' => true
            ];
        } catch (Throwable $exception) {
            return [
                "status" => ServiceResponseMessageEnum::FIRESTORE_PROVIDER_ERROR->value,
                'response' => json_decode($exception->getMessage()),
                'is_successful' => false
            ];
        }
    }
}
