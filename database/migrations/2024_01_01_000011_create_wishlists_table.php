<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'product_id'], 'uq_wishlists_user_id_product_id');

            $table->foreign('user_id', 'fk_wishlist_user')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('product_id', 'fk_wishlist_product')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->index('product_id', 'idx_wishlists_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
