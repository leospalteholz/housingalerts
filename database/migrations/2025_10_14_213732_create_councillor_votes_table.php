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
        Schema::create('councillor_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hearing_vote_id')->constrained()->onDelete('cascade');
            $table->foreignId('councillor_id')->constrained()->onDelete('cascade');
            $table->enum('vote', ['for', 'against', 'abstain', 'absent'])->default('absent');
            $table->timestamps();
            
            // Ensure each councillor can only vote once per hearing vote
            $table->unique(['hearing_vote_id', 'councillor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('councillor_votes');
    }
};
