<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);

            $table->unique(['cart_id', 'product_id'], 'uq_cart_items_cart_id_product_id');

            $table->foreign('cart_id', 'fk_cart_item_cart')
                ->references('id')->on('carts')
                ->onDelete('cascade'); // dependent on its cart

            $table->foreign('product_id', 'fk_cart_item_product')
                ->references('id')->on('products')
                ->onDelete('restrict'); // protect referential integrity

            $table->index('product_id', 'idx_cart_items_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
