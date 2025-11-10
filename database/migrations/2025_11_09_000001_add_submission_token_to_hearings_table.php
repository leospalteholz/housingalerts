<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->string('submission_token', 64)
                ->nullable()
                ->unique('hearings_submission_token_unique')
                ->after('comments_email');
        });
    }

    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropUnique('hearings_submission_token_unique');
            $table->dropColumn('submission_token');
        });
    }
};
