<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\Config;

final class ImageFactory
{
  public static function make(): ImageProvider
  {
    $provider = Config::get('images.provider', 'placeholder');

    return $provider === 'pixabay'
      ? new PixabayImageProvider(Config::get('images.pixabay_key', ''))
      : new PlaceholderImageProvider();
  }
}
