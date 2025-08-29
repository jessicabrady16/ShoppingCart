<?php

namespace App\Services\Images;

final class PlaceholderImageProvider implements ImageProvider
{
  public function generate(string $productName): ?string
  {
    $seed = substr(sha1($productName), 0, 12);
    return "https://picsum.photos/seed/{$seed}/800/600";
  }
}
