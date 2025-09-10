<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;

class PricingController extends Controller
{
  public function __construct(private PricingService $pricing) {}

  public function show()
  {
    return response()->json($this->pricing->get());
  }

  public function update(Request $request)
  {
    $data = $request->validate([
      'tax_rate'      => ['sometimes', 'numeric', 'min:0'],
      'discount_type' => ['sometimes', 'in:none,flat,percent'],
      'discount'      => ['sometimes', 'numeric', 'min:0'],
    ]);

    // If type is percent, cap at 100 (server-side safety)
    if (($data['discount_type'] ?? null) === 'percent' && isset($data['discount'])) {
      $data['discount'] = min(100, (float) $data['discount']);
    }

    return response()->json($this->pricing->update($data));
  }
}
