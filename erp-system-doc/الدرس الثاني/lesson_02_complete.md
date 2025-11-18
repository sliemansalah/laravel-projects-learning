# 📄 الدرس الثاني: نظام الشركات والفروع المتعددة (Multi-Tenant)

---

## 🎯 الهدف من الدرس
بنهاية هذا الدرس ستكون قادراً على:
- إنشاء نظام شركات متعددة
- إدارة فروع لكل شركة
- التبديل بين الشركات
- حفظ الشركة النشطة في Session
- فصل بيانات كل شركة عن الأخرى

**⏱️ الوقت المتوقع:** 2-3 ساعات

---

## 📚 المفاهيم النظرية (10 دقائق)

### ما هو Multi-Tenant System؟
نظام يسمح لعدة شركات باستخدام نفس التطبيق، مع فصل بيانات كل شركة بشكل كامل.

**مثال واقعي:**
- شركة الرياض للتجارة
  - فرع الرياض الرئيسي
  - فرع جدة
  - فرع الدمام
- شركة جدة للمقاولات
  - فرع جدة الرئيسي
  - فرع مكة

كل شركة لها:
- بياناتها الخاصة (عملاء، فواتير، منتجات)
- إعداداتها الخاصة
- مستخدميها الخاصين

### كيف نفصل البيانات؟
سنستخدم `company_id` في كل جدول لفصل بيانات الشركات.

---

## 💻 التطبيق العملي - خطوة بخطوة

### 🔷 الخطوة 1: إنشاء جدول الشركات (10 دقائق)

#### أ) إنشاء Migration للشركات:

```bash
php artisan make:migration create_companies_table
```

#### ب) افتح الملف في:
`database/migrations/XXXX_XX_XX_create_companies_table.php`

#### ج) استبدل محتوى دالة `up()` بهذا:

```php
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
```

#### د) شغّل الـ Migration:

```bash
php artisan migrate
```

✅ **تحقق:** افتح phpMyAdmin، يجب أن ترى جدول `companies`

---

### 🔷 الخطوة 2: إنشاء جدول الفروع (10 دقائق)

#### أ) إنشاء Migration للفروع:

```bash
php artisan make:migration create_branches_table
```

#### ب) افتح الملف واستبدل `up()`:

```php
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
```

#### ج) شغّل الـ Migration:

```bash
php artisan migrate
```

✅ **تحقق:** يجب أن ترى جدول `branches` في phpMyAdmin

---

### 🔷 الخطوة 3: إنشاء Models (10 دقائق)

#### أ) إنشاء Model للشركة:

```bash
php artisan make:model Company
```

#### ب) افتح:
`app/Models/Company.php`

#### ج) استبدل المحتوى بهذا:

```php
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
```

#### د) إنشاء Model للفرع:

```bash
php artisan make:model Branch
```

#### هـ) افتح:
`app/Models/Branch.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'address',
        'city',
        'phone',
        'manager_name',
        'manager_phone',
        'is_main',
        'is_active',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean',
    ];

    // علاقة: الفرع ينتمي لشركة
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
```

---

### 🔷 الخطوة 4: ربط المستخدمين بالشركات (10 دقائق)

#### أ) إنشاء Migration لإضافة company_id للمستخدمين:

```bash
php artisan make:migration add_company_id_to_users_table
```

#### ب) افتح الملف:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('company_id')
              ->nullable()
              ->after('id')
              ->constrained()
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['company_id']);
        $table->dropColumn('company_id');
    });
}
```

#### ج) شغّل الـ Migration:

```bash
php artisan migrate
```

#### د) عدّل User Model:
افتح `app/Models/User.php` وأضف:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'company_id',  // أضف هذا
];

// أضف هذه الدالة في النهاية
public function company()
{
    return $this->belongsTo(Company::class);
}
```

---

### 🔷 الخطوة 5: إنشاء بيانات تجريبية (10 دقائق)

#### أ) إنشاء Seeder:

```bash
php artisan make:seeder CompanySeeder
```

#### ب) افتح:
`database/seeders/CompanySeeder.php`

```php
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
```

#### ج) شغّل الـ Seeder:

