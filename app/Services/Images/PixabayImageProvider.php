<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\Http;

final class PixabayImageProvider implements ImageProvider
{
  public function __construct(private string $apiKey) {}

  public function generate(string $productName): ?string
  {
    if (!$this->apiKey) {
      return null;
    }

    $q = trim($productName);
    if ($q === '') return null;

    $resp = Http::get('https://pixabay.com/api/', [
      'key'         => $this->apiKey,
      'q'           => $q,
      'image_type'  => 'photo',
      'safesearch'  => 'true',
      'per_page'    => 3,
      'order'       => 'popular',
      'category'    => null,
    ]);

    if (!$resp->successful()) return null;

    $hits = $resp->json('hits', []);
    if (empty($hits)) return null;

    // pick first hit; prefer large image URL
    return $hits[0]['largeImageURL'] ?? $hits[0]['webformatURL'] ?? null;
  }
}
