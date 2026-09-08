<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');

            $table->string('transaction_id', 100)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 30);
            $table->string('status', 30)->default('PENDING');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique('order_id', 'uq_payments_order_id');
            $table->unique('transaction_id', 'uq_payments_transaction_id');

            $table->foreign('order_id', 'fk_payment_order')
                ->references('id')->on('orders')
                ->onDelete('cascade'); // dependent on its order
        });

        DB::statement("
            ALTER TABLE payments
            ADD CONSTRAINT chk_payments_status
            CHECK (status IN ('PENDING', 'COMPLETED', 'FAILED', 'REFUNDED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