```bash
php artisan db:seed --class=CompanySeeder
```

✅ **تحقق:** افتح phpMyAdmin، يجب أن ترى شركتين و 4 فروع

---

### 🔷 الخطوة 6: ربط المستخدم الحالي بشركة (5 دقائق)

افتح phpMyAdmin وشغّل:

```sql
UPDATE users SET company_id = 1 WHERE email = 'admin@erp.com';
```

✅ الآن المستخدم `admin@erp.com` مرتبط بشركة الرياض

---

### 🔷 الخطوة 7: إنشاء Controller للشركات (15 دقيقة)

#### أ) إنشاء Controller:

```bash
php artisan make:controller CompanyController --resource
```

#### ب) افتح:
`app/Http/Controllers/CompanyController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // عرض جميع الشركات
    public function index()
    {
        $companies = Company::with('branches')->paginate(10);
        return view('companies.index', compact('companies'));
    }

    // عرض نموذج إضافة شركة
    public function create()
    {
        return view('companies.create');
    }

    // حفظ شركة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'tax_number' => 'required|string|unique:companies',
            'commercial_register' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
        ]);

        $validated['country'] = 'SA';
        $validated['is_active'] = true;

        Company::create($validated);

        return redirect()->route('companies.index')
            ->with('success', 'تم إضافة الشركة بنجاح');
    }

    // عرض شركة محددة
    public function show(Company $company)
    {
        $company->load('branches');
        return view('companies.show', compact('company'));
    }

    // تبديل الشركة النشطة
    public function switchCompany($companyId)
    {
        $company = Company::findOrFail($companyId);
        
        // حفظ الشركة في Session
        session(['company_id' => $company->id]);
        session(['company_name' => $company->name]);
        
        return redirect()->route('dashboard')
            ->with('success', "تم التبديل إلى: {$company->name}");
    }
}
```

---

### 🔷 الخطوة 8: إنشاء Views للشركات (20 دقيقة)

#### أ) أنشئ مجلد:
`resources/views/companies`

