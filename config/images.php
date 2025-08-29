<?php

return [
  // Auto-pick pixabay if key is present, otherwise fallback to placeholder
  'provider'    => env('IMAGE_PROVIDER', env('PIXABAY_API_KEY') ? 'pixabay' : 'placeholder'),
  'pixabay_key' => env('PIXABAY_API_KEY', ''),
];
