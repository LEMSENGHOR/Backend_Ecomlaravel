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

            // Primary Key
            $table->id();

            // Basic Information
            $table->string('name', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 150);
            $table->string('password', 255);
            $table->string('phone', 20)->nullable();

            // Profile Information
            $table->string('avatar', 255)->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('bio')->nullable();

            // Address Information
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            // Account Information
            $table->string('status', 20)->default('ACTIVE');
            $table->boolean('email_verified')->default(false);
            $table->timestamp('last_login')->nullable();

            // Timestamps
            $table->timestamps();

            // Unique Email
            $table->unique('email', 'uq_users_email');
        });

        // Status CHECK constraint
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT chk_users_status
            CHECK (status IN ('ACTIVE', 'INACTIVE', 'SUSPENDED'))
        ");

        // Gender CHECK constraint
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT chk_users_gender
            CHECK (
                gender IS NULL
                OR gender IN ('Male', 'Female', 'Other')
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};


// <?php
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB;

// return new class extends Migration
// {
//     public function up(): void
//     {
//         Schema::create('users', function (Blueprint $table) {
//             $table->id();
//             $table->string('name', 100);
//             $table->string('email', 150);
//             $table->string('password', 255);
//             $table->string('phone', 20)->nullable();
//             $table->string('status', 20)->default('ACTIVE');
//             $table->timestamps();

//             $table->unique('email', 'uq_users_email');
//         });

//         // status-like column -> CHECK constraint instead of native ENUM
//         DB::statement("
//             ALTER TABLE users
//             ADD CONSTRAINT chk_users_status
//             CHECK (status IN ('ACTIVE', 'INACTIVE', 'SUSPENDED'))
//         ");
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists('users');
//     }
// };
