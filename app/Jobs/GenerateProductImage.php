<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\Images\ImageFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateProductImage implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(public int $productId) {}

  public function handle(): void
  {
    $product = Product::find($this->productId);
    if (!$product) return;

    $provider = ImageFactory::make();
    $tmpUrl   = $provider->generate($product->name);

    if (!$tmpUrl) return;

    $bytes = Http::timeout(60)->get($tmpUrl)->body();
    if (!$bytes) return;

    $ext  = 'jpg';
    $path = "products/{$product->id}-" . Str::slug($product->name) . ".$ext";

    Storage::disk('public')->put($path, $bytes);
    $product->image_url = Storage::url($path);
    $product->save();
  }
}
