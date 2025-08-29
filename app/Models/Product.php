<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = ['name', 'price'];
  protected $casts = ['price' => 'decimal:2']; // stored as DECIMAL(…,2)
}
