<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);

            $table->foreign('order_id', 'fk_order_item_order')
                ->references('id')->on('orders')
                ->onDelete('cascade'); // dependent on its order

            $table->foreign('product_id', 'fk_order_item_product')
                ->references('id')->on('products')
                ->onDelete('restrict'); // protect order history

            $table->index('order_id', 'idx_order_items_order_id');
            $table->index('product_id', 'idx_order_items_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
