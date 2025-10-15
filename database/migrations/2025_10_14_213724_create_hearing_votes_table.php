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
        Schema::create('hearing_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hearing_id')->constrained()->onDelete('cascade');
            $table->date('vote_date')->nullable();
            $table->boolean('passed')->nullable(); // did the vote pass?
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearing_votes');
    }
};
