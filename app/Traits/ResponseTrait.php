<?php

namespace App\Traits;

use App\Events\Logs\ProcessLogEvent;
use Exception;

trait ResponseTrait
{
	function sendResponse($data, $response_code_object)
	{
        /*
        |--------------------------------------------------------------------------
        | add comments
        |--------------------------------------------------------------------------
        */
		$payload = $response_code_object->toString();
		$payload['data'] = $data;

        /*
        |--------------------------------------------------------------------------
        | add comments
        |--------------------------------------------------------------------------
        */
		if($payload['status'] == 400){
			// $log_payload = [
			// 	'response' => $payload,
			// 	'request' => request()->all()
			// ];

			// ProcessLogEvent::dispatch(maskSensitiveLogData($log_payload));
		}

        /*
        |--------------------------------------------------------------------------
        | add comments
        |--------------------------------------------------------------------------
        */
		return response()
		->json($payload, 200)
		->withHeaders([
			'IOS_SUPPORTED_APP_VERSION' => ENV('IOS_SUPPORTED_APP_VERSION'),
			'ANDROID_SUPPORTED_APP_VERSION' => ENV('ANDROID_SUPPORTED_APP_VERSION')
		]);

	}
}
