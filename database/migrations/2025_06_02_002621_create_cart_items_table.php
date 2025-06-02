<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // elimina items si usuario se elimina
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // elimina items si producto se elimina
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id']); // para que un producto esté una sola vez por usuario
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
