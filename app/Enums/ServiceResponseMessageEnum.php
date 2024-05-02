<?php

namespace App\Enums;

enum ServiceResponseMessageEnum: string {
    case SUCCESSFUL = "successful";
    case VALIDATION_ERROR = "validation_error";
    case BIG_QUERY_PROVIDER_SERVICE_ERROR = 'big_query_provider_service_error';
    case CACHE_SERVICE_PROVIDER_ERROR = 'cache_service_provider_error';
    case FIRESTORE_PROVIDER_ERROR = 'firestore_provider_error';
    case EMPTY_PAYLOAD = 'payload_not_found';
}
