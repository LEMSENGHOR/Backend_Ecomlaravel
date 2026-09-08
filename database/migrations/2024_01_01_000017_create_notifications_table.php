<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->string('title', 200);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id', 'fk_notification_user')
                ->references('id')->on('users')
                ->onDelete('cascade'); // dependent on its user

            $table->index('user_id', 'idx_notifications_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
