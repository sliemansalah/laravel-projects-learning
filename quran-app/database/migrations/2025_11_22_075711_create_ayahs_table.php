<?php
// database/migrations/xxxx_xx_xx_create_ayahs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surah_id')->constrained()->onDelete('cascade');
            $table->integer('number'); // رقم الآية
            $table->longText('text'); // نص الآية
            $table->string('transliteration')->nullable(); // التشكيل
            $table->longText('translation')->nullable(); // الترجمة
            $table->integer('page_number'); // رقم الصفحة
            $table->integer('line_number'); // رقم السطر
            $table->integer('juz_number'); // رقم الجزء
            $table->string('hizb'); // الحزب
            $table->string('quarter')->nullable(); // الربع
            $table->timestamps();

            $table->unique(['surah_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayahs');
    }
};
