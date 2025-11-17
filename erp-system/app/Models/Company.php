<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'tax_number',
        'commercial_register',
        'logo',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // علاقة: الشركة لها فروع متعددة
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    // علاقة: الشركة لها مستخدمين
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // دالة مساعدة: الحصول على الفرع الرئيسي
    public function mainBranch()
    {
        return $this->branches()->where('is_main', true)->first();
    }
}
