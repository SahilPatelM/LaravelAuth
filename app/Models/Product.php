<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Categorie;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'in_stock',
        'vendor_id',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Categorie::class);
    }

    // Relationship: A product has many images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Optional: belongs to a vendor (user)
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
