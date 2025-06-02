<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Order;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'discountPrice',
        'stock',
        'rating',
        'featured',
        'image',        // <-- Agregado
        'category_id',  // <-- Agregado
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}


