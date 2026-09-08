<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'product_id'], 'uq_reviews_user_id_product_id');

            $table->foreign('user_id', 'fk_review_user')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('product_id', 'fk_review_product')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->index('product_id', 'idx_reviews_product_id');
        });

        DB::statement("
            ALTER TABLE reviews
            ADD CONSTRAINT chk_reviews_rating
            CHECK (rating BETWEEN 1 AND 5)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
