<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminToken
{
  public function handle(Request $request, Closure $next): Response
  {
    $expected = config('cart.admin_token');
    if (!$expected) {
      abort(500, 'ADMIN_API_TOKEN not set');
    }

    $given = $request->header('X-Admin-Token');
    if (!hash_equals($expected, (string) $given)) {
      abort(403, 'Admin token required');
    }

    return $next($request);
  }
}
