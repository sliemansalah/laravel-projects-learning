<?php
// database/migrations/xxxx_xx_xx_create_hifz_pages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hifz_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('page_number'); // رقم الصفحة
            $table->enum('status', ['not_memorized', 'memorizing', 'memorized', 'reviewing'])->default('not_memorized');
            $table->integer('review_count')->default(0); // عدد مرات المراجعة
            $table->date('memorized_date')->nullable(); // تاريخ الحفظ
            $table->date('last_review_date')->nullable(); // تاريخ آخر مراجعة
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();

            $table->unique(['user_id', 'page_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hifz_pages');
    }
};
