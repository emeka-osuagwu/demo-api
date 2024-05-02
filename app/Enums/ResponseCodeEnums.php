<?php
namespace App\Enums;

enum ResponseCodeEnums: int
{
    /*
    |--------------------------------------------------------------------------
    | Transaction
    |--------------------------------------------------------------------------
    */
    case TRANSACTION_REQUEST_ERROR = 1000;
    case TRANSACTION_REQUEST_SUCCESSFUL = 1001;
    case TRANSACTION_NOT_FOUND = 1002;
    case TRANSACTION_REQUEST_VALIDATION_ERROR = 1003;
    case TRANSACTION_SERVICE_REQUEST_ERROR = 1004;
    case TRANSACTION_ALREADY_EXISTS = 1005;
    case TRANSACTION_SERVICE_VALIDATION_ERROR = 1006;
    case PAYSTACK_TRANSACTION_NOT_FOUND = 1007;
    case PAYSTACK_TRANSACTION_SERVICE_REQUEST_ERROR = 1008;

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */
    case USER_REQUEST_ERROR = 2001;
    case USER_REQUEST_VALIDATION_ERROR = 2002;
    case USER_REQUEST_SUCCESSFUL = 2003;
    case USER_NOT_FOUND = 2004;
    case USER_REQUEST_NOT_FOUND = 2005;
    case USER_SERVICE_REQUEST_ERROR = 2006;
    case USER_PUSH_NOTIFICATION_ERROR = 2007;
    case USER_CANT_INVITE_SELF_ERROR = 2008;

    /*
    |--------------------------------------------------------------------------
    | Puzzle
    |--------------------------------------------------------------------------
    */
    case PUZZLE_REQUEST_ERROR = 3001;
    case PUZZLE_REQUEST_SUCCESSFUL = 3002;
    case PUZZLE_REQUEST_VALIDATION_ERROR = 3003;
    case PUZZLE_SERVICE_VALIDATION_ERROR = 3004;
    case PUZZLE_SERVICE_REQUEST_ERROR = 3005;
    case PUZZLE_NOT_FOUND = 3006;
    case PUZZLE_FILE_CONVERSION_ERROR = 3007;

    /*
    |--------------------------------------------------------------------------
    | Team
    |--------------------------------------------------------------------------
    */
    case TEAM_REQUEST_VALIDATION_ERROR = 4001;
    case TEAM_REQUEST_SUCCESSFUL = 4002;
    case TEAM_REQUEST_ERROR = 4003;
    case TEAM_NOT_FOUND = 4004;
    case TEAM_PLAYER_NOT_FOUND = 4005;
    case TEAM_SERVICE_REQUEST_ERROR = 4006;
    case TEAM_SERVICE_VALIDATION_ERROR = 4007;
    case USER_ALREADY_OWNS_A_TEAM = 4008;
    case TEAM_INVITATION_NOT_FOUND = 4009;
    case TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR = 4010;

    /*
    |--------------------------------------------------------------------------
    | Level
    |--------------------------------------------------------------------------
    */
    case LEVEL_REQUEST_ERROR = 5001;
    case LEVEL_NOT_FOUND = 5002;
    case LEVEL_REQUEST_SUCCESSFUL = 5003;
    case LEVEL_SERVICE_REQUEST_ERROR = 5004;

    /*
    |--------------------------------------------------------------------------
    | Game
    |--------------------------------------------------------------------------
    */
    case GAME_REQUEST_ERROR = 6001;
    case GAME_SERVICE_REQUEST_ERROR = 6002;
    case GAME_NOT_FOUND = 6003;
    case GAME_REQUEST_SUCCESSFUL = 6004;
    case GAME_SERVICE_VALIDATION_ERROR = 6005;
    case GAME_REQUEST_VALIDATION_ERROR = 6006;
    case GAME_IN_PROGRESS = 6007;
    case GAME_NOT_ACTIVATED = 6008;
    case GAME_REQUEST_FAILED = 6009;
    case GAME_INVITE_ALREADY_EXIST_ERROR = 6010;
    case UNABLE_TO_UPDATE_GAME_ERROR = 6011;
    case GAME_TOTEM_EXHAUSTED = 6012;
    case ACTIVE_GAME_INVITE_ALREADY_EXISTS = 6013;
    case GAME_PLAYER_MISMATCH_ERROR = 6014;

