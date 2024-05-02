<?php

namespace App\Http\Middleware\Custom;

use Closure;
use App\Traits\ResponseTrait;
use App\Enums\ResponseCode\AuthResponseCode;

use App\Services\AuthService;

class AdminOnly
{
    use ResponseTrait;


    public function __construct(
        protected AuthService $authService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $token = bearerToken();

        /*
        |--------------------------------------------------------------------------
        | check if token is not null
        |--------------------------------------------------------------------------
        */
        if (is_null($token)) {
            return $this->sendResponse([], AuthResponseCode::INVALID_AUTHORIZATION);
        }

        /*
        |--------------------------------------------------------------------------
        | validate token
        |--------------------------------------------------------------------------
        */
        $auth = $this->authService->checkAuthorization($token ?? '');

        /*
        |--------------------------------------------------------------------------
        | check if auth is valid
        |--------------------------------------------------------------------------
        */
        if (!$auth) {
            return $this->sendResponse([], AuthResponseCode::INVALID_AUTHORIZATION);
        }

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        if (!empty($auth)) {
            if (measurementDate($auth['created_at'], 'minutes') > 150) {
                return $this->sendResponse([], AuthResponseCode::INVALID_AUTHORIZATION);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | add comment
        |--------------------------------------------------------------------------
        */
        if ($auth['role'] !== 'admin') {
            return $this->sendResponse([], AuthResponseCode::INVALID_AUTHORIZATION);
        }

        /*
        |--------------------------------------------------------------------------
        | set auth in request
        |--------------------------------------------------------------------------
        */
        request()->auth_user = $auth;

        /*
        |--------------------------------------------------------------------------
        | response
        |--------------------------------------------------------------------------
        */
        return $next($request);
    }
}
