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
            $table->string('street_address'); // renamed from title, mandatory
            $table->string('postal_code');
            $table->boolean('rental')->default(false);
            $table->integer('units');
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
