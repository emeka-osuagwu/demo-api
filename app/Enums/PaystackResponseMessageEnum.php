<?php

namespace App\Enums;

enum PaystackResponseMessageEnum: string {
    case FAILED = 'failed';
    case PENDING = 'pending';
    case SUCCESSFUL = 'successful';
    case INSUFFICIENT_FUNDS = 'insufficient_funds';
    case INVALID_AUTHORIZATION_EMAIL = 'invalid_authorization_email';
    case TRANSACTION_REFERENCE_NOT_FOUND = 'transaction_reference_not_found';
    case PROVIDER_SERVICE_CONNECTION_ERROR = 'provider_service_connection_error';
    case TRANSACTION_AMOUNT_LIMIT_EXCEEDED = 'card_transaction_amount_limit_exceeded';
    case AUTHORIZATION_CODE_PATTERN_MISMATCH = 'authorization_code_pattern_mismatch';
}
