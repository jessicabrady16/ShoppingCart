<?php

namespace App\Services\Images;

interface ImageProvider
{
  /**
   * Generate an image for a product name, return a temporary HTTP URL to the image.
   * Implementations may return a remote URL; the job will download and store locally.
   */
  public function generate(string $productName): ?string;
}
