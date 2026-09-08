<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->string('image_url', 500);
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('product_id', 'fk_product_images_product')
                ->references('id')->on('products')
                ->onDelete('cascade'); // dependent on its product

            $table->index('product_id', 'idx_product_images_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
