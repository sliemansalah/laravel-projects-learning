<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('daily_goal')->default(5); // عدد الكلمات/الآيات اليومية
            $table->time('reminder_time')->default('20:00');
            $table->boolean('notifications_enabled')->default(true);
            $table->string('preferred_reciter')->default('alafasy');
            $table->boolean('dark_mode')->default(false);
            $table->integer('total_points')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->timestamp('last_activity_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'daily_goal',
                'reminder_time',
                'notifications_enabled',
                'preferred_reciter',
                'dark_mode',
                'total_points',
                'current_streak',
                'longest_streak',
                'last_activity_at'
            ]);
        });
    }
};
