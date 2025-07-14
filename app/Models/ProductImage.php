<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'tag',
    ];

    // Relationship: Each image belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}