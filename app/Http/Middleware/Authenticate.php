<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate extends Middleware
{
    /**
     * The JWT Authenticator.
     *
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwtAuth;
    protected $auth;

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param JWTAuth $jwtAuth
     * @param Auth $auth
     */
    public function __construct(JWTAuth $jwtAuth, Auth $auth)
    {
        parent::__construct($auth);
        $this->jwtAuth = $jwtAuth;
    }

    public function checkForToken(Request $request)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            throw new UnauthorizedHttpException('jwt-auth', 'Token not provided');
        }
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws UnauthorizedException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    protected function authenticate($request, array $guards)
    {
        $this->checkForToken($request);

        try {
            if (!$this->jwtAuth->parseToken()->authenticate()) {
                $this->unauthenticated($request, $guards);
            }
        } catch (JWTException $e) {
            if ($e->getMessage() == "Token has expired") {
                throw new UnauthorizedException("token_expire");
            }
            $this->unauthenticated($request, $guards);
        }
        return $this->auth->shouldUse($guards[0]);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Get a JWT via given credentials.
     * @param Request $request
     * @param array $guards
     * @throws UnauthorizedException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new UnauthorizedException();
    }
}
