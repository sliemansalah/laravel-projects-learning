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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')                  // ربط بالشركة
                ->constrained()
                ->cascadeOnDelete();                       // عند حذف الشركة، احذف فروعها
            $table->string('name');                          // اسم الفرع
            $table->string('code')->unique();                // كود الفرع (BR-001)
            $table->text('address');                         // العنوان
            $table->string('city');                          // المدينة
            $table->string('phone');                         // الهاتف
            $table->string('manager_name')->nullable();      // اسم المدير
            $table->string('manager_phone')->nullable();     // هاتف المدير
            $table->boolean('is_main')->default(false);      // فرع رئيسي؟
            $table->boolean('is_active')->default(true);     // نشط؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
