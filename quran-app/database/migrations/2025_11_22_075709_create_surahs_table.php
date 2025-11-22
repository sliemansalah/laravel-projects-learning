<?php
// database/migrations/xxxx_xx_xx_create_surahs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم السورة
            $table->string('name_en'); // الاسم الإنجليزي
            $table->integer('number'); // رقم السورة
            $table->integer('ayah_count'); // عدد الآيات
            $table->string('revelation_place'); // مكان النزول (مكي/مدني)
            $table->integer('juz_number'); // رقم الجزء
            $table->text('description')->nullable(); // وصف السورة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
