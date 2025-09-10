<?php

return [
  // defaults used for non-admin callers
  'default_tax_rate' => (float) env('CART_TAX_RATE', 0.0),

  'discount' => [
    'type'   => env('CART_DISCOUNT_TYPE', 'none'), // none|flat|percent
    'amount' => (float) env('CART_DISCOUNT_AMOUNT', 0.0),
  ],

  // very light admin gate for demo purposes
  'admin_token' => env('ADMIN_API_TOKEN', null),
];
