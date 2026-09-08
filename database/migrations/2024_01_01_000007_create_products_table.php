<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->string('name', 150);
            $table->text('description')->nullable();

            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);

            $table->string('sku', 100)->nullable();

            $table->string('status', 20)->default('ACTIVE');

            $table->timestamps();

            $table->unique('sku', 'uq_products_sku');

            $table->foreign('category_id', 'fk_product_category')
                ->references('id')->on('categories')
                ->onDelete('restrict'); // required link, protect referential integrity

            $table->foreign('sub_category_id', 'fk_product_subcategory')
                ->references('id')->on('sub_categories')
                ->onDelete('set null'); // optional link

            $table->foreign('brand_id', 'fk_product_brand')
                ->references('id')->on('brands')
                ->onDelete('set null'); // optional link

            // FK columns used in frequent lookups
            $table->index('category_id', 'idx_products_category_id');
            $table->index('sub_category_id', 'idx_products_sub_category_id');
            $table->index('brand_id', 'idx_products_brand_id');
        });

        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT chk_products_status
            CHECK (status IN ('ACTIVE', 'INACTIVE', 'OUT_OF_STOCK'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
