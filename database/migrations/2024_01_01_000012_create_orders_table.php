<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->string('order_number', 50);
            $table->decimal('total_amount', 12, 2);
            $table->string('status', 30)->default('PENDING');
            $table->text('shipping_address')->nullable();

            $table->timestamps();

            $table->unique('order_number', 'uq_orders_order_number');

            $table->foreign('user_id', 'fk_order_user')
                ->references('id')->on('users')
                ->onDelete('restrict'); // protect order history

            $table->index('user_id', 'idx_orders_user_id');
        });

        DB::statement("
            ALTER TABLE orders
            ADD CONSTRAINT chk_orders_status
            CHECK (status IN ('PENDING', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'REFUNDED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
