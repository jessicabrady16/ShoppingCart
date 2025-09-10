<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireRole
{
  public function handle(Request $request, Closure $next, string $role)
  {
    if ($request->session()->get('role', 'shopper') !== $role) {
      return response()->json(['message' => 'Forbidden'], 403);
    }
    return $next($request);
  }
}
