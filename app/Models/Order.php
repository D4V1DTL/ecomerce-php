<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total', 'sub_total', 'igv', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con productos a través de la tabla pivote order_product
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
