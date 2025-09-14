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
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['development', 'policy'])->default('development');
            $table->string('title')->nullable(); // for policy hearings, auto-generated for development
            $table->string('street_address')->nullable(); // only for development hearings
            $table->string('postal_code')->nullable(); // only for development hearings
            $table->boolean('rental')->nullable(); // only for development hearings
            $table->integer('units')->nullable(); // only for development hearings
            $table->text('description'); // details of the hearing
            $table->string('image_url')->nullable();
            $table->date('start_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('more_info_url')->nullable();
            $table->text('remote_instructions'); // how to join (phone/virtual), could contain links
            $table->text('inperson_instructions'); // how to join in person
            $table->string('comments_email'); // email to submit comments
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};
