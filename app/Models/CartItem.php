<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product; 
use App\Models\User; 

class CartItem extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
