<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('password', 255);
            $table->string('phone', 20)->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();

            $table->unique('email', 'uq_users_email');
        });

        // status-like column -> CHECK constraint instead of native ENUM
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT chk_users_status
            CHECK (status IN ('ACTIVE', 'INACTIVE', 'SUSPENDED'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
