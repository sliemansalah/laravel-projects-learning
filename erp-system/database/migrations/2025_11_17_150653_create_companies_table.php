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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // اسم الشركة
            $table->string('name_en')->nullable();           // الاسم بالإنجليزي
            $table->string('tax_number')->unique();          // الرقم الضريبي
            $table->string('commercial_register');           // السجل التجاري
            $table->string('logo')->nullable();              // شعار الشركة
            $table->text('address');                         // العنوان
            $table->string('city');                          // المدينة
            $table->string('country')->default('SA');        // الدولة
            $table->string('phone');                         // الهاتف
            $table->string('email');                         // البريد
            $table->string('website')->nullable();           // الموقع
            $table->boolean('is_active')->default(true);     // نشطة؟
            $table->timestamps();                            // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
