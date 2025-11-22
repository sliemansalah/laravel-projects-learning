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
        Schema::table('user_hifz', function (Blueprint $table) {
            $table->timestamp('last_reviewed_at')->nullable()->after('completion_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_hifz', function (Blueprint $table) {
            $table->dropColumn('last_reviewed_at');
        });
    }
};
