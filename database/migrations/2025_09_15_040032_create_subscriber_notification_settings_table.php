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
        Schema::create('subscriber_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->boolean('notify_development_hearings')->default(true);
            $table->boolean('notify_policy_hearings')->default(true);
            $table->boolean('send_day_of_reminders')->default(false);
            $table->timestamps();

            $table->unique('subscriber_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_notification_settings');
    }
};
