<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique('user_id', 'uq_carts_user_id');

            $table->foreign('user_id', 'fk_cart_user')
                ->references('id')->on('users')
                ->onDelete('cascade'); // dependent on its user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