    /*
    |--------------------------------------------------------------------------
    | Product
    |--------------------------------------------------------------------------
    */
    case PRODUCT_REQUEST_VALIDATION_ERROR = 7001;
    case PRODUCT_REQUEST_ERROR = 7002;
    case PRODUCT_SERVICE_REQUEST_ERROR = 7003;
    case PRODUCT_NOT_FOUND = 7004;
    case PRODUCT_REQUEST_SUCCESSFUL = 7005;
    case PRODUCT_SERVICE_VALIDATION_ERROR = 7006;

    /*
    |--------------------------------------------------------------------------
    | Event
    |--------------------------------------------------------------------------
    */
    case EVENT_REQUEST_VALIDATION_ERROR = 8001;
    case EVENT_REQUEST_ERROR = 8002;
    case EVENT_SERVICE_REQUEST_ERROR = 8003;
    case EVENT_NOT_FOUND = 8004;
    case EVENT_REQUEST_SUCCESSFUL = 8005;
    case EVENT_SERVICE_VALIDATION_ERROR = 8006;

    /*
    |--------------------------------------------------------------------------
    | Push Tokens
    |--------------------------------------------------------------------------
    */
    case PUSH_TOKEN_NOT_FOUND = 9001;
    case PUSH_TOKEN_SERVICE_REQUEST_ERROR = 9002;
    /*
    |--------------------------------------------------------------------------
    | Team Player
    |--------------------------------------------------------------------------
    */
    case TEAM_PLAYER_SERVICE_REQUEST_ERROR = 10000;
    case TEAM_PLAYER_SERVICE_VALIDATION_ERROR = 10001;
    case AUTH_USER_IS_NOT_A_TEAM_PLAYER = 10002;
    case AUTH_USER_IS_NOT_A_TEAM_ADMIN = 10003;
    case TEAM_PLAYER_REQUEST_SUCCESSFUL = 10004;
    case TEAM_PLAYER_IS_ALREADY_ON_A_TEAM = 10005;
    case TEAM_PLAYER_INVITATION_NOT_FOUND = 10006;
    case TEAM_PLAYER_INVITATION_ALREADY_ACCEPTED = 10007;
    case UNABLE_TO_REMOVE_SELF_ERROR = 10008;
    case USER_IS_ALREADY_IN_A_TEAM = 10009;

    /*
    |--------------------------------------------------------------------------
    | Team Player Points
    |--------------------------------------------------------------------------
    */
    case TEAM_PLAYER_POINTS_REQUEST_VALIDATION_ERROR = 11000;
    case TEAM_PLAYER_POINTS_SERVICE_VALIDATION_ERROR = 11001;
    case TEAM_PLAYER_POINTS_SERVICE_REQUEST_ERROR = 11002;
    case TEAM_PLAYER_POINTS_REQUEST_SUCCESSFUL = 11003;

    /*
    |--------------------------------------------------------------------------
    | Points donations
    |--------------------------------------------------------------------------
    */
    case POINT_DONATIONS_REQUEST_VALIDATION_ERROR = 12000;
    case POINT_DONATIONS_SERVICE_VALIDATION_ERROR = 12001;
    case POINT_DONATIONS_SERVICE_REQUEST_ERROR = 12002;
    case POINT_DONATIONS_REQUEST_SUCCESSFUL = 12003;
    case INSUFFICIENT_POINTS_ERROR = 12004;
    case USER_CANNOT_DONATE_POINT_TO_SELF = 12005;

    /*
    |--------------------------------------------------------------------------
    | Points donations
    |--------------------------------------------------------------------------
    */
    case LEADERBOARD_REQUEST_SUCCESSFUL = 13000;

    public function getCode()
    {
        return $this;
    }

