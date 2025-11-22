<?php
// database/migrations/xxxx_xx_xx_create_user_hifz_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_hifz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('surah_id')->constrained()->onDelete('cascade');
            $table->integer('start_ayah'); // رقم الآية الأولى
            $table->integer('end_ayah'); // رقم الآية الأخيرة
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'reviewing'])->default('not_started');
            $table->integer('memorized_count')->default(0); // عدد الآيات المحفوظة
            $table->integer('reviewed_count')->default(0); // عدد الآيات المراجعة
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'surah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_hifz');
    }
};
