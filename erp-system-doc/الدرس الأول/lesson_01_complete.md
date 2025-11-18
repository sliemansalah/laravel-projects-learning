# 📄 الدرس الأول: إنشاء مشروع ERP والإعداد الأولي

---

## 🎯 الهدف من الدرس
بنهاية هذا الدرس ستكون قادراً على:
- إنشاء مشروع Laravel جديد للـ ERP
- فهم هيكل المجلدات والملفات
- إعداد قاعدة البيانات
- تثبيت نظام المصادقة (تسجيل دخول)
- رؤية أول صفحة تعمل في المشروع

**⏱️ الوقت المتوقع:** 2-3 ساعات

---

## 📚 المفاهيم النظرية (10 دقائق - اقرأ بتركيز!)

### ما هو Laravel؟
Laravel هو إطار عمل PHP يساعدك على بناء تطبيقات ويب قوية بسرعة. يوفر لك:
- **نظام توجيه (Routing)**: لتحديد صفحات الموقع
- **قاعدة بيانات (Database)**: للتعامل مع البيانات بسهولة
- **قوالب (Blade)**: لإنشاء واجهات المستخدم
- **أمان مدمج**: حماية ضد الاختراق

### هيكل مشروع Laravel الأساسي:
```
erp-system/
├── app/               # كود التطبيق الرئيسي
│   ├── Http/          # Controllers والـ Middleware
│   ├── Models/        # نماذج قاعدة البيانات
│   └── Services/      # منطق الأعمال
├── database/          # قاعدة البيانات
│   ├── migrations/    # جداول قاعدة البيانات
│   └── seeders/       # بيانات تجريبية
├── public/            # الملفات العامة (CSS, JS, صور)
├── resources/         # الواجهات (Blade)
│   └── views/         # ملفات HTML
├── routes/            # مسارات الموقع
│   └── web.php        # المسارات الرئيسية
└── .env               # الإعدادات (قاعدة البيانات، إلخ)
```

---

## 💻 التطبيق العملي - تابع معي خطوة بخطوة

### 🔷 الخطوة 1: إنشاء المشروع (5 دقائق)

افتح **Command Prompt** أو **Terminal** واكتب:

```bash
# انتقل للمجلد الذي تريد إنشاء المشروع فيه
cd C:\xampp\htdocs
# أو
cd ~/Desktop

# إنشاء مشروع Laravel جديد باسم erp-system
composer create-project laravel/laravel erp-system

# انتقل لمجلد المشروع
cd erp-system
```

**⏳ انتظر 2-3 دقائق حتى يكتمل التثبيت...**

✅ **تحقق:** يجب أن ترى رسالة `Application ready!`

---

### 🔷 الخطوة 2: تشغيل المشروع (دقيقة واحدة)

```bash
# شغّل السيرفر المحلي
php artisan serve
```

✅ **افتح المتصفح واذهب إلى:** http://localhost:8000

يجب أن ترى **صفحة Laravel الترحيبية** 🎉

**⚠️ مهم:** اترك هذه النافذة مفتوحة، وافتح نافذة terminal جديدة للأوامر التالية!

---

### 🔷 الخطوة 3: إعداد قاعدة البيانات (5 دقائق)

#### أ) إنشاء قاعدة البيانات

افتح **phpMyAdmin** (http://localhost/phpmyadmin)

```sql
CREATE DATABASE erp_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

أو استخدم MySQL من الـ terminal:
```bash
mysql -u root -p
CREATE DATABASE erp_system;
exit;
```

#### ب) ربط Laravel بقاعدة البيانات

افتح ملف `.env` في **مجلد المشروع** بأي محرر نصوص وعدّل:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_system
DB_USERNAME=root
DB_PASSWORD=          # ضع كلمة المرور إذا كان لديك واحدة
```

✅ **احفظ الملف**

#### ج) اختبار الاتصال

في الـ terminal:
```bash
php artisan migrate
```

✅ **يجب أن ترى:** `Migration table created successfully`

---

### 🔷 الخطوة 4: تثبيت نظام المصادقة (10 دقائق)

سنستخدم **Laravel Breeze** لنظام تسجيل دخول جاهز:

```bash
# تثبيت Breeze
composer require laravel/breeze --dev

# تثبيت نظام المصادقة
php artisan breeze:install blade

# تثبيت مكتبات JavaScript
npm install

# بناء ملفات CSS و JS
npm run build
```

⏳ **انتظر 3-5 دقائق...**

```bash
# تشغيل الـ migrations لإنشاء جداول المستخدمين
php artisan migrate
```

✅ **تحقق:** افتح phpMyAdmin، يجب أن ترى جداول مثل `users`, `password_resets`

---

### 🔷 الخطوة 5: أول تسجيل دخول (5 دقائق)

#### أ) افتح المتصفح:
http://localhost:8000/register

#### ب) سجّل مستخدم جديد:
- **Name:** Admin
- **Email:** admin@erp.com
- **Password:** password123
- **Confirm Password:** password123

اضغط **Register**

✅ **يجب أن تنتقل تلقائياً لصفحة Dashboard!**

---

### 🔷 الخطوة 6: تخصيص صفحة Dashboard (15 دقيقة)

الآن سنعدّل الصفحة الرئيسية لتكون خاصة بنظام ERP!

#### أ) افتح ملف:
`resources/views/dashboard.blade.php`

#### ب) استبدل المحتوى بالكود التالي:

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة التحكم - نظام ERP') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- رسالة الترحيب --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-2">مرحباً {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-gray-600">أهلاً بك في نظام ERP المتكامل</p>
                </div>
            </div>

            {{-- بطاقات الإحصائيات --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                
                {{-- إجمالي المبيعات --}}
                <div class="bg-blue-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">إجمالي المبيعات</p>
                                <p class="text-3xl font-bold mt-2">0.00 ريال</p>
                            </div>
                            <div class="text-4xl opacity-50">💰</div>
                        </div>
                    </div>
                </div>

                {{-- عدد الفواتير --}}
                <div class="bg-green-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">عدد الفواتير</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                            </div>
                            <div class="text-4xl opacity-50">📄</div>
                        </div>
                    </div>
                </div>

                {{-- عدد العملاء --}}
                <div class="bg-purple-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">عدد العملاء</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                            </div>
                            <div class="text-4xl opacity-50">👥</div>
                        </div>
                    </div>
                </div>

                {{-- المخزون --}}
                <div class="bg-orange-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">المنتجات</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                            </div>
                            <div class="text-4xl opacity-50">📦</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- الوحدات الرئيسية --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">الوحدات الرئيسية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- المحاسبة --}}
                        <a href="#" class="block p-6 bg-gray-50 hover:bg-gray-100 rounded-lg border-2 border-gray-200 hover:border-blue-500 transition">
                            <div class="text-4xl mb-3">📊</div>
                            <h4 class="text-lg font-bold mb-2">المحاسبة</h4>
                            <p class="text-sm text-gray-600">شجرة الحسابات، القيود، التقارير المالية</p>
                        </a>

                        {{-- المبيعات --}}
                        <a href="#" class="block p-6 bg-gray-50 hover:bg-gray-100 rounded-lg border-2 border-gray-200 hover:border-green-500 transition">
                            <div class="text-4xl mb-3">🛒</div>
                            <h4 class="text-lg font-bold mb-2">المبيعات</h4>
                            <p class="text-sm text-gray-600">الفواتير، العملاء، عروض الأسعار</p>
                        </a>

                        {{-- المشتريات --}}
                        <a href="#" class="block p-6 bg-gray-50 hover:bg-gray-100 rounded-lg border-2 border-gray-200 hover:border-purple-500 transition">
                            <div class="text-4xl mb-3">🛍️</div>
                            <h4 class="text-lg font-bold mb-2">المشتريات</h4>
                            <p class="text-sm text-gray-600">الموردين، فواتير المشتريات</p>
                        </a>

                        {{-- المخزون --}}
                        <a href="#" class="block p-6 bg-gray-50 hover:bg-gray-100 rounded-lg border-2 border-gray-200 hover:border-orange-500 transition">
                            <div class="text-4xl mb-3">📦</div>
                            <h4 class="text-lg font-bold mb-2">المخزون</h4>
                            <p class="text-sm text-gray-600">المنتجات، المستودعات، حركات المخزون</p>
                        </a>

                        {{-- الموارد البشرية --}}
                        <a href="#" class="block p-6 bg-gray-50 hover:bg-gray-100 rounded-lg border-2 border-gray-200 hover:border-red-500 transition">
                            <div class="text-4xl mb-3">👥</div>
                            <h4 class="text-lg font-bold mb-2">الموارد البشرية</h4>
                            <p class="text-sm text-gray-600">الموظفين، الحضور، الرواتب</p>
                        </a>

                        {{-- التقارير --}}
                        <a href="#" class="block p-6 bg-gray-50 hover:bg-gray-100 rounded-lg border-2 border-gray-200 hover:border-indigo-500 transition">
                            <div class="text-4xl mb-3">📈</div>
                            <h4 class="text-lg font-bold mb-2">التقارير</h4>
                            <p class="text-sm text-gray-600">التقارير المالية والتحليلية</p>
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
```

#### ج) احفظ الملف وحدّث المتصفح

✅ **يجب أن ترى Dashboard جميلة بالعربي مع بطاقات ملونة!**

---

### 🔷 الخطوة 7: تعريب القوائم (10 دقائق)

#### أ) افتح ملف:
`resources/views/layouts/navigation.blade.php`

#### ب) ابحث عن السطر:
```blade
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
```

#### ج) استبدل القائمة بهذا:

```blade
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('لوحة التحكم') }}
    </x-nav-link>
    
    <x-nav-link href="#" :active="false">
        {{ __('المحاسبة') }}
    </x-nav-link>
    
    <x-nav-link href="#" :active="false">
        {{ __('المبيعات') }}
    </x-nav-link>
    
    <x-nav-link href="#" :active="false">
        {{ __('المشتريات') }}
    </x-nav-link>
    
    <x-nav-link href="#" :active="false">
        {{ __('المخزون') }}
    </x-nav-link>
</div>
```

✅ **احفظ وحدّث المتصفح - يجب أن ترى القوائم بالعربي!**

---

### 🔷 الخطوة 8: إنشاء صفحة معلومات النظام (5 دقائق)

#### أ) أنشئ Controller جديد:

```bash
php artisan make:controller SystemInfoController
```

#### ب) افتح الملف:
`app/Http/Controllers/SystemInfoController.php`

#### ج) استبدل المحتوى بهذا:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemInfoController extends Controller
{
    public function index()
    {
        $systemInfo = [
            'system_name' => 'نظام ERP المتكامل',
            'version' => '1.0.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => config('database.default'),
        ];

        return view('system-info', compact('systemInfo'));
    }
}
```

#### د) أنشئ ملف View:
`resources/views/system-info.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            معلومات النظام
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">📋 معلومات النظام</h3>
                    
                    <table class="w-full">
                        <tr class="border-b">
                            <td class="py-3 font-bold">اسم النظام:</td>
                            <td class="py-3">{{ $systemInfo['system_name'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">الإصدار:</td>
                            <td class="py-3">{{ $systemInfo['version'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">Laravel:</td>
                            <td class="py-3">{{ $systemInfo['laravel_version'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">PHP:</td>
                            <td class="py-3">{{ $systemInfo['php_version'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">قاعدة البيانات:</td>
                            <td class="py-3">{{ $systemInfo['database'] }}</td>
                        </tr>
                    </table>

                    <div class="mt-6 p-4 bg-green-100 rounded">
                        <p class="text-green-800 font-bold">✅ النظام يعمل بشكل صحيح!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

#### هـ) أضف Route:
افتح `routes/web.php` وأضف في النهاية:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/system-info', [App\Http\Controllers\SystemInfoController::class, 'index'])
        ->name('system-info');
});
```

#### و) أضف الرابط في القائمة:
عدّل `resources/views/layouts/navigation.blade.php` وأضف:

```blade
<x-nav-link :href="route('system-info')" :active="request()->routeIs('system-info')">
    {{ __('معلومات النظام') }}
</x-nav-link>
```

✅ **اذهب إلى:** http://localhost:8000/system-info

---

## ✅ النتيجة المتوقعة

بعد إتمام جميع الخطوات، يجب أن يكون لديك:

✅ مشروع Laravel يعمل على http://localhost:8000
✅ نظام تسجيل دخول/تسجيل مستخدم جديد
✅ Dashboard معرّبة بالكامل
✅ قوائم تنقل بالعربي
✅ 4 بطاقات إحصائية ملونة
✅ 6 وحدات رئيسية (محاسبة، مبيعات، مشتريات، مخزون، HR، تقارير)
✅ صفحة معلومات النظام

---

## 🐛 المشاكل الشائعة وحلولها

### ❌ **مشكلة:** "composer not found"
**✅ الحل:** تأكد من تثبيت Composer من https://getcomposer.org

### ❌ **مشكلة:** "SQLSTATE[HY000] [1045] Access denied"
**✅ الحل:** تحقق من بيانات قاعدة البيانات في ملف `.env`

### ❌ **مشكلة:** "npm not found"
**✅ الحل:** ثبّت Node.js من https://nodejs.org

### ❌ **مشكلة:** الصفحة لا تعرض CSS بشكل صحيح
**✅ الحل:** شغّل `npm run build` مرة أخرى

### ❌ **مشكلة:** "419 Page Expired" عند التسجيل
**✅ الحل:** امسح الـ cache بـ `php artisan cache:clear`

---

## 💡 نصائح وأفضل الممارسات

1. **احفظ التغييرات باستمرار** على Git:
```bash
git init
git add .
git commit -m "الدرس الأول: إعداد المشروع الأساسي"
```

2. **اترك السيرفر يعمل** في نافذة منفصلة
3. **استخدم محرر نصوص جيد** مثل VS Code
4. **راجع الكود** واقرأه بتمعن، لا تنسخ فقط!

---

## 📝 التمرين العملي

الآن دورك! نفذ المهام التالية **بنفسك**:

### ✏️ **تمرين 1:** أضف بطاقة إحصائية جديدة
أضف بطاقة خامسة في Dashboard تعرض "عدد الموردين" باللون الأزرق الفاتح

### ✏️ **تمرين 2:** أنشئ صفحة "عن النظام"
1. أنشئ Controller اسمه `AboutController`
2. أنشئ View اسمها `about.blade.php`
3. اعرض فيها:
   - اسم النظام
   - الهدف من النظام
   - المطور (اسمك!)
4. أضف الرابط في القائمة

### ✏️ **تمرين 3:** غيّر الألوان
غيّر ألوان البطاقات الأربعة في Dashboard لألوان من اختيارك

---

## 🔗 المصادر الإضافية

- 📚 [توثيق Laravel الرسمي](https://laravel.com/docs)
- 🎥 قنوات Laravel بالعربي على YouTube
- 💬 مجتمع Laravel بالعربي

---

## ✅ Checklist - تأكد من إتمام كل شيء

قبل الانتقال للدرس القادم، تأكد من:

- [ ] المشروع يعمل بدون أخطاء
- [ ] تستطيع تسجيل الدخول والخروج
- [ ] Dashboard تظهر بشكل صحيح
- [ ] القوائم بالعربي
- [ ] فهمت كل سطر من الكود (لا تستعجل!)
- [ ] أنجزت التمارين الثلاثة

---

## 🎯 الدرس القادم

في الدرس القادم سنبني:
- **نظام الشركات والفروع المتعددة (Multi-Tenant)**
- جدول Companies
- جدول Branches
- إمكانية التبديل بين الشركات
- Middleware للتحكم بالشركة النشطة

---

**تذكر:** لا تنتقل للدرس القادم حتى تفهم هذا الدرس بالكامل! 💪

**انتهى الدرس الأول** ✅