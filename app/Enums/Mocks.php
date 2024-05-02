<?php

namespace App\Enums;

enum Mocks: string {

    // USER -> 1XXX
    case INVALID_AUTHORIZATION_CODE = '1011111111';
    case INVALID_EMAIL = "samples@example.com";
    case FULL_NAME = "sample";
    case GOOGLE_AUTH_PROVIDER = "google";
    CASE APPLE_AUTH_PROVIDER = "apple";
    case INVALID_PLAYER_ID = "sjvngb";
    case PUSH_TOKEN = "111111";
    case INVALID_ID = "18c55643-94f3-46fa-a8f4-dd4368cef6a9";
    case USER_ROLE = "user";

    case EXISTING_TEAM_ID = "185ac6ae-9b31-4c33-beaa-1ac84b7e4fc4";
    case EXISTING_USER_EMAIL = "teffddddddestd@gmail.com";
    case EXISTING_USER_FULL_NAME = "test";
    case EXISTING_USER_AUTH_TOKEN = "11111111111";
    case EXISTING_USER_PLAYER_ID = "8fq5TwbtNz";
    case EXISTING_USER_AUTH_ID = "1212121212";

    case INVALID_TEAM_ID = "185ac6ae-9b31-4c33-beaa-1ac84b7e4ffs3";
    case INVALID_TEAM_NAME = "team name";
    
    case EVENT_ID = "185ac6ae-9b31-4c33-beaa-1ac84b7e4ffa9";

    case INVALID_PROVIDER_TRANSACTION_REFERENCE = "transaction reference";


    case INVALID_FIRESTORE_COLLECTION = "collection";
    case EMPTY_FIRESTORE_ID = "1111111";
    case INVALID_FIRESTORE_ID = "222222";

}
