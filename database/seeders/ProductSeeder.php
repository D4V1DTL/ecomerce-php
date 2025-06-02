<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            for ($i = 1; $i <= 5; $i++) {
                $price = rand(200, 1000);
                Product::create([
                    'name' => $category->name . " Producto $i",
                    'description' => "Descripción de " . $category->name . " Producto $i",
                    'price' => $price,
                    'discountPrice' => rand((int)($price * 0.7), $price - 1),
                    'stock' => rand(1, 50),
                    'rating' => rand(1, 5),
                    'featured' => (bool)rand(0, 1),
                    'image' => $category->image, // Imagen genérica
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
