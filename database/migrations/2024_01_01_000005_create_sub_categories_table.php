<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('category_id', 'fk_subcategory_category')
                ->references('id')->on('categories')
                ->onDelete('cascade'); // dependent on its parent category

            $table->index('category_id', 'idx_sub_categories_category_id');
        });

        DB::statement("
            ALTER TABLE sub_categories
            ADD CONSTRAINT chk_sub_categories_status
            CHECK (status IN ('ACTIVE', 'INACTIVE'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};
