<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            $table->primary(['user_id', 'role_id']);

            $table->foreign('user_id', 'fk_role_user_user')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('role_id', 'fk_role_user_role')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            // FK columns used in lookups
            $table->index('role_id', 'idx_role_user_role_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
