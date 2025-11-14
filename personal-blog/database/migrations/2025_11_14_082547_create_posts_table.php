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
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // معرف فريد
            $table->string('title'); // عنوان مقال
            $table->string('slug')->unique(); // رابط ودي
            $table->text('excerpt'); // ملخص
            $table->longText('content'); // محتوى
            $table->string('image')->nullable(); // صورة المقال
            $table->boolean('published')->default(false); // حالة النشر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
