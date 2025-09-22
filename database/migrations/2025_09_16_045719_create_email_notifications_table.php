<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('hearing_id')->nullable();
            $table->foreign('hearing_id')->references('id')->on('hearings')->nullOnDelete();
            $table->enum('notification_type', ['hearing_created', 'day_of_reminder']);
            $table->string('email_address');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->text('failure_reason')->nullable();
            $table->boolean('opted_in')->default(true);
            $table->timestamp('created_at')->nullable();

            // Prevent duplicate notifications
            $table->unique(['user_id', 'hearing_id', 'notification_type'], 'unique_notification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
