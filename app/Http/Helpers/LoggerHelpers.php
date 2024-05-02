<?php

use App\Models\User;
use App\Services\LogService;
// use Aws\Credentials\Credentials;
// use App\Notifications\SofriLogNotification;
// use Aws\CloudWatchLogs\CloudWatchLogsClient;
// use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;

// function fireEvent($payload)
// {
//     if (in_array(env('APP_ENV'), ['staging', 'production'])) {
//         $user = User::find(1);
//         $user->notify(new SofriLogNotification($payload));
//     }
// }

function maskSensitiveLogData($json_input) {

    $keys_to_mask = [
        'token',
        'password',
        'clientId',
        'push_token',
        'nip_session_id'
    ];

    $data = $json_input;

    if (is_string($json_input)) {
        $data = json_decode($json_input, true);
    }

    array_walk_recursive($data, function(&$value, $key) use ($keys_to_mask) {
        if (in_array($key, $keys_to_mask)) {
            $value = "SENSITIVE_DATA";
        }
    });

    if (isset($data['request']) && is_array($data['request']) && array_key_exists('file', $data['request'])) {
        $file = $data['request']['file'];
        if (is_object($file) && get_class($file) === 'Illuminate\Http\UploadedFile') {
            $data['request']['file'] = 'SENSITIVE_FILE_DATA';
        }
    }
    return $data;
}

function systemLogger($payload)
{
    $data = $payload;

    // if(gettype($payload) != 'string'){
    //     $data = maskSensitiveLogData($payload);
    // }

    consoleLogger($data);
}

/*
|--------------------------------------------------------------------------
| this function make it possible to show a log with trace
|--------------------------------------------------------------------------
*/
function consoleLogger(mixed $payload)
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $payload = [
        'file' => $trace[0]['file'],
        'line' => $trace[0]['line'],
        'payload' => $payload
    ];
    \Log::info($payload);
    // \Log::info(json_encode($payload));
}

function initiateLogger() {
    request()->logger = new LogService();
}

