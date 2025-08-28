<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreCartItemRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }
  public function rules(): array
  {
    return [
      'product_id' => ['required', 'integer', 'min:1'],
      'name'       => ['required', 'string', 'max:255'],
      'price'      => ['required', 'numeric', 'min:0.01'],
      'quantity'   => ['required', 'integer', 'min:1'],
    ];
  }
}