    public function toString()
    {
        return match ($this) {
            /*
            |--------------------------------------------------------------------------
            | User Response
            |--------------------------------------------------------------------------
            */
            self::USER_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_REQUEST_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_PUSH_NOTIFICATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_CANT_INVITE_SELF_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Transaction Response
            |--------------------------------------------------------------------------
            */
            self::TRANSACTION_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TRANSACTION_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TRANSACTION_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
                self::TRANSACTION_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TRANSACTION_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TRANSACTION_ALREADY_EXISTS => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TRANSACTION_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PAYSTACK_TRANSACTION_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PAYSTACK_TRANSACTION_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Puzzle response
            |--------------------------------------------------------------------------
            */
            self::PUZZLE_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PUZZLE_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],

            self::PUZZLE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PUZZLE_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PUZZLE_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PUZZLE_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PUZZLE_FILE_CONVERSION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],


            /*
            |--------------------------------------------------------------------------
            | Team response
            |--------------------------------------------------------------------------
            */
            self::TEAM_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            self::TEAM_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],

            self::TEAM_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            self::TEAM_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            self::TEAM_PLAYER_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_ALREADY_OWNS_A_TEAM => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_INVITATION_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_INVITATION_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Level response
            |--------------------------------------------------------------------------
            */
            self::LEVEL_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::LEVEL_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::LEVEL_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::LEVEL_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Game Response
            |--------------------------------------------------------------------------
            */
            self::GAME_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
                self::GAME_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_IN_PROGRESS => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_NOT_ACTIVATED => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_REQUEST_FAILED => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_INVITE_ALREADY_EXIST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::UNABLE_TO_UPDATE_GAME_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_TOTEM_EXHAUSTED => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::ACTIVE_GAME_INVITE_ALREADY_EXISTS => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::GAME_PLAYER_MISMATCH_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Product Response
            |--------------------------------------------------------------------------
            */
            self::PRODUCT_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PRODUCT_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PRODUCT_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PRODUCT_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PRODUCT_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PRODUCT_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Event Response
            |--------------------------------------------------------------------------
            */
            self::EVENT_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'message' => $this->name,
                'response_code' => $this,
            ],
            self::EVENT_NOT_FOUND => [
                'status' => 400,
                'message' => $this->name,
                'response_code' => $this,
            ],
            self::EVENT_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'message' => $this->name,
                'response_code' => $this,
            ],

            /*
            |--------------------------------------------------------------------------
            | Push Token Response
            |--------------------------------------------------------------------------
            */
            self::PUSH_TOKEN_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::PUSH_TOKEN_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Team Player Response
            |--------------------------------------------------------------------------
            */
            self::TEAM_PLAYER_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::AUTH_USER_IS_NOT_A_TEAM_PLAYER => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            self::AUTH_USER_IS_NOT_A_TEAM_ADMIN => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_IS_ALREADY_ON_A_TEAM => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_INVITATION_NOT_FOUND => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_INVITATION_ALREADY_ACCEPTED => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::UNABLE_TO_REMOVE_SELF_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Team Player Points Response
            |--------------------------------------------------------------------------
            */
            self::TEAM_PLAYER_POINTS_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_POINTS_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_POINTS_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::TEAM_PLAYER_POINTS_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_IS_ALREADY_IN_A_TEAM  => [
              'status' => 400,
              'response_code' => $this,
              'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Point Donations Response
            |--------------------------------------------------------------------------
            */
            self::POINT_DONATIONS_REQUEST_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::POINT_DONATIONS_SERVICE_VALIDATION_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::POINT_DONATIONS_SERVICE_REQUEST_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::POINT_DONATIONS_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::INSUFFICIENT_POINTS_ERROR => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],
            self::USER_CANNOT_DONATE_POINT_TO_SELF => [
                'status' => 400,
                'response_code' => $this,
                'message' => $this->name
            ],

            /*
            |--------------------------------------------------------------------------
            | Leaderboard Response
            |--------------------------------------------------------------------------
            */
            self::LEADERBOARD_REQUEST_SUCCESSFUL => [
                'status' => 200,
                'response_code' => $this,
                'message' => $this->name
            ],
        };
    }
}
