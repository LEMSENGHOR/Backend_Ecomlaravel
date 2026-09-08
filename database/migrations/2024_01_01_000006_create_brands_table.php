<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamp('created_at')->useCurrent();

            $table->unique('name', 'uq_brands_name');
        });

        DB::statement("
            ALTER TABLE brands
            ADD CONSTRAINT chk_brands_status
            CHECK (status IN ('ACTIVE', 'INACTIVE'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
