<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Service Account Credentials JSON File
    |
    | https://googlecloudplatform.github.io/google-cloud-php/#/docs/google-cloud/v0.35.0/guides/authentication
    |
    */

    'application_credentials' => base_path() . '/' . env('GOOGLE_CLOUD_APPLICATION_CREDENTIALS'),

    /*
    | OPTIONAL:
    | Use keyFile to use a json config from the current environment.
    | For example secrets in laravel vapor
    |
    | https://docs.vapor.build/1.0/projects/environments.html#secrets
    */
    
    // 'keyFile' => [
    //     "type" => "service_account",
    //     "project_id" => "sofri-343419",
    //     "private_key_id" => "964a2e767b2d195a7a7adf740e0655f67a811322",
    //     "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCQFFQAWyF24q5H\nd+tSkxD6GLB6p3fR+7rCc/hmheKmMStd0joDKUUNm7u7oJjpdgPEIK4ejlhOPRoz\nUIHluYKOiObuK2Uxw5NWPkOE06FK5no4y70/Qi1KXFws1cjk7ba5vT+RE2Mdix3+\nndet/fqeVAAO2Kal5xqg+tP1VbbFREi5owhiRU2CH12PHQYqqxbmeJkn21DPfeIn\n96amcRYZjGBjTQ3wQgwEuyIhPVknqZRZFDMC6xAn1B3dwTngPoh7y8Hf6LeXvQpo\nBiHhEn+4fGlF+XXqAfiJOnYmtNo7xov1414vqdVg0+aWRXQRmIAZpYyMcQdwirp/\nG7bZ/Tc9AgMBAAECggEAAXgXmC5Vs8ThcoycO2oQ+v/b8tA41k0LDTcKAh+c3UV0\nk/8UBNq7n8Ul/6aGUKEZrjsIE3svltKkLQBF5s4CsQnf0u9h14VPQAqf/R98Tkt1\n1RTsv9OgqxiwfuuKIh1zZsbxejz4noE/48v/ukAz+T/RhVU7s8bcvGX6Wc7PL4k8\nCXxFbo8Ubg/3qVHY8+cmlioX+bPfen3t+qtA3f1OaJvRptpHhkpAeLB0LHvXge7P\nBE25az8HDXVYcumEnjf6m53Lw+9/HR4xOe818G7qClyY66XXCm1Dn0cY5GwjDzdm\ncXaBlHSM5fHyY1MkJuoeqNlFzWAvl26YOv7O0r/bQQKBgQDKufbh8ck0qpgz+6Sw\n1z6XN5voaDoyEyLHDGc+nKmTasrIxJiQnSOQ+Qgh20t2Pce3TtsXPD3qz49RcdD4\nIXy56oCSSZ3W/XiVeQd9sHMH4E3nQ3e0weYr97wQuWpmJmc+d1/D5iNPCMwvUN7o\ns6Nf9NUpERlwDfDdmB1raXbhfQKBgQC18P83dMpAb/EASKsVioyWKiXnIS8U08J0\nngNR0ls7E84J6BfaSxBlzod6HqwST8JHHObyONuTrh63ccsV4uwDZcEICkS6ogDm\nqa30GtfK3ouupEM9FHRGLvScM4dfKlyn3DLyIBoSE+ZCa5pFPXt3TxNfHX1WmQY3\nFeg9oFeYwQKBgD9miV+ATny6HX+kY40qw9hm+8tjTU/7zBSUPHXIaQBBlcnUMiKI\nAmeEepacDq2cBKm/b6WEoZid8SR3g/MWBfve6vbVLxfdjaixgTY9yLvd2n9JClbt\njR6TC63vTudDe2Z9zuVlRAWjqrfhgtUj2SRZXZDKWDpDIeErgYfmI9fBAoGAPAuu\nXyIHQd7v3dUdyX0xSrncSnx0Kl581bn0hIN1Ink1zaUwghW18rOHmLEYvu5dwtRy\nD8zeAs47SvWePbnnhHOHkly5NBMVUwr64w4c29I4rUl++2CNwz/p8Mc8zRaQ/8E3\nextHH2I74v3aKzHQVp7dWM3FuNfF6lYrkHMjlcECgYEAiPIT3zu84MY27vBBXIi4\n268lgtPg4vQTofA5F78qNuQt+zMVQykBP6lchnhNUUl2HpcEhF36EilHAb9e52rF\n8YQEgDwGVp0oR/rgqQ2YwIL8aHhMNLT5ZoMLfWKSeH9ZhNRqf0nHS6GbZmlAmMXR\n9xqQ66vY9Y9VmJb6qmYWdK4=\n-----END PRIVATE KEY-----\n",
    //     "client_email" => "sofri-local-service@sofri-343419.iam.gserviceaccount.com",
    //     "client_id" => "100746488232947549450",
    //     "AUTHORIZATION_uri" => "https://accounts.google.com/o/oauth2/auth",
    //     "token_uri" => "https://oauth2.googleapis.com/token",
    //     "AUTHORIZATION_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
    //     "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/sofri-local-service%40sofri-343419.iam.gserviceaccount.com"
    // ],
    // 'keyFile' => json_decode(trim(env('GOOGLE_CLOUD_APPLICATION_CREDENTIALS')), true),

    /*
    |--------------------------------------------------------------------------
    | Project ID
    |--------------------------------------------------------------------------
    |
    | The Project Name is a user-friendly name,
    | while the Project ID is required by the Google Cloud client libraries to authenticate API requests.
    |
    */
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Client Auth Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the auth cache connection that gets used.
    |
    | Supported: "apc", "array", "database", "file", "memcached", "redis"
    |
    */

    'AUTHORIZATION_cache_store' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Client Options
    |--------------------------------------------------------------------------
    |
    | Here you may configure additional parameters that
    | the underlying BigQueryClient will use.
    |
    | Optional parameters: "authCacheOptions", "authHttpHandler", "httpHandler", "retries", "scopes", "returnInt64AsObject"
    */

    'client_options' => [
        'retries' => 3, // Default
    ],

    /*
    |--------------------------------------------------------------------------
    | Dataset location
    |--------------------------------------------------------------------------
    |
    | Specify the dataset location.
    |
    | Supported values can be found at https://cloud.google.com/bigquery/docs/locations
    |
    */

    'location' => '',
];
