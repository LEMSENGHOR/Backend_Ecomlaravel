<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50);
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_amount', 12, 2)->default(0);
            $table->integer('max_usage')->default(0);
            $table->integer('used_count')->default(0);

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->string('status', 20)->default('ACTIVE');
            $table->timestamp('created_at')->useCurrent();

            $table->unique('code', 'uq_coupons_code');
        });

        DB::statement("
            ALTER TABLE coupons
            ADD CONSTRAINT chk_coupons_discount_type
            CHECK (discount_type IN ('PERCENTAGE', 'FIXED'))
        ");

        DB::statement("
            ALTER TABLE coupons
            ADD CONSTRAINT chk_coupons_status
            CHECK (status IN ('ACTIVE', 'INACTIVE', 'EXPIRED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
