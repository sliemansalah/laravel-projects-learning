<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Branch;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // شركة 1: شركة الرياض للتجارة
        $riyadh = Company::create([
            'name' => 'شركة الرياض للتجارة',
            'name_en' => 'Riyadh Trading Company',
            'tax_number' => '300000000100003',
            'commercial_register' => '1010000001',
            'address' => 'شارع الملك فهد، الرياض',
            'city' => 'الرياض',
            'country' => 'SA',
            'phone' => '0112345678',
            'email' => 'info@riyadh-trading.com',
            'is_active' => true,
        ]);

        // فروع شركة الرياض
        Branch::create([
            'company_id' => $riyadh->id,
            'name' => 'الفرع الرئيسي - الرياض',
            'code' => 'RY-001',
            'address' => 'شارع الملك فهد، الرياض',
            'city' => 'الرياض',
            'phone' => '0112345678',
            'manager_name' => 'أحمد محمد',
            'is_main' => true,
            'is_active' => true,
        ]);

        Branch::create([
            'company_id' => $riyadh->id,
            'name' => 'فرع جدة',
            'code' => 'RY-002',
            'address' => 'شارع التحلية، جدة',
            'city' => 'جدة',
            'phone' => '0122345678',
            'manager_name' => 'خالد أحمد',
            'is_main' => false,
            'is_active' => true,
        ]);

        // شركة 2: شركة جدة للمقاولات
        $jeddah = Company::create([
            'name' => 'شركة جدة للمقاولات',
            'name_en' => 'Jeddah Contracting Company',
            'tax_number' => '300000000200003',
            'commercial_register' => '4030000001',
            'address' => 'شارع الأمير سلطان، جدة',
            'city' => 'جدة',
            'country' => 'SA',
            'phone' => '0123456789',
            'email' => 'info@jeddah-contracting.com',
            'is_active' => true,
        ]);

        // فروع شركة جدة
        Branch::create([
            'company_id' => $jeddah->id,
            'name' => 'الفرع الرئيسي - جدة',
            'code' => 'JD-001',
            'address' => 'شارع الأمير سلطان، جدة',
            'city' => 'جدة',
            'phone' => '0123456789',
            'manager_name' => 'محمد عبدالله',
            'is_main' => true,
            'is_active' => true,
        ]);

        Branch::create([
            'company_id' => $jeddah->id,
            'name' => 'فرع مكة',
            'code' => 'JD-002',
            'address' => 'شارع إبراهيم الخليل، مكة',
            'city' => 'مكة',
            'phone' => '0125555555',
            'manager_name' => 'عبدالرحمن سعيد',
            'is_main' => false,
            'is_active' => true,
        ]);
    }
}