#### ب) أنشئ ملف:
`resources/views/companies/index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                الشركات
            </h2>
            <a href="{{ route('companies.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                إضافة شركة جديدة
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2">
                                <th class="text-right py-3">#</th>
                                <th class="text-right py-3">اسم الشركة</th>
                                <th class="text-right py-3">الرقم الضريبي</th>
                                <th class="text-right py-3">المدينة</th>
                                <th class="text-right py-3">عدد الفروع</th>
                                <th class="text-right py-3">الحالة</th>
                                <th class="text-right py-3">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <div class="font-bold">{{ $company->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $company->name_en }}</div>
                                    </td>
                                    <td class="py-3">{{ $company->tax_number }}</td>
                                    <td class="py-3">{{ $company->city }}</td>
                                    <td class="py-3">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            {{ $company->branches->count() }} فرع
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if($company->is_active)
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">نشطة</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">غير نشطة</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="flex gap-2">
                                            <a href="{{ route('companies.show', $company) }}" 
                                               class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                                عرض
                                            </a>
                                            <form action="{{ route('companies.switch', $company) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">
                                                    تبديل
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">
                                        لا توجد شركات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

#### ج) أنشئ ملف:
`resources/views/companies/show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تفاصيل الشركة: {{ $company->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- معلومات الشركة --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">📋 معلومات الشركة</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">اسم الشركة (عربي)</p>
                            <p class="font-bold">{{ $company->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">اسم الشركة (إنجليزي)</p>
                            <p class="font-bold">{{ $company->name_en ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الرقم الضريبي</p>
                            <p class="font-bold">{{ $company->tax_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">السجل التجاري</p>
                            <p class="font-bold">{{ $company->commercial_register }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">العنوان</p>
                            <p class="font-bold">{{ $company->address }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">المدينة</p>
                            <p class="font-bold">{{ $company->city }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الهاتف</p>
                            <p class="font-bold">{{ $company->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                            <p class="font-bold">{{ $company->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الفروع --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">🏢 الفروع ({{ $company->branches->count() }})</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($company->branches as $branch)
                            <div class="border rounded-lg p-4 {{ $branch->is_main ? 'bg-blue-50 border-blue-300' : 'bg-gray-50' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-lg">{{ $branch->name }}</h4>
                                    @if($branch->is_main)
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">رئيسي</span>
                                    @endif
                                </div>
                                
                                <div class="space-y-1 text-sm">
                                    <p><span class="text-gray-600">الكود:</span> <span class="font-bold">{{ $branch->code }}</span></p>
                                    <p><span class="text-gray-600">المدينة:</span> {{ $branch->city }}</p>
                                    <p><span class="text-gray-600">العنوان:</span> {{ $branch->address }}</p>
                                    <p><span class="text-gray-600">الهاتف:</span> {{ $branch->phone }}</p>
                                    @if($branch->manager_name)
                                        <p><span class="text-gray-600">المدير:</span> {{ $branch->manager_name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
```

---

### 🔷 الخطوة 9: إضافة Routes (5 دقائق)

افتح `routes/web.php` وأضف:

```php
use App\Http\Controllers\CompanyController;

Route::middleware(['auth'])->group(function () {
    // ... الكود الموجود ...
    
    // Routes الشركات
    Route::resource('companies', CompanyController::class);
    Route::post('companies/{company}/switch', [CompanyController::class, 'switchCompany'])
        ->name('companies.switch');
});
```

---

### 🔷 الخطوة 10: إضافة رابط الشركات للقائمة (5 دقائق)

افتح `resources/views/layouts/navigation.blade.php` وأضف:

```blade
<x-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
    {{ __('الشركات') }}
</x-nav-link>
```

---

### 🔷 الخطوة 11: عرض الشركة النشطة في Header (10 دقائق)

عدّل `resources/views/layouts/app.blade.php`

ابحث عن:
```blade
<header class="bg-white shadow">
```

استبدله بـ:

```blade
<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div>
                {{ $header }}
            </div>
            
            @if(session('company_name'))
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                    <span class="text-sm">الشركة النشطة:</span>
                    <span class="font-bold">{{ session('company_name') }}</span>
                </div>
            @endif
        </div>
    </div>
</header>
```

---

## ✅ النتيجة المتوقعة

بعد إتمام جميع الخطوات:

✅ جدول companies يحتوي على شركتين
✅ جدول branches يحتوي على 4 فروع
✅ صفحة عرض جميع الشركات تعمل
✅ صفحة تفاصيل الشركة تعرض الفروع
✅ زر "تبديل" يغير الشركة النشطة
✅ اسم الشركة النشطة يظهر في الـ Header

---

## 🐛 المشاكل الشائعة وحلولها

### ❌ **مشكلة:** "Class 'Company' not found"
**✅ الحل:** تأكد من كتابة `use App\Models\Company;` في أول الـ Controller

### ❌ **مشكلة:** "SQLSTATE[23000]: Integrity constraint violation"
**✅ الحل:** تأكد من وجود `company_id` عند إنشاء سجل جديد

### ❌ **مشكلة:** Session لا تحفظ الشركة
**✅ الحل:** تأكد من أن `php artisan serve` يعمل بشكل صحيح

---

## 💡 نصائح وأفضل الممارسات

1. **استخدم Global Scope** لفلترة البيانات حسب الشركة تلقائياً (سنتعلمه لاحقاً)
2. **احفظ company_id في Session** عند تسجيل الدخول
3. **تحقق من الصلاحيات** قبل السماح بالتبديل بين الشركات

---

## 📝 التمرين العملي

الآن دورك! نفذ المهام التالية **بنفسك**:

### ✏️ **تمرين 1:** أضف شركة جديدة
1. أنشئ نموذج إضافة شركة (`companies/create.blade.php`)
2. املأ الحقول المطلوبة
3. احفظ الشركة في قاعدة البيانات
4. أضف لها فرعين على الأقل

**Hint:** استخدم نفس تصميم صفحات Breeze

### ✏️ **تمرين 2:** أنشئ Controller للفروع
1. أنشئ `BranchController`
2. أضف دالة `index()` لعرض جميع الفروع
3. أنشئ صفحة `branches/index.blade.php`
4. اعرض اسم الفرع، المدينة، والشركة التابع لها
5. أضف رابط في القائمة

### ✏️ **تمرين 3:** عدّل Dashboard
عدّل بطاقات الإحصائيات في Dashboard لتعرض:
- عدد الشركات الموجودة
- عدد الفروع للشركة النشطة
- اسم الفرع الرئيسي

**Hint:**
```php
$companiesCount = Company::count();
$branchesCount = Branch::where('company_id', session('company_id'))->count();
```

---

## 🔧 كود إضافي مفيد

### إنشاء Middleware للتحقق من الشركة النشطة

```bash
php artisan make:middleware EnsureCompanySelected
```

افتح `app/Http/Middleware/EnsureCompanySelected.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    public function handle(Request $request, Closure $next): Response
    {
        // إذا لم يكن هناك شركة نشطة، اختر الشركة الأولى تلقائياً
        if (!session()->has('company_id')) {
            $user = auth()->user();
            
            if ($user && $user->company_id) {
                session([
                    'company_id' => $user->company_id,
                    'company_name' => $user->company->name
                ]);
            } else {
                // إذا لم يكن المستخدم مرتبط بشركة، اختر أول شركة
                $firstCompany = \App\Models\Company::first();
                if ($firstCompany) {
                    session([
                        'company_id' => $firstCompany->id,
                        'company_name' => $firstCompany->name
                    ]);
                }
            }
        }
        
        return $next($request);
    }
}
```

سجّل الـ Middleware في `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... الكود الموجود
        \App\Http\Middleware\EnsureCompanySelected::class,
    ],
];
```

---

## 📊 إضافة مؤشر الشركة النشطة في Dashboard

عدّل `app/Http/Controllers/DashboardController.php` (أنشئه إذا لم يكن موجوداً):

```bash
php artisan make:controller DashboardController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Branch;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');
        
        $stats = [
            'total_companies' => Company::count(),
            'total_branches' => Branch::where('company_id', $companyId)->count(),
            'active_company' => Company::find($companyId),
        ];
        
        return view('dashboard', $stats);
    }
}
```

عدّل Route في `routes/web.php`:

```php
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
```

عدّل `resources/views/dashboard.blade.php` لاستخدام البيانات:

```blade
{{-- بعد رسالة الترحيب --}}
@if($active_company)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                🏢
            </div>
            <div class="mr-3">
                <p class="text-sm text-blue-700">
                    تعمل حالياً على: <strong>{{ $active_company->name }}</strong>
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    عدد الفروع: {{ $total_branches }}
                </p>
            </div>
        </div>
    </div>
@endif
```

---

## 🎨 تحسين صفحة اختيار الشركة

أنشئ صفحة مخصصة لاختيار الشركة:

### أ) أنشئ Route:
```php
Route::get('/select-company', [CompanyController::class, 'selectCompany'])
    ->name('companies.select')
    ->middleware('auth');
```

### ب) أضف دالة في CompanyController:
```php
public function selectCompany()
{
    $companies = Company::where('is_active', true)->get();
    return view('companies.select', compact('companies'));
}
```

### ج) أنشئ View:
`resources/views/companies/select.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            اختر الشركة
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">اختر الشركة التي تريد العمل عليها</h3>
                    <p class="text-gray-600">يمكنك التبديل بين الشركات في أي وقت</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($companies as $company)
                    <form action="{{ route('companies.switch', $company) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-right">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition border-2 hover:border-blue-500 {{ session('company_id') == $company->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="text-4xl">🏢</div>
                                        @if(session('company_id') == $company->id)
                                            <span class="bg-blue-500 text-white text-xs px-3 py-1 rounded-full">
                                                نشط الآن
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="text-xl font-bold mb-2">{{ $company->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $company->name_en }}</p>
                                    
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center text-gray-600">
                                            <span class="ml-2">📍</span>
                                            {{ $company->city }}
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <span class="ml-2">🏪</span>
                                            {{ $company->branches->count() }} فرع
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <span class="ml-2">📞</span>
                                            {{ $company->phone }}
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t">
                                        @if(session('company_id') == $company->id)
                                            <span class="text-blue-600 font-bold">✓ تعمل حالياً على هذه الشركة</span>
                                        @else
                                            <span class="text-gray-600">اضغط للتبديل إلى هذه الشركة</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
```

### د) أضف رابط في القائمة:
```blade
<x-nav-link :href="route('companies.select')" :active="request()->routeIs('companies.select')">
    {{ __('تبديل الشركة') }}
</x-nav-link>
```

---

## 📱 إضافة Dropdown لاختيار الشركة في الـ Navigation

عدّل `resources/views/layouts/navigation.blade.php`:

ابحث عن قسم Settings Dropdown وأضف قبله:

```blade
{{-- Company Selector --}}
@if(auth()->check())
    <div class="hidden sm:flex sm:items-center sm:ms-6">
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <div class="flex items-center">
                        <span class="ml-2">🏢</span>
                        <div class="text-right">
                            <div class="text-xs text-gray-400">الشركة النشطة</div>
                            <div class="font-semibold">{{ session('company_name', 'اختر شركة') }}</div>
                        </div>
                    </div>

                    <div class="ms-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2 text-xs text-gray-400">
                    اختر الشركة
                </div>
                
                @php
                    $companies = \App\Models\Company::where('is_active', true)->get();
                @endphp
                
                @foreach($companies as $company)
                    <form method="POST" action="{{ route('companies.switch', $company) }}">
                        @csrf
                        <button type="submit" class="w-full text-right block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ session('company_id') == $company->id ? 'bg-blue-50 font-bold' : '' }}">
                            {{ session('company_id') == $company->id ? '✓ ' : '' }}{{ $company->name }}
                        </button>
                    </form>
                @endforeach
                
                <div class="border-t border-gray-200"></div>
                
                <x-dropdown-link :href="route('companies.index')">
                    إدارة الشركات
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
@endif
```

---

## ✅ Checklist - تأكد من إتمام كل شيء

قبل الانتقال للدرس القادم، تأكد من:

- [ ] جدول companies يحتوي على بيانات
- [ ] جدول branches مرتبط بالشركات
- [ ] صفحة عرض الشركات تعمل بشكل صحيح
- [ ] صفحة تفاصيل الشركة تعرض الفروع
- [ ] يمكنك التبديل بين الشركات
- [ ] الشركة النشطة تظهر في الـ Header
- [ ] فهمت مفهوم Multi-Tenant
- [ ] أنجزت التمارين الثلاثة

---

## 🔗 المصادر الإضافية

- 📚 [Laravel Relationships](https://laravel.com/docs/eloquent-relationships)
- 📚 [Laravel Sessions](https://laravel.com/docs/session)
- 📚 [Multi-Tenancy in Laravel](https://laravel.com/docs/sanctum#multi-tenancy)

---

## 🎯 الدرس القادم

في الدرس القادم سنبني:
- **السنوات المالية والفترات المحاسبية**
- جدول fiscal_years (السنوات المالية)
- جدول fiscal_periods (الفترات الشهرية)
- إقفال الفترات والسنوات
- ربط كل عملية مالية بفترة محددة

---

## 🎓 ما تعلمناه في هذا الدرس

✅ إنشاء علاقات بين الجداول (hasMany, belongsTo)
✅ استخدام Foreign Keys
✅ التعامل مع Sessions في Laravel
✅ إنشاء Seeders لبيانات تجريبية
✅ بناء صفحات CRUD كاملة
✅ استخدام Blade Components
✅ التعامل مع Pagination
✅ إنشاء Forms وحفظ البيانات

---

## 💪 تحدي إضافي (اختياري)

إذا أنهيت كل شيء بسرعة، جرّب:

1. **أضف إمكانية تعطيل الشركة:** زر لتغيير `is_active` من true إلى false
2. **أضف إحصائيات للشركة:** عدد المستخدمين، عدد الفواتير، إلخ
3. **أضف صورة شعار للشركة:** استخدم Laravel Storage لرفع الصور
4. **أضف فلترة وبحث:** ابحث عن الشركات حسب الاسم أو المدينة

---

**تذكر:** المفتاح هو الممارسة اليومية! لا تنتقل للدرس القادم حتى تفهم هذا الدرس تماماً 💪

**انتهى الدرس الثاني** ✅