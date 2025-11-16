<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ayah_id')->constrained()->onDelete('cascade');
            $table->integer('position');
            $table->string('text_arabic');
            $table->string('text_uthmani');
            $table->string('transliteration')->nullable();
            $table->string('translation')->nullable();
            $table->text('meaning')->nullable();
            $table->timestamps();

            $table->unique(['ayah_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
