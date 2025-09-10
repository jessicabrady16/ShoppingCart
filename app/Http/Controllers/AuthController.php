<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
  public function me(Request $request)
  {
    return response()->json([
      'role' => $request->session()->get('role', 'shopper'),
    ]);
  }

  public function login(Request $request)
  {
    $role = $request->input('role', 'shopper');

    if ($role === 'admin') {
      $token = (string) $request->input('token', '');
      $expected = (string) env('ADMIN_TOKEN', '');
      if ($expected === '' || $token !== $expected) {
        return response()->json(['message' => 'Invalid admin token'], 403);
      }
    }

    $request->session()->put('role', $role);
    return response()->json(['role' => $role]);
  }

  public function logout(Request $request)
  {
    $request->session()->forget('role');
    return response()->json(['role' => 'shopper']);
  }
}
