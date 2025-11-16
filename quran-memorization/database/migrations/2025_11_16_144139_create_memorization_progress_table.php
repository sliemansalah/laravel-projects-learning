<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memorization_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ayah_id')->constrained()->onDelete('cascade');
            $table->foreignId('word_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('status', ['new', 'learning', 'reviewing', 'mastered'])->default('new');
            $table->integer('strength')->default(1); // 1-10
            $table->integer('review_count')->default(0);
            $table->integer('mistake_count')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'ayah_id', 'word_id']);
            $table->index(['user_id', 'next_review_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memorization_progress');
    }
};
