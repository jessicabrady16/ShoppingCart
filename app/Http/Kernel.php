<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  /**
   * Global HTTP middleware (runs on every request)
   */
  protected $middleware = [
    \Illuminate\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\HandleCors::class,
    \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
  ];

  /**
   * Route middleware groups
   */
  protected $middlewareGroups = [
    'web' => [
      \Illuminate\Cookie\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Session\Middleware\StartSession::class,
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'api' => [
      'throttle:api',
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
  ];

  /**
   * Route middleware aliases
   */
  protected $middlewareAliases = [
    'auth'             => \Illuminate\Auth\Middleware\Authenticate::class,
    'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
    'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can'              => \Illuminate\Auth\Middleware\Authorize::class,

    // If you DON'T have App\Http\Middleware\RedirectIfAuthenticated, you can remove this alias.
    'guest'            => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,

    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
    'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    'precognitive'     => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,

    // Your custom ones
    'admin.token'      => \App\Http\Middleware\AdminToken::class,
    'role'             => \App\Http\Middleware\RequireRole::class,
  ];
}
