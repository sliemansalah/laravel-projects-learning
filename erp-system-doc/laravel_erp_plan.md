# 🏢 خطة Laravel ERP System: من الصفر للاحتراف

## 📊 نظرة عامة على المشروع

**المشروع:** نظام ERP متكامل (Enterprise Resource Planning)
**المدة:** 14-60 يومًا
**الساعات اليومية:** 3-6 ساعات
**الوحدات الرئيسية:**
- 💰 المحاسبة المالية
- 📦 إدارة المخزون
- 🛒 المشتريات والموردين
- 💼 المبيعات والعملاء
- 👥 الموارد البشرية
- 📊 التقارير والتحليلات
- ⚙️ الإعدادات والصلاحيات

---

## 🗓️ المرحلة الأولى: البنية التحتية + وحدة الإعدادات (الأسبوع 1)

### **اليوم 1: إعداد المشروع**
#### الدرس 1: تثبيت وإعداد بيئة ERP
**🎯 الهدف:** إنشاء بيئة Laravel جاهزة لنظام ERP ضخم

**📚 المفاهيم:**
- هيكلة مشاريع Enterprise
- معايير تسمية المجلدات للأنظمة الكبيرة
- Multi-database Architecture

**💻 التطبيق العملي:**
```bash
# إنشاء المشروع
composer create-project laravel/laravel erp-system
cd erp-system

# تثبيت الحزم الأساسية
composer require laravel/ui
composer require spatie/laravel-permission
composer require yajra/laravel-datatables-oracle
php artisan ui bootstrap --auth
npm install && npm run build
```

**🔧 هيكل قاعدة البيانات:**
```
erp_system/
├── users (المستخدمين)
├── companies (الشركات - Multi-tenant)
├── branches (الفروع)
├── fiscal_years (السنوات المالية)
└── system_settings (إعدادات النظام)
```

**✅ النتيجة:** مشروع Laravel جاهز مع نظام مستخدمين

**📝 التمرين:** أضف صفحة dashboard أساسية مع قائمة جانبية

---

### **اليوم 2: Multi-Tenant Architecture**
#### الدرس 2: نظام الشركات والفروع المتعددة
**🎯 الهدف:** بناء نظام يدعم عدة شركات وفروع

**💻 الجداول:**
```php
// Migration: companies
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('name_en')->nullable();
    $table->string('tax_number')->unique();
    $table->string('commercial_register');
    $table->string('logo')->nullable();
    $table->text('address');
    $table->string('phone');
    $table->string('email');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Migration: branches
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('code')->unique();
    $table->text('address');
    $table->string('phone');
    $table->string('manager_name')->nullable();
    $table->boolean('is_main')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**🔧 Middleware للتحكم بالشركة:**
```php
// app/Http/Middleware/SetCompanyContext.php
public function handle($request, Closure $next)
{
    $companyId = session('company_id', 1);
    config(['app.company_id' => $companyId]);
    return $next($request);
}
```

**✅ النتيجة:** نظام شركات وفروع متعدد

---

### **اليوم 3: السنوات والفترات المالية**
#### الدرس 3: إدارة السنوات والفترات المحاسبية
**🎯 الهدف:** بناء نظام السنوات المالية والفترات

**💻 الجداول:**
```php
// fiscal_years
Schema::create('fiscal_years', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('name'); // 2024/2025
    $table->date('start_date');
    $table->date('end_date');
    $table->boolean('is_closed')->default(false);
    $table->boolean('is_current')->default(false);
    $table->timestamps();
});

// fiscal_periods (شهور)
Schema::create('fiscal_periods', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fiscal_year_id')->constrained();
    $table->string('name'); // يناير 2024
    $table->integer('period_number'); // 1-12
    $table->date('start_date');
    $table->date('end_date');
    $table->boolean('is_closed')->default(false);
    $table->timestamps();
});
```

**✅ النتيجة:** نظام سنوات مالية مع فترات شهرية

---

### **اليوم 4: شجرة الحسابات - الأساس**
#### الدرس 4: بناء دليل الحسابات (Chart of Accounts)
**🎯 الهدف:** إنشاء شجرة الحسابات الرئيسية

**💻 الجدول:**
```php
Schema::create('accounts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('parent_id')->nullable()->constrained('accounts');
    $table->string('code')->unique(); // 1-1-1-001
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
    $table->enum('nature', ['debit', 'credit']); // طبيعة الحساب
    $table->integer('level'); // مستوى الحساب في الشجرة
    $table->boolean('is_parent')->default(false);
    $table->boolean('is_active')->default(true);
    $table->boolean('can_post')->default(true); // يقبل قيود
    $table->decimal('opening_balance', 15, 2)->default(0);
    $table->enum('currency', ['SAR', 'USD', 'EUR'])->default('SAR');
    $table->timestamps();
});
```

**🔧 Structure الحسابات:**
```
1- الأصول (Assets)
  1-1- الأصول المتداولة
    1-1-1- النقدية والبنوك
      1-1-1-001 الخزينة الرئيسية
      1-1-1-002 بنك الراجحي
    1-1-2- العملاء
    1-1-3- المخزون
  1-2- الأصول الثابتة
2- الخصوم (Liabilities)
  2-1- الخصوم المتداولة
  2-2- الخصوم طويلة الأجل
3- حقوق الملكية (Equity)
4- الإيرادات (Revenue)
5- المصروفات (Expenses)
```

**✅ النتيجة:** دليل حسابات شجري كامل

---

### **اليوم 5: مراكز التكلفة**
#### الدرس 5: نظام مراكز التكلفة
**🎯 الهدف:** إضافة مراكز التكلفة للتحليل المالي

**💻 الجدول:**
```php
Schema::create('cost_centers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('parent_id')->nullable()->constrained('cost_centers');
    $table->string('code')->unique();
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->boolean('is_parent')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**مثال:**
```
CC-001: إدارة المبيعات
CC-002: إدارة المشتريات
CC-003: الإدارة المالية
CC-004: فرع الرياض
CC-005: فرع جدة
```

**✅ النتيجة:** نظام مراكز تكلفة

---

### **اليوم 6: العملات والضرائب**
#### الدرس 6: إدارة العملات والضرائب
**🎯 الهدف:** نظام تعدد العملات والضرائب

**💻 الجداول:**
```php
// currencies
Schema::create('currencies', function (Blueprint $table) {
    $table->id();
    $table->string('code', 3); // SAR, USD
    $table->string('name');
    $table->string('symbol');
    $table->boolean('is_base')->default(false);
    $table->decimal('exchange_rate', 10, 4)->default(1);
    $table->timestamps();
});

// taxes
Schema::create('taxes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('name'); // ضريبة القيمة المضافة
    $table->string('code'); // VAT
    $table->decimal('rate', 5, 2); // 15.00
    $table->foreignId('account_id')->constrained(); // حساب الضريبة
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**✅ النتيجة:** نظام عملات وضرائب

---

### **اليوم 7: الصلاحيات والأدوار**
#### الدرس 7: نظام الأدوار والصلاحيات الشامل
**🎯 الهدف:** بناء نظام صلاحيات متقدم باستخدام Spatie

**💻 التطبيق:**
```php
// Roles
$admin = Role::create(['name' => 'admin']);
$accountant = Role::create(['name' => 'accountant']);
$salesManager = Role::create(['name' => 'sales_manager']);
$purchaseManager = Role::create(['name' => 'purchase_manager']);

// Permissions
Permission::create(['name' => 'view_accounts']);
Permission::create(['name' => 'create_journal_entry']);
Permission::create(['name' => 'approve_invoice']);
Permission::create(['name' => 'view_reports']);
Permission::create(['name' => 'manage_users']);
```

**🔧 الصلاحيات حسب الوحدات:**
```
المحاسبة:
- view_accounts, edit_accounts
- create_journal_entry, edit_journal_entry
- approve_journal_entry, post_journal_entry

المبيعات:
- view_invoices, create_invoice
- approve_invoice, cancel_invoice

المشتريات:
- view_purchases, create_purchase
- approve_purchase

التقارير:
- view_financial_reports
- view_inventory_reports
```

**✅ النتيجة:** نظام صلاحيات شامل

---

## 🗓️ المرحلة الثانية: المحاسبة المالية (الأسبوع 2)

### **اليوم 8: القيود اليومية - الأساس**
#### الدرس 8: نظام القيود اليومية (Journal Entries)
**🎯 الهدف:** بناء محرك القيود المحاسبية

**💻 الجداول:**
```php
// journal_entries
Schema::create('journal_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('branch_id')->constrained();
    $table->foreignId('fiscal_year_id')->constrained();
    $table->foreignId('fiscal_period_id')->constrained();
    $table->string('entry_number')->unique(); // JE-2024-0001
    $table->date('entry_date');
    $table->string('reference_type')->nullable(); // invoice, payment, etc
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->text('description');
    $table->decimal('total_debit', 15, 2);
    $table->decimal('total_credit', 15, 2);
    $table->enum('status', ['draft', 'posted', 'approved'])->default('draft');
    $table->foreignId('created_by')->constrained('users');
    $table->foreignId('posted_by')->nullable()->constrained('users');
    $table->timestamp('posted_at')->nullable();
    $table->timestamps();
});

// journal_entry_lines
Schema::create('journal_entry_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
    $table->foreignId('account_id')->constrained();
    $table->foreignId('cost_center_id')->nullable()->constrained();
    $table->decimal('debit', 15, 2)->default(0);
    $table->decimal('credit', 15, 2)->default(0);
    $table->text('description')->nullable();
    $table->timestamps();
});
```

**🔧 JournalEntry Model:**
```php
class JournalEntry extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($entry) {
            // Auto-generate entry number
            $entry->entry_number = self::generateEntryNumber();
        });
        
        static::saving(function ($entry) {
            // Validate debit = credit
            if ($entry->total_debit != $entry->total_credit) {
                throw new \Exception('المدين يجب أن يساوي الدائن');
            }
        });
    }
    
    public function post()
    {
        DB::transaction(function () {
            $this->status = 'posted';
            $this->posted_by = auth()->id();
            $this->posted_at = now();
            $this->save();
            
            // Update account balances
            $this->updateAccountBalances();
        });
    }
}
```

**✅ النتيجة:** محرك قيود محاسبية

---

### **اليوم 9: أرصدة الحسابات**
#### الدرس 9: حساب الأرصدة وميزان المراجعة
**🎯 الهدف:** نظام حساب الأرصدة الآلي

**💻 الجدول:**
```php
Schema::create('account_balances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('account_id')->constrained();
    $table->foreignId('fiscal_period_id')->constrained();
    $table->decimal('opening_balance', 15, 2)->default(0);
    $table->decimal('debit', 15, 2)->default(0);
    $table->decimal('credit', 15, 2)->default(0);
    $table->decimal('closing_balance', 15, 2)->default(0);
    $table->timestamps();
    
    $table->unique(['account_id', 'fiscal_period_id']);
});
```

**🔧 Service لحساب الأرصدة:**
```php
class AccountBalanceService
{
    public function calculateBalance($accountId, $fiscalPeriodId)
    {
        $opening = $this->getOpeningBalance($accountId, $fiscalPeriodId);
        $movements = $this->getPeriodMovements($accountId, $fiscalPeriodId);
        
        return [
            'opening_balance' => $opening,
            'debit' => $movements->sum('debit'),
            'credit' => $movements->sum('credit'),
            'closing_balance' => $this->calculateClosing($opening, $movements)
        ];
    }
}
```

**✅ النتيجة:** نظام أرصدة تلقائي

---

### **اليوم 10: سندات القبض والصرف**
#### الدرس 10: نظام سندات القبض والصرف
**🎯 الهدف:** إدارة المقبوضات والمدفوعات النقدية

**💻 الجداول:**
```php
// receipts (سندات القبض)
Schema::create('receipts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('receipt_number')->unique();
    $table->date('receipt_date');
    $table->enum('type', ['cash', 'bank', 'check']);
    $table->foreignId('customer_id')->nullable()->constrained();
    $table->decimal('amount', 15, 2);
    $table->foreignId('payment_method_id')->constrained();
    $table->string('check_number')->nullable();
    $table->date('check_date')->nullable();
    $table->foreignId('bank_account_id')->nullable()->constrained('accounts');
    $table->text('notes')->nullable();
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->enum('status', ['draft', 'posted'])->default('draft');
    $table->timestamps();
});

// payments (سندات الصرف)
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('payment_number')->unique();
    $table->date('payment_date');
    $table->enum('type', ['cash', 'bank', 'check']);
    $table->foreignId('supplier_id')->nullable()->constrained();
    $table->decimal('amount', 15, 2);
    $table->foreignId('payment_method_id')->constrained();
    $table->string('check_number')->nullable();
    $table->foreignId('bank_account_id')->nullable()->constrained('accounts');
    $table->text('notes')->nullable();
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->enum('status', ['draft', 'posted'])->default('draft');
    $table->timestamps();
});
```

**🔧 Auto-posting Journal Entry:**
```php
public function postReceipt(Receipt $receipt)
{
    $entry = JournalEntry::create([...]);
    
    // Debit: Bank/Cash
    $entry->lines()->create([
        'account_id' => $receipt->bank_account_id,
        'debit' => $receipt->amount,
        'description' => "سند قبض رقم {$receipt->receipt_number}"
    ]);
    
    // Credit: Customer Account
    $entry->lines()->create([
        'account_id' => $receipt->customer->account_id,
        'credit' => $receipt->amount,
    ]);
    
    $entry->post();
    $receipt->update(['journal_entry_id' => $entry->id]);
}
```

**✅ النتيجة:** نظام سندات قبض وصرف

---

### **اليوم 11-12: العملاء والموردين**
#### الدرس 11-12: إدارة العملاء والموردين
**🎯 الهدف:** نظام CRM/SRM مدمج مع المحاسبة

**💻 الجداول:**
```php
// customers
Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('code')->unique(); // CUST-0001
    $table->string('name');
    $table->string('name_en')->nullable();
    $table->string('tax_number')->nullable();
    $table->string('commercial_register')->nullable();
    $table->enum('type', ['individual', 'company']);
    $table->string('phone');
    $table->string('mobile')->nullable();
    $table->string('email')->nullable();
    $table->text('address');
    $table->string('city');
    $table->string('country')->default('SA');
    $table->foreignId('account_id')->constrained(); // حساب العميل
    $table->decimal('credit_limit', 15, 2)->default(0);
    $table->integer('payment_terms_days')->default(30);
    $table->foreignId('sales_person_id')->nullable()->constrained('users');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// suppliers
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('code')->unique(); // SUPP-0001
    $table->string('name');
    $table->string('tax_number')->nullable();
    $table->string('phone');
    $table->string('email')->nullable();
    $table->text('address');
    $table->foreignId('account_id')->constrained();
    $table->decimal('credit_limit', 15, 2)->default(0);
    $table->integer('payment_terms_days')->default(30);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// customer_transactions (كشف حساب العميل)
Schema::create('customer_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained();
    $table->date('transaction_date');
    $table->string('transaction_type'); // invoice, payment, credit_note
    $table->string('reference_number');
    $table->decimal('debit', 15, 2)->default(0);
    $table->decimal('credit', 15, 2)->default(0);
    $table->decimal('balance', 15, 2);
    $table->text('description');
    $table->timestamps();
});
```

**✅ النتيجة:** نظام عملاء وموردين متكامل

---

### **اليوم 13-14: التقارير المالية الأساسية**
#### الدرس 13-14: التقارير المحاسبية الرئيسية
**🎯 الهدف:** بناء التقارير المالية الأساسية

**📊 التقارير:**
1. **ميزان المراجعة (Trial Balance)**
2. **دفتر اليومية (Journal Book)**
3. **دفتر الأستاذ (Ledger)**
4. **كشف حساب عميل/مورد**

**🔧 Report Controller:**
```php
class FinancialReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $accounts = Account::where('can_post', true)
            ->with(['balances' => function($q) use ($request) {
                $q->where('fiscal_period_id', $request->period_id);
            }])
            ->get()
            ->map(function($account) {
                $balance = $account->balances->first();
                return [
                    'code' => $account->code,
                    'name' => $account->name_ar,
                    'debit' => $balance->debit ?? 0,
                    'credit' => $balance->credit ?? 0,
                ];
            });
            
        return view('reports.trial-balance', compact('accounts'));
    }
    
    public function ledger($accountId, Request $request)
    {
        $transactions = JournalEntryLine::where('account_id', $accountId)
            ->whereBetween('created_at', [$request->from, $request->to])
            ->with('journalEntry')
            ->get();
            
        return view('reports.ledger', compact('transactions'));
    }
}
```

**✅ النتيجة:** تقارير مالية أساسية

---

## 🗓️ المرحلة الثالثة: المبيعات والمشتريات (الأسبوع 3)

### **اليوم 15-16: نظام المنتجات والخدمات**
#### الدرس 15-16: إدارة المنتجات والمخزون
**🎯 الهدف:** بناء كتالوج المنتجات

**💻 الجداول:**
```php
// product_categories
Schema::create('product_categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('parent_id')->nullable()->constrained('product_categories');
    $table->string('name');
    $table->foreignId('inventory_account_id')->constrained('accounts');
    $table->foreignId('sales_account_id')->constrained('accounts');
    $table->foreignId('cogs_account_id')->constrained('accounts'); // Cost of Goods Sold
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// products
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('category_id')->constrained('product_categories');
    $table->string('code')->unique(); // PROD-0001
    $table->string('barcode')->unique()->nullable();
    $table->string('name');
    $table->string('name_en')->nullable();
    $table->text('description')->nullable();
    $table->enum('type', ['product', 'service', 'raw_material']);
    $table->foreignId('unit_id')->constrained('units');
    $table->decimal('cost_price', 15, 2)->default(0);
    $table->decimal('selling_price', 15, 2);
    $table->decimal('min_price', 15, 2)->nullable();
    $table->decimal('wholesale_price', 15, 2)->nullable();
    $table->integer('reorder_level')->default(0);
    $table->boolean('track_inventory')->default(true);
    $table->boolean('is_active')->default(true);
    $table->string('image')->nullable();
    $table->timestamps();
});

// units (وحدات القياس)
Schema::create('units', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // قطعة، كرتونة، كيلو
    $table->string('symbol'); // PC, CTN, KG
    $table->timestamps();
});

// product_warehouse (المخزون حسب المستودع)
Schema::create('product_warehouse', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained();
    $table->foreignId('warehouse_id')->constrained();
    $table->decimal('quantity', 15, 3)->default(0);
    $table->decimal('reserved_quantity', 15, 3)->default(0);
    $table->decimal('available_quantity', 15, 3)->default(0);
    $table->timestamps();
});
```

**✅ النتيجة:** نظام منتجات شامل

---

### **اليوم 17-18: فواتير المبيعات**
#### الدرس 17-18: نظام فواتير المبيعات
**🎯 الهدف:** فواتير مبيعات مع القيود التلقائية

**💻 الجداول:**
```php
// sales_invoices
Schema::create('sales_invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('branch_id')->constrained();
    $table->string('invoice_number')->unique(); // INV-2024-0001
    $table->date('invoice_date');
    $table->date('due_date');
    $table->foreignId('customer_id')->constrained();
    $table->foreignId('sales_person_id')->nullable()->constrained('users');
    $table->decimal('subtotal', 15, 2);
    $table->decimal('discount_percentage', 5, 2)->default(0);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('tax_amount', 15, 2)->default(0);
    $table->decimal('total_amount', 15, 2);
    $table->decimal('paid_amount', 15, 2)->default(0);
    $table->decimal('remaining_amount', 15, 2);
    $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
    $table->text('notes')->nullable();
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');
    $table->timestamps();
});

// sales_invoice_items
Schema::create('sales_invoice_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_invoice_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained();
    $table->foreignId('warehouse_id')->constrained();
    $table->decimal('quantity', 15, 3);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('discount_percentage', 5, 2)->default(0);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('tax_percentage', 5, 2)->default(0);
    $table->decimal('tax_amount', 15, 2)->default(0);
    $table->decimal('total', 15, 2);
    $table->timestamps();
});
```

**🔧 Auto-posting Journal Entry للفاتورة:**
```php
public function confirmInvoice(SalesInvoice $invoice)
{
    DB::transaction(function () use ($invoice) {
        // إنشاء القيد المحاسبي
        $entry = JournalEntry::create([
            'company_id' => $invoice->company_id,
            'entry_date' => $invoice->invoice_date,
            'reference_type' => 'sales_invoice',
            'reference_id' => $invoice->id,
            'description' => "فاتورة مبيعات رقم {$invoice->invoice_number}",
        ]);
        
        // من ح/ العميل (مدين)
        $entry->lines()->create([
            'account_id' => $invoice->customer->account_id,
            'debit' => $invoice->total_amount,
        ]);
        
        // إلى ح/ المبيعات (دائن)
        foreach ($invoice->items as $item) {
            $entry->lines()->create([
                'account_id' => $item->product->category->sales_account_id,
                'credit' => $item->total - $item->tax_amount,
            ]);
        }
        
        // إلى ح/ ضريبة المبيعات (دائن)
        if ($invoice->tax_amount > 0) {
            $entry->lines()->create([
                'account_id' => Tax::first()->account_id,
                'credit' => $invoice->tax_amount,
            ]);
        }
        
        $entry->post();
        
        // قيد تكلفة البضاعة المباعة (COGS)
        $this->postCOGS($invoice, $entry);
        
        // تحديث المخزون
        $this->updateInventory($invoice);
        
        $invoice->update([
            'status' => 'confirmed',
            'journal_entry_id' => $entry->id
        ]);
    });
}

private function postCOGS($invoice, $parentEntry)
{
    $cogsEntry = JournalEntry::create([...]);
    
    foreach ($invoice->items as $item) {
        // من ح/ تكلفة البضاعة المباعة (مدين)
        $cogsEntry->lines()->create([
            'account_id' => $item->product->category->cogs_account_id,
            'debit' => $item->quantity * $item->product->cost_price,
        ]);
        
        // إلى ح/ المخزون (دائن)
        $cogsEntry->lines()->create([
            'account_id' => $item->product->category->inventory_account_id,
            'credit' => $item->quantity * $item->product->cost_price,
        ]);
    }
    
    $cogsEntry->post();
}
```

**✅ النتيجة:** نظام فواتير مبيعات متكامل

---

### **اليوم 19-20: فواتير المشتريات**
#### الدرس 19-20: نظام فواتير المشتريات
**🎯 الهدف:** إدارة المشتريات مع القيود التلقائية

**💻 الجداول:**
```php
// purchase_invoices
Schema::create('purchase_invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('branch_id')->constrained();
    $table->string('invoice_number')->unique(); // PUR-2024-0001
    $table->string('supplier_invoice_number')->nullable();
    $table->date('invoice_date');
    $table->date('due_date');
    $table->foreignId('supplier_id')->constrained();
    $table->foreignId('warehouse_id')->constrained();
    $table->decimal('subtotal', 15, 2);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('tax_amount', 15, 2)->default(0);
    $table->decimal('total_amount', 15, 2);
    $table->decimal('paid_amount', 15, 2)->default(0);
    $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');
    $table->timestamps();
});

// purchase_invoice_items
Schema::create('purchase_invoice_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('purchase_invoice_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained();
    $table->decimal('quantity', 15, 3);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('tax_amount', 15, 2)->default(0);
    $table->decimal('total', 15, 2);
    $table->timestamps();
});
```

**🔧 Auto-posting للمشتريات:**
```php
public function confirmPurchase(PurchaseInvoice $invoice)
{
    DB::transaction(function () use ($invoice) {
        $entry = JournalEntry::create([...]);
        
        // من ح/ المشتريات (مدين)
        foreach ($invoice->items as $item) {
            $entry->lines()->create([
                'account_id' => $item->product->category->inventory_account_id,
                'debit' => $item->total - $item->tax_amount,
            ]);
        }
        
        // من ح/ ضريبة المشتريات (مدين)
        if ($invoice->tax_amount > 0) {
            $entry->lines()->create([
                'account_id' => Tax::first()->account_id,
                'debit' => $invoice->tax_amount,
            ]);
        }
        
        // إلى ح/ المورد (دائن)
        $entry->lines()->create([
            'account_id' => $invoice->supplier->account_id,
            'credit' => $invoice->total_amount,
        ]);
        
        $entry->post();
        
        // تحديث المخزون
        $this->updateInventory($invoice);
        
        // تحديث تكلفة المنتج
        $this->updateProductCost($invoice);
    });
}
```

**✅ النتيجة:** نظام مشتريات متكامل

---

### **اليوم 21: عروض الأسعار وأوامر البيع**
#### الدرس 21: Sales Quotations & Orders
**🎯 الهدف:** إدارة عروض الأسعار وأوامر البيع

**💻 الجداول:**
```php
// quotations
Schema::create('quotations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('quotation_number')->unique(); // QUO-2024-0001
    $table->date('quotation_date');
    $table->date('valid_until');
    $table->foreignId('customer_id')->constrained();
    $table->decimal('total_amount', 15, 2);
    $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
    $table->foreignId('converted_invoice_id')->nullable()->constrained('sales_invoices');
    $table->timestamps();
});

// sales_orders
Schema::create('sales_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('order_number')->unique(); // SO-2024-0001
    $table->date('order_date');
    $table->date('delivery_date')->nullable();
    $table->foreignId('customer_id')->constrained();
    $table->decimal('total_amount', 15, 2);
    $table->enum('status', ['pending', 'confirmed', 'invoiced', 'cancelled'])->default('pending');
    $table->foreignId('converted_invoice_id')->nullable()->constrained('sales_invoices');
    $table->timestamps();
});
```

**🔧 تحويل عرض السعر لفاتورة:**
```php
public function convertToInvoice(Quotation $quotation)
{
    $invoice = SalesInvoice::create([
        'customer_id' => $quotation->customer_id,
        'invoice_date' => now(),
        'total_amount' => $quotation->total_amount,
        // ... باقي الحقول
    ]);
    
    foreach ($quotation->items as $item) {
        $invoice->items()->create([
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ]);
    }
    
    $quotation->update([
        'status' => 'accepted',
        'converted_invoice_id' => $invoice->id
    ]);
    
    return $invoice;
}
```

**✅ النتيجة:** نظام عروض أسعار وأوامر بيع

---

## 🗓️ المرحلة الرابعة: المخزون والإنتاج (الأسبوع 4)

### **اليوم 22-23: إدارة المستودعات**
#### الدرس 22-23: نظام المستودعات وحركة المخزون
**🎯 الهدف:** نظام مستودعات متعدد مع تتبع الحركات

**💻 الجداول:**
```php
// warehouses
Schema::create('warehouses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('branch_id')->constrained();
    $table->string('code')->unique(); // WH-001
    $table->string('name');
    $table->text('address');
    $table->foreignId('manager_id')->nullable()->constrained('users');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// inventory_transactions (حركات المخزون)
Schema::create('inventory_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('warehouse_id')->constrained();
    $table->foreignId('product_id')->constrained();
    $table->string('transaction_number')->unique();
    $table->date('transaction_date');
    $table->enum('type', [
        'purchase', 'sales', 'transfer', 
        'adjustment', 'return', 'production'
    ]);
    $table->enum('movement', ['in', 'out']);
    $table->decimal('quantity', 15, 3);
    $table->decimal('unit_cost', 15, 2);
    $table->decimal('total_cost', 15, 2);
    $table->string('reference_type')->nullable();
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});

// inventory_adjustments (جرد وتسوية المخزون)
Schema::create('inventory_adjustments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('warehouse_id')->constrained();
    $table->string('adjustment_number')->unique();
    $table->date('adjustment_date');
    $table->enum('type', ['increase', 'decrease', 'physical_count']);
    $table->text('reason');
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->enum('status', ['draft', 'confirmed'])->default('draft');
    $table->timestamps();
});

// inventory_adjustment_items
Schema::create('inventory_adjustment_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inventory_adjustment_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained();
    $table->decimal('system_quantity', 15, 3); // الكمية بالنظام
    $table->decimal('actual_quantity', 15, 3); // الكمية الفعلية
    $table->decimal('difference', 15, 3); // الفرق
    $table->decimal('unit_cost', 15, 2);
    $table->decimal('total_cost', 15, 2);
    $table->timestamps();
});
```

**🔧 Inventory Service:**
```php
class InventoryService
{
    public function recordTransaction($data)
    {
        DB::transaction(function () use ($data) {
            $transaction = InventoryTransaction::create($data);
            
            // تحديث الكمية بالمستودع
            $this->updateWarehouseQuantity(
                $transaction->warehouse_id,
                $transaction->product_id,
                $transaction->quantity,
                $transaction->movement
            );
            
            // حساب المتوسط المرجح للتكلفة
            if ($transaction->movement == 'in') {
                $this->updateWeightedAverageCost($transaction);
            }
        });
    }
    
    public function transferBetweenWarehouses($fromWarehouse, $toWarehouse, $product, $quantity)
    {
        DB::transaction(function () use ($fromWarehouse, $toWarehouse, $product, $quantity) {
            // حركة خروج من المستودع الأول
            $this->recordTransaction([
                'warehouse_id' => $fromWarehouse,
                'product_id' => $product,
                'type' => 'transfer',
                'movement' => 'out',
                'quantity' => $quantity,
            ]);
            
            // حركة دخول للمستودع الثاني
            $this->recordTransaction([
                'warehouse_id' => $toWarehouse,
                'product_id' => $product,
                'type' => 'transfer',
                'movement' => 'in',
                'quantity' => $quantity,
            ]);
        });
    }
}
```

**✅ النتيجة:** نظام مخزون متقدم مع تتبع الحركات

---

### **اليوم 24: تقييم المخزون**
#### الدرس 24: طرق تقييم المخزون
**🎯 الهدف:** تطبيق طرق تقييم المخزون المختلفة

**💻 تطبيق الطرق:**
```php
class InventoryValuationService
{
    // 1. المتوسط المرجح (Weighted Average)
    public function weightedAverage($productId, $warehouseId)
    {
        $totalCost = InventoryTransaction::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('movement', 'in')
            ->sum(DB::raw('quantity * unit_cost'));
            
        $totalQuantity = ProductWarehouse::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->value('quantity');
            
        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }
    
    // 2. FIFO (First In First Out)
    public function getFIFOCost($productId, $quantity)
    {
        $batches = InventoryTransaction::where('product_id', $productId)
            ->where('movement', 'in')
            ->where('remaining_quantity', '>', 0)
            ->orderBy('transaction_date')
            ->get();
            
        $totalCost = 0;
        $remainingQty = $quantity;
        
        foreach ($batches as $batch) {
            $qtyToUse = min($batch->remaining_quantity, $remainingQty);
            $totalCost += $qtyToUse * $batch->unit_cost;
            $remainingQty -= $qtyToUse;
            
            if ($remainingQty <= 0) break;
        }
        
        return $totalCost;
    }
    
    // 3. LIFO (Last In First Out) - ممنوع في المعايير الدولية لكن للعلم
    public function getLIFOCost($productId, $quantity)
    {
        // مشابه لـ FIFO لكن orderByDesc
    }
}
```

**✅ النتيجة:** نظام تقييم مخزون دقيق

---

### **اليوم 25-26: الإنتاج والتصنيع**
#### الدرس 25-26: نظام أوامر الإنتاج (Bill of Materials)
**🎯 الهدف:** نظام تصنيع وتجميع المنتجات

**💻 الجداول:**
```php
// bill_of_materials (BOM - مكونات المنتج)
Schema::create('bill_of_materials', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('product_id')->constrained(); // المنتج النهائي
    $table->string('bom_number')->unique();
    $table->string('version')->default('1.0');
    $table->decimal('quantity', 15, 3)->default(1); // كمية الإنتاج
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// bom_items (المواد الخام المطلوبة)
Schema::create('bom_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bom_id')->constrained('bill_of_materials')->cascadeOnDelete();
    $table->foreignId('material_id')->constrained('products'); // المادة الخام
    $table->decimal('quantity_required', 15, 3); // الكمية المطلوبة
    $table->decimal('waste_percentage', 5, 2)->default(0); // نسبة الهدر
    $table->timestamps();
});

// production_orders (أوامر الإنتاج)
Schema::create('production_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('warehouse_id')->constrained();
    $table->string('order_number')->unique(); // PRO-2024-0001
    $table->date('order_date');
    $table->date('planned_start_date');
    $table->date('planned_completion_date');
    $table->date('actual_start_date')->nullable();
    $table->date('actual_completion_date')->nullable();
    $table->foreignId('bom_id')->constrained('bill_of_materials');
    $table->foreignId('product_id')->constrained(); // المنتج النهائي
    $table->decimal('quantity_to_produce', 15, 3);
    $table->decimal('quantity_produced', 15, 3)->default(0);
    $table->enum('status', ['draft', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('draft');
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->timestamps();
});
```

**🔧 Production Service:**
```php
class ProductionService
{
    public function startProduction(ProductionOrder $order)
    {
        DB::transaction(function () use ($order) {
            // سحب المواد الخام من المخزون
            foreach ($order->bom->items as $item) {
                $requiredQty = $item->quantity_required * $order->quantity_to_produce;
                
                InventoryTransaction::create([
                    'product_id' => $item->material_id,
                    'warehouse_id' => $order->warehouse_id,
                    'type' => 'production',
                    'movement' => 'out',
                    'quantity' => $requiredQty,
                    'reference_type' => 'production_order',
                    'reference_id' => $order->id,
                ]);
            }
            
            $order->update([
                'status' => 'in_progress',
                'actual_start_date' => now()
            ]);
        });
    }
    
    public function completeProduction(ProductionOrder $order)
    {
        DB::transaction(function () use ($order) {
            // إضافة المنتج النهائي للمخزون
            InventoryTransaction::create([
                'product_id' => $order->product_id,
                'warehouse_id' => $order->warehouse_id,
                'type' => 'production',
                'movement' => 'in',
                'quantity' => $order->quantity_to_produce,
                'unit_cost' => $this->calculateProductionCost($order),
            ]);
            
            // إنشاء القيد المحاسبي
            $this->postProductionEntry($order);
            
            $order->update([
                'status' => 'completed',
                'actual_completion_date' => now(),
                'quantity_produced' => $order->quantity_to_produce
            ]);
        });
    }
    
    private function calculateProductionCost($order)
    {
        $materialCost = 0;
        foreach ($order->bom->items as $item) {
            $materialCost += $item->material->cost_price * 
                           ($item->quantity_required * $order->quantity_to_produce);
        }
        
        // يمكن إضافة تكاليف إضافية (عمالة، تشغيل، إلخ)
        return $materialCost / $order->quantity_to_produce;
    }
}
```

**✅ النتيجة:** نظام إنتاج وتصنيع

---

### **اليوم 27-28: التقارير المتقدمة**
#### الدرس 27-28: تقارير المخزون والإنتاج
**🎯 الهدف:** تقارير تحليلية شاملة

**📊 التقارير:**
1. **تقرير حركة المخزون**
2. **تقرير المخزون الحالي**
3. **تقرير الأصناف تحت حد الطلب**
4. **تقرير المنتجات الراكدة**
5. **تقرير تقييم المخزون**
6. **تقرير تكلفة الإنتاج**

**🔧 Report Examples:**
```php
class InventoryReportController extends Controller
{
    public function currentStock(Request $request)
    {
        $stock = ProductWarehouse::with('product', 'warehouse')
            ->when($request->warehouse_id, function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            })
            ->where('quantity', '>', 0)
            ->get()
            ->map(function($item) {
                return [
                    'product' => $item->product->name,
                    'warehouse' => $item->warehouse->name,
                    'quantity' => $item->quantity,
                    'cost' => $item->product->cost_price,
                    'value' => $item->quantity * $item->product->cost_price,
                ];
            });
            
        return view('reports.current-stock', compact('stock'));
    }
    
    public function slowMovingItems(Request $request)
    {
        // المنتجات التي لم تتحرك خلال آخر 6 شهور
        $products = Product::whereDoesntHave('inventoryTransactions', function($q) {
            $q->where('transaction_date', '>=', now()->subMonths(6));
        })
        ->where('track_inventory', true)
        ->with('warehouse')
        ->get();
        
        return view('reports.slow-moving', compact('products'));
    }
}
```

**✅ النتيجة:** تقارير مخزون وإنتاج شاملة

---

## 🗓️ المرحلة الخامسة: الموارد البشرية والرواتب (الأسبوع 5)

### **اليوم 29-30: إدارة الموظفين**
#### الدرس 29-30: نظام الموارد البشرية الأساسي
**🎯 الهدف:** بناء نظام HR متكامل

**💻 الجداول:**
```php
// departments
Schema::create('departments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('name');
    $table->foreignId('manager_id')->nullable()->constrained('employees');
    $table->foreignId('cost_center_id')->nullable()->constrained();
    $table->timestamps();
});

// employees
Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->string('employee_number')->unique();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('national_id')->unique();
    $table->date('birth_date');
    $table->enum('gender', ['male', 'female']);
    $table->string('phone');
    $table->string('email')->unique();
    $table->text('address');
    $table->date('hire_date');
    $table->date('termination_date')->nullable();
    $table->foreignId('department_id')->constrained();
    $table->foreignId('job_title_id')->constrained();
    $table->decimal('basic_salary', 15, 2);
    $table->enum('employment_type', ['full_time', 'part_time', 'contract']);
    $table->enum('status', ['active', 'on_leave', 'terminated'])->default('active');
    $table->string('bank_name')->nullable();
    $table->string('bank_account_number')->nullable();
    $table->string('iban')->nullable();
    $table->timestamps();
});

// job_titles
Schema::create('job_titles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('title');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

**✅ النتيجة:** نظام موارد بشرية أساسي

---

### **اليوم 31-32: الحضور والانصراف**
#### الدرس 31-32: نظام تتبع الحضور
**🎯 الهدف:** إدارة الحضور والغياب والإجازات

**💻 الجداول:**
```php
// attendance
Schema::create('attendance', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained();
    $table->date('attendance_date');
    $table->time('check_in')->nullable();
    $table->time('check_out')->nullable();
    $table->integer('work_hours')->nullable();
    $table->integer('overtime_hours')->default(0);
    $table->integer('late_minutes')->default(0);
    $table->enum('status', ['present', 'absent', 'on_leave', 'holiday'])->default('present');
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->unique(['employee_id', 'attendance_date']);
});

// leaves
Schema::create('leaves', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained();
    $table->foreignId('leave_type_id')->constrained();
    $table->date('start_date');
    $table->date('end_date');
    $table->integer('days_count');
    $table->text('reason');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->text('rejection_reason')->nullable();
    $table->timestamps();
});

// leave_types
Schema::create('leave_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('name'); // إجازة سنوية، مرضية، طارئة
    $table->integer('max_days_per_year');
    $table->boolean('is_paid')->default(true);
    $table->timestamps();
});
```

**✅ النتيجة:** نظام حضور وإجازات

---

### **اليوم 33-34: الرواتب والبدلات**
#### الدرس 33-34: نظام الرواتب والمستحقات
**🎯 الهدف:** حساب وصرف الرواتب

**💻 الجداول:**
```php
// allowances (البدلات)
Schema::create('allowances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('name'); // بدل سكن، انتقال، جوال
    $table->enum('type', ['fixed', 'percentage']);
    $table->decimal('amount', 15, 2);
    $table->boolean('is_taxable')->default(false);
    $table->timestamps();
});

// employee_allowances
Schema::create('employee_allowances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained();
    $table->foreignId('allowance_id')->constrained();
    $table->decimal('amount', 15, 2);
    $table->date('effective_date');
    $table->timestamps();
});

// deductions (الخصومات)
Schema::create('deductions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('name'); // تأمينات، غياب، تأخير
    $table->enum('type', ['fixed', 'percentage', 'per_day']);
    $table->decimal('amount', 15, 2);
    $table->timestamps();
});

// payroll (كشوف الرواتب)
Schema::create('payrolls', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained();
    $table->string('payroll_number')->unique(); // PAY-2024-01
    $table->integer('month');
    $table->integer('year');
    $table->date('payment_date');
    $table->decimal('total_basic_salary', 15, 2);
    $table->decimal('total_allowances', 15, 2);
    $table->decimal('total_overtime', 15, 2);
    $table->decimal('total_deductions', 15, 2);
    $table->decimal('total_net_salary', 15, 2);
    $table->enum('status', ['draft', 'confirmed', 'paid'])->default('draft');
    $table->foreignId('journal_entry_id')->nullable()->constrained();
    $table->timestamps();
});

// payroll_details
Schema::create('payroll_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
    $table->foreignId('employee_id')->constrained();
    $table->decimal('basic_salary', 15, 2);
    $table->decimal('allowances', 15, 2)->default(0);
    $table->decimal('overtime', 15, 2)->default(0);
    $table->decimal('deductions', 15, 2)->default(0);
    $table->decimal('net_salary', 15, 2);
    $table->integer('worked_days');
    $table->integer('absent_days')->default(0);
    $table->integer('overtime_hours')->default(0);
    $table->timestamps();
});
```

**🔧 Payroll Service:**
```php
class PayrollService
{
    public function generatePayroll($month, $year)
    {
        DB::transaction(function () use ($month, $year) {
            $payroll = Payroll::create([
                'month' => $month,
                'year' => $year,
                'payment_date' => now(),
            ]);
            
            $employees = Employee::where('status', 'active')->get();
            
            foreach ($employees as $employee) {
                $salary = $this->calculateEmployeeSalary($employee, $month, $year);
                
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $employee->id,
                    'basic_salary' => $salary['basic'],
                    'allowances' => $salary['allowances'],
                    'overtime' => $salary['overtime'],
                    'deductions' => $salary['deductions'],
                    'net_salary' => $salary['net'],
                    'worked_days' => $salary['worked_days'],
                    'absent_days' => $salary['absent_days'],
                    'overtime_hours' => $salary['overtime_hours'],
                ]);
            }
            
            $this->updatePayrollTotals($payroll);
        });
    }
    
    private function calculateEmployeeSalary($employee, $month, $year)
    {
        // حساب الراتب الأساسي
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();
            
        $workedDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        
        // الراتب الأساسي المستحق
        $basicSalary = ($employee->basic_salary / $daysInMonth) * $workedDays;
        
        // البدلات
        $allowances = $employee->allowances->sum('amount');
        
        // العمل الإضافي
        $overtimeHours = $attendance->sum('overtime_hours');
        $hourlyRate = $employee->basic_salary / ($daysInMonth * 8);
        $overtime = $overtimeHours * $hourlyRate * 1.5; // وقت إضافي بمعدل 1.5
        
        // الخصومات
        $deductions = 0;
        
        // خصم الغياب
        if ($absentDays > 0) {
            $deductions += ($employee->basic_salary / $daysInMonth) * $absentDays;
        }
        
        // التأمينات الاجتماعية (9% في السعودية)
        $socialInsurance = $employee->basic_salary * 0.09;
        $deductions += $socialInsurance;
        
        // صافي الراتب
        $netSalary = $basicSalary + $allowances + $overtime - $deductions;
        
        return [
            'basic' => $basicSalary,
            'allowances' => $allowances,
            'overtime' => $overtime,
            'deductions' => $deductions,
            'net' => $netSalary,
            'worked_days' => $workedDays,
            'absent_days' => $absentDays,
            'overtime_hours' => $overtimeHours,
        ];
    }
    
    public function confirmPayroll(Payroll $payroll)
    {
        DB::transaction(function () use ($payroll) {
            // إنشاء القيد المحاسبي
            $entry = JournalEntry::create([
                'company_id' => $payroll->company_id,
                'entry_date' => $payroll->payment_date,
                'reference_type' => 'payroll',
                'reference_id' => $payroll->id,
                'description' => "كشف رواتب {$payroll->month}/{$payroll->year}",
            ]);
            
            // من ح/ مصروف الرواتب (مدين)
            $entry->lines()->create([
                'account_id' => Account::where('code', '5-1-1-001')->first()->id, // مصروف الرواتب
                'debit' => $payroll->total_basic_salary,
            ]);
            
            // من ح/ مصروف البدلات (مدين)
            $entry->lines()->create([
                'account_id' => Account::where('code', '5-1-1-002')->first()->id,
                'debit' => $payroll->total_allowances,
            ]);
            
            // إلى ح/ التأمينات المستحقة (دائن)
            $socialInsurance = $payroll->details->sum(function($detail) {
                return $detail->basic_salary * 0.09;
            });
            $entry->lines()->create([
                'account_id' => Account::where('code', '2-1-3-001')->first()->id,
                'credit' => $socialInsurance,
            ]);
            
            // إلى ح/ الرواتب المستحقة (دائن)
            $entry->lines()->create([
                'account_id' => Account::where('code', '2-1-2-001')->first()->id,
                'credit' => $payroll->total_net_salary,
            ]);
            
            $entry->post();
            
            $payroll->update([
                'status' => 'confirmed',
                'journal_entry_id' => $entry->id
            ]);
        });
    }
}
```

**✅ النتيجة:** نظام رواتب متكامل مع القيود

---

### **اليوم 35: القروض والسلف**
#### الدرس 35: إدارة قروض وسلف الموظفين
**🎯 الهدف:** نظام إدارة القروض والسلف

**💻 الجداول:**
```php
// employee_loans
Schema::create('employee_loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained();
    $table->string('loan_number')->unique();
    $table->date('loan_date');
    $table->decimal('loan_amount', 15, 2);
    $table->integer('installments_count');
    $table->decimal('installment_amount', 15, 2);
    $table->decimal('remaining_amount', 15, 2);
    $table->date('start_date');
    $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
    $table->text('notes')->nullable();
    $table->timestamps();
});

// loan_installments
Schema::create('loan_installments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_loan_id')->constrained()->cascadeOnDelete();
    $table->integer('installment_number');
    $table->date('due_date');
    $table->decimal('amount', 15, 2);
    $table->boolean('is_paid')->default(false);
    $table->date('paid_date')->nullable();
    $table->foreignId('payroll_id')->nullable()->constrained();
    $table->timestamps();
});
```

**✅ النتيجة:** نظام قروض وسلف

---

## 🗓️ المرحلة السادسة: التقارير المتقدمة والتحليلات (الأسبوع 6)

### **اليوم 36-37: القوائم المالية**
#### الدرس 36-37: إنشاء القوائم المالية الرئيسية
**🎯 الهدف:** قائمة الدخل والميزانية العمومية

**💻 التطبيق:**
```php
class FinancialStatementController extends Controller
{
    // 1. قائمة الدخل (Income Statement)
    public function incomeStatement(Request $request)
    {
        $period = FiscalPeriod::find($request->period_id);
        
        // الإيرادات
        $revenues = Account::where('type', 'revenue')
            ->with(['balances' => function($q) use ($period) {
                $q->where('fiscal_period_id', $period->id);
            }])
            ->get()
            ->sum(function($account) {
                return $account->balances->first()->credit ?? 0;
            });
        
        // المصروفات
        $expenses = Account::where('type', 'expense')
            ->with(['balances' => function($q) use ($period) {
                $q->where('fiscal_period_id', $period->id);
            }])
            ->get()
            ->sum(function($account) {
                return $account->balances->first()->debit ?? 0;
            });
        
        // تكلفة البضاعة المباعة
        $cogs = Account::where('code', 'LIKE', '5-2%') // حسابات تكلفة المبيعات
            ->with(['balances' => function($q) use ($period) {
                $q->where('fiscal_period_id', $period->id);
            }])
            ->get()
            ->sum(function($account) {
                return $account->balances->first()->debit ?? 0;
            });
        
        $grossProfit = $revenues - $cogs;
        $operatingExpenses = $expenses;
        $netProfit = $grossProfit - $operatingExpenses;
        
        return view('reports.income-statement', compact(
            'revenues', 'cogs', 'grossProfit', 
            'operatingExpenses', 'netProfit', 'period'
        ));
    }
    
    // 2. الميزانية العمومية (Balance Sheet)
    public function balanceSheet(Request $request)
    {
        $period = FiscalPeriod::find($request->period_id);
        
        // الأصول
        $assets = Account::where('type', 'asset')
            ->with(['balances' => function($q) use ($period) {
                $q->where('fiscal_period_id', $period->id);
            }])
            ->get()
            ->groupBy('parent_id')
            ->map(function($group) {
                return $group->sum(function($account) {
                    $balance = $account->balances->first();
                    return $balance ? $balance->closing_balance : 0;
                });
            });
        
        // الخصوم
        $liabilities = Account::where('type', 'liability')
            ->with(['balances' => function($q) use ($period) {
                $q->where('fiscal_period_id', $period->id);
            }])
            ->get()
            ->sum(function($account) {
                $balance = $account->balances->first();
                return $balance ? $balance->closing_balance : 0;
            });
        
        // حقوق الملكية
        $equity = Account::where('type', 'equity')
            ->with(['balances' => function($q) use ($period) {
                $q->where('fiscal_period_id', $period->id);
            }])
            ->get()
            ->sum(function($account) {
                $balance = $account->balances->first();
                return $balance ? $balance->closing_balance : 0;
            });
        
        $totalAssets = $assets->sum();
        $totalLiabilitiesAndEquity = $liabilities + $equity;
        
        return view('reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilitiesAndEquity', 'period'
        ));
    }
    
    // 3. قائمة التدفقات النقدية (Cash Flow Statement)
    public function cashFlowStatement(Request $request)
    {
        $period = FiscalPeriod::find($request->period_id);
        
        // الأنشطة التشغيلية
        $operatingActivities = [
            'cash_from_customers' => $this->getCashFromCustomers($period),
            'cash_to_suppliers' => $this->getCashToSuppliers($period),
            'operating_expenses' => $this->getCashOperatingExpenses($period),
        ];
        
        $netOperatingCash = $operatingActivities['cash_from_customers'] 
                          - $operatingActivities['cash_to_suppliers']
                          - $operatingActivities['operating_expenses'];
        
        // الأنشطة الاستثمارية
        $investingActivities = [
            'purchase_of_assets' => $this->getFixedAssetPurchases($period),
            'sale_of_assets' => $this->getFixedAssetSales($period),
        ];
        
        $netInvestingCash = $investingActivities['sale_of_assets'] 
                          - $investingActivities['purchase_of_assets'];
        
        // الأنشطة التمويلية
        $financingActivities = [
            'loans_received' => $this->getLoansReceived($period),
            'loans_paid' => $this->getLoansPaid($period),
        ];
        
        $netFinancingCash = $financingActivities['loans_received'] 
                          - $financingActivities['loans_paid'];
        
        $netCashChange = $netOperatingCash + $netInvestingCash + $netFinancingCash;
        
        return view('reports.cash-flow', compact(
            'operatingActivities', 'investingActivities', 
            'financingActivities', 'netCashChange', 'period'
        ));
    }
}
```

**✅ النتيجة:** القوائم المالية الثلاثة الرئيسية

---

### **اليوم 38-39: Dashboard & KPIs**
#### الدرس 38-39: لوحة التحكم والمؤشرات
**🎯 الهدف:** Dashboard تفاعلية مع مؤشرات الأداء

**💻 KPIs:**
```php
class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            // المؤشرات المالية
            'total_sales' => $this->getTotalSales(),
            'total_purchases' => $this->getTotalPurchases(),
            'total_expenses' => $this->getTotalExpenses(),
            'net_profit' => $this->getNetProfit(),
            
            // مؤشرات المبيعات
            'sales_today' => $this->getSalesToday(),
            'sales_this_month' => $this->getSalesThisMonth(),
            'top_selling_products' => $this->getTopSellingProducts(10),
            'top_customers' => $this->getTopCustomers(10),
            
            // مؤشرات المخزون
            'low_stock_products' => $this->getLowStockProducts(),
            'total_inventory_value' => $this->getTotalInventoryValue(),
            'out_of_stock_count' => $this->getOutOfStockCount(),
            
            // مؤشرات الذمم
            'total_receivables' => $this->getTotalReceivables(),
            'overdue_receivables' => $this->getOverdueReceivables(),
            'total_payables' => $this->getTotalPayables(),
            
            // مؤشرات HR
            'total_employees' => Employee::where('status', 'active')->count(),
            'employees_on_leave' => $this->getEmployeesOnLeave(),
            'pending_leave_requests' => Leave::where('status', 'pending')->count(),
            
            // الرسوم البيانية
            'sales_chart' => $this->getSalesChartData(),
            'profit_chart' => $this->getProfitChartData(),
            'expenses_chart' => $this->getExpensesBreakdown(),
        ];
        
        return view('dashboard', $data);
    }
    
    private function getSalesChartData()
    {
        // مبيعات آخر 12 شهر
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $sales = SalesInvoice::whereMonth('invoice_date', $month->month)
                ->whereYear('invoice_date', $month->year)
                ->where('status', 'confirmed')
                ->sum('total_amount');
            
            $data[] = [
                'month' => $month->format('M Y'),
                'sales' => $sales
            ];
        }
        
        return $data;
    }
    
    private function getTopSellingProducts($limit = 10)
    {
        return Product::withSum(['invoiceItems' => function($q) {
            $q->whereHas('salesInvoice', function($q2) {
                $q2->where('status', 'confirmed')
                    ->whereMonth('invoice_date', now()->month);
            });
        }], 'quantity')
        ->orderByDesc('invoice_items_sum_quantity')
        ->limit($limit)
        ->get();
    }
}
```

**🎨 Blade View مع Charts:**
```blade
<!-- resources/views/dashboard.blade.php -->
<div class="row">
    <!-- KPI Cards -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>إجمالي المبيعات</h5>
                <h2>{{ number_format($total_sales, 2) }} ريال</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>صافي الربح</h5>
                <h2>{{ number_format($net_profit, 2) }} ريال</h2>
            </div>
        </div>
    </div>
    
    <!-- المزيد من الكروت -->
</div>

<div class="row mt-4">
    <!-- Sales Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">مبيعات آخر 12 شهر</div>
            <div class="card-body">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Products -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">الأكثر مبيعاً</div>
            <div class="card-body">
                <table class="table">
                    @foreach($top_selling_products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->invoice_items_sum_quantity }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($sales_chart, 'month')) !!},
        datasets: [{
            label: 'المبيعات',
            data: {!! json_encode(array_column($sales_chart, 'sales')) !!},
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});
</script>
@endpush
```

**✅ النتيجة:** Dashboard احترافية مع Charts

---

### **اليوم 40-41: تقارير مقارنة وتحليلية**
#### الدرس 40-41: التقارير التحليلية المتقدمة
**🎯 الهدف:** تقارير مقارنة وتحليل الاتجاهات

**📊 التقارير:**
```php
class AnalyticalReportController extends Controller
{
    // 1. تقرير مقارنة المبيعات (شهر بشهر، سنة بسنة)
    public function salesComparison(Request $request)
    {
        $currentPeriod = $request->current_period;
        $comparisonPeriod = $request->comparison_period;
        
        $current = SalesInvoice::whereBetween('invoice_date', 
            [$currentPeriod['from'], $currentPeriod['to']])
            ->selectRaw('
                SUM(total_amount) as total,
                COUNT(*) as invoice_count,
                AVG(total_amount) as average_invoice
            ')
            ->first();
        
        $comparison = SalesInvoice::whereBetween('invoice_date',
            [$comparisonPeriod['from'], $comparisonPeriod['to']])
            ->selectRaw('
                SUM(total_amount) as total,
                COUNT(*) as invoice_count,
                AVG(total_amount) as average_invoice
            ')
            ->first();
        
        $growth = [
            'total' => (($current->total - $comparison->total) / $comparison->total) * 100,
            'count' => (($current->invoice_count - $comparison->invoice_count) / $comparison->invoice_count) * 100,
            'average' => (($current->average_invoice - $comparison->average_invoice) / $comparison->average_invoice) * 100,
        ];
        
        return view('reports.sales-comparison', compact('current', 'comparison', 'growth'));
    }
    
    // 2. تحليل الربحية حسب المنتج
    public function productProfitability()
    {
        $products = Product::with(['invoiceItems' => function($q) {
            $q->whereHas('salesInvoice', function($q2) {
                $q2->where('status', 'confirmed')
                    ->whereYear('invoice_date', now()->year);
            });
        }])
        ->get()
        ->map(function($product) {
            $totalSales = $product->invoiceItems->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });
            
            $totalCost = $product->invoiceItems->sum(function($item) {
                return $item->quantity * $product->cost_price;
            });
            
            $profit = $totalSales - $totalCost;
            $profitMargin = $totalSales > 0 ? ($profit / $totalSales) * 100 : 0;
            
            return [
                'product' => $product->name,
                'sales' => $totalSales,
                'cost' => $totalCost,
                'profit' => $profit,
                'margin' => $profitMargin,
            ];
        })
        ->sortByDesc('profit');
        
        return view('reports.product-profitability', compact('products'));
    }
    
    // 3. تحليل العملاء (ABC Analysis)
    public function customerABCAnalysis()
    {
        $customers = Customer::withSum(['invoices' => function($q) {
            $q->where('status', 'confirmed')
                ->whereYear('invoice_date', now()->year);
        }], 'total_amount')
        ->orderByDesc('invoices_sum_total_amount')
        ->get();
        
        $totalSales = $customers->sum('invoices_sum_total_amount');
        $cumulativePercentage = 0;
        
        $analyzed = $customers->map(function($customer) use ($totalSales, &$cumulativePercentage) {
            $percentage = ($customer->invoices_sum_total_amount / $totalSales) * 100;
            $cumulativePercentage += $percentage;
            
            // تصنيف ABC
            if ($cumulativePercentage <= 80) {
                $category = 'A'; // 80% من المبيعات
            } elseif ($cumulativePercentage <= 95) {
                $category = 'B'; // 15% من المبيعات
            } else {
                $category = 'C'; // 5% من المبيعات
            }
            
            return [
                'customer' => $customer->name,
                'sales' => $customer->invoices_sum_total_amount,
                'percentage' => $percentage,
                'cumulative' => $cumulativePercentage,
                'category' => $category,
            ];
        });
        
        return view('reports.customer-abc', compact('analyzed'));
    }
    
    // 4. تحليل الاتجاهات (Trend Analysis)
    public function trendAnalysis(Request $request)
    {
        $months = 12;
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            
            $trends[] = [
                'period' => $month->format('M Y'),
                'sales' => $this->getSalesForPeriod($month),
                'purchases' => $this->getPurchasesForPeriod($month),
                'expenses' => $this->getExpensesForPeriod($month),
                'profit' => $this->getProfitForPeriod($month),
            ];
        }
        
        // حساب خط الاتجاه (Linear Regression)
        $trendLine = $this->calculateTrendLine($trends);
        
        return view('reports.trend-analysis', compact('trends', 'trendLine'));
    }
}
```

**✅ النتيجة:** تقارير تحليلية متقدمة

---

### **اليوم 42: تصدير التقارير**
#### الدرس 42: تصدير التقارير (PDF, Excel)
**🎯 الهدف:** تصدير جميع التقارير

**💻 التطبيق:**
```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

```php
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function exportPDF($reportType, Request $request)
    {
        $data = $this->getReportData($reportType, $request);
        $pdf = Pdf::loadView("reports.pdf.{$reportType}", $data);
        
        return $pdf->download("{$reportType}-" . now()->format('Y-m-d') . ".pdf");
    }
    
    public function exportExcel($reportType, Request $request)
    {
        $data = $this->getReportData($reportType, $request);
        
        return Excel::download(
            new ReportExport($reportType, $data),
            "{$reportType}-" . now()->format('Y-m-d') . ".xlsx"
        );
    }
}
```

**✅ النتيجة:** تصدير التقارير بصيغ متعددة

---

## 🗓️ المرحلة السابعة: التحول إلى Vue.js (الأسبوع 7-8)

### **اليوم 43-44: إعداد Inertia.js + Vue 3**
#### الدرس 43-44: تجهيز البيئة للانتقال لـ Vue
**🎯 الهدف:** تحويل المشروع من Blade إلى Vue.js

**💻 التثبيت:**
```bash
# تثبيت Inertia.js
composer require inertiajs/inertia-laravel

# تثبيت Vue 3 و Inertia adapter
npm install @inertiajs/vue3 vue@next
npm install @vitejs/plugin-vue

# تثبيت مكتبات إضافية
npm install pinia vue-router
npm install @heroicons/vue
npm install @headlessui/vue
```

**🔧 إعداد vite.config.js:**
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
```

**🔧 إعداد resources/js/app.js:**
```javascript
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia()
        
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .mount(el)
    },
})
```

**🔧 Middleware Setup:**
```php
// app/Http/Middleware/HandleInertiaRequests.php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'company' => session('company'),
            'fiscalYear' => session('fiscal_year'),
        ]);
    }
}
```

**✅ النتيجة:** بيئة Vue.js جاهزة

---

### **اليوم 45-46: Layout وMaster Components**
#### الدرس 45-46: بناء المكونات الأساسية
**🎯 الهدف:** إنشاء الهيكل الأساسي للتطبيق

**💻 AppLayout.vue:**
```vue
<!-- resources/js/Layouts/AppLayout.vue -->
<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 right-0 w-64 bg-gray-800">
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-20 bg-gray-900">
          <h1 class="text-2xl font-bold text-white">ERP System</h1>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4">
          <SidebarLink 
            v-for="item in navigation" 
            :key="item.name"
            :href="item.href"
            :icon="item.icon"
            :active="item.active"
          >
            {{ item.name }}
          </SidebarLink>
        </nav>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="mr-64">
      <!-- Top Bar -->
      <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-6 py-4">
          <h2 class="text-2xl font-semibold text-gray-800">
            {{ pageTitle }}
          </h2>
          
          <div class="flex items-center gap-4">
            <!-- Notifications -->
            <NotificationDropdown />
            
            <!-- User Menu -->
            <UserDropdown :user="$page.props.auth.user" />
          </div>
        </div>
      </header>
      
      <!-- Flash Messages -->
      <FlashMessages />
      
      <!-- Page Content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SidebarLink from '@/Components/SidebarLink.vue'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'
import UserDropdown from '@/Components/UserDropdown.vue'
import FlashMessages from '@/Components/FlashMessages.vue'

const page = usePage()

const navigation = computed(() => [
  {
    name: 'لوحة التحكم',
    href: route('dashboard'),
    icon: 'HomeIcon',
    active: route().current('dashboard')
  },
  {
    name: 'المحاسبة',
    href: '#',
    icon: 'ChartBarIcon',
    children: [
      { name: 'شجرة الحسابات', href: route('accounts.index') },
      { name: 'القيود اليومية', href: route('journal-entries.index') },
      { name: 'سندات القبض', href: route('receipts.index') },
      { name: 'سندات الصرف', href: route('payments.index') },
    ]
  },
  {
    name: 'المبيعات',
    href: '#',
    icon: 'ShoppingCartIcon',
    children: [
      { name: 'العملاء', href: route('customers.index') },
      { name: 'الفواتير', href: route('sales-invoices.index') },
      { name: 'عروض الأسعار', href: route('quotations.index') },
    ]
  },
  {
    name: 'المشتريات',
    href: '#',
    icon: 'ShoppingBagIcon',
    children: [
      { name: 'الموردين', href: route('suppliers.index') },
      { name: 'فواتير المشتريات', href: route('purchase-invoices.index') },
    ]
  },
  {
    name: 'المخزون',
    href: '#',
    icon: 'CubeIcon',
    children: [
      { name: 'المنتجات', href: route('products.index') },
      { name: 'المستودعات', href: route('warehouses.index') },
      { name: 'حركات المخزون', href: route('inventory-transactions.index') },
    ]
  },
  {
    name: 'الموارد البشرية',
    href: '#',
    icon: 'UsersIcon',
    children: [
      { name: 'الموظفين', href: route('employees.index') },
      { name: 'الحضور', href: route('attendance.index') },
      { name: 'الرواتب', href: route('payrolls.index') },
    ]
  },
  {
    name: 'التقارير',
    href: '#',
    icon: 'DocumentReportIcon',
    children: [
      { name: 'التقارير المالية', href: route('reports.financial') },
      { name: 'تقارير المبيعات', href: route('reports.sales') },
      { name: 'تقارير المخزون', href: route('reports.inventory') },
    ]
  },
  {
    name: 'الإعدادات',
    href: route('settings.index'),
    icon: 'CogIcon',
    active: route().current('settings.*')
  },
])
</script>
```

**💻 SidebarLink Component:**
```vue
<!-- resources/js/Components/SidebarLink.vue -->
<template>
  <Link
    :href="href"
    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
    :class="{ 'bg-gray-700 text-white': active }"
  >
    <component :is="icon" class="w-5 h-5 ml-3" />
    <span>{{ children }}</span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import * as Icons from '@heroicons/vue/24/outline'

const props = defineProps({
  href: String,
  icon: String,
  active: Boolean,
})

const icon = Icons[props.icon]
</script>
```

**✅ النتيجة:** Layout احترافي مع Sidebar

---

### **اليوم 47-48: تحويل وحدة المحاسبة**
#### الدرس 47-48: تحويل صفحات المحاسبة لـ Vue
**🎯 الهدف:** تحويل شجرة الحسابات والقيود

**💻 Accounts/Index.vue:**
```vue
<!-- resources/js/Pages/Accounts/Index.vue -->
<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">شجرة الحسابات</h1>
        <button
          @click="openCreateModal"
          class="btn-primary"
        >
          إضافة حساب جديد
        </button>
      </div>
      
      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="grid grid-cols-4 gap-4">
          <input
            v-model="filters.search"
            type="text"
            placeholder="بحث..."
            class="form-input"
          />
          <select v-model="filters.type" class="form-select">
            <option value="">كل الأنواع</option>
            <option value="asset">أصول</option>
            <option value="liability">خصوم</option>
            <option value="equity">حقوق ملكية</option>
            <option value="revenue">إيرادات</option>
            <option value="expense">مصروفات</option>
          </select>
        </div>
      </div>
      
      <!-- Accounts Tree -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                الكود
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                اسم الحساب
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                النوع
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                الرصيد
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                إجراءات
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <AccountRow
              v-for="account in accounts"
              :key="account.id"
              :account="account"
              :level="0"
              @edit="editAccount"
              @delete="deleteAccount"
            />
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <Pagination :links="accounts.links" />
    </div>
    
    <!-- Create/Edit Modal -->
    <AccountModal
      v-if="showModal"
      :account="selectedAccount"
      :accounts="allAccounts"
      @close="closeModal"
      @saved="handleSaved"
    />
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AccountRow from '@/Components/Accounts/AccountRow.vue'
import AccountModal from '@/Components/Accounts/AccountModal.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  accounts: Object,
  allAccounts: Array,
})

const filters = ref({
  search: '',
  type: '',
})

const showModal = ref(false)
const selectedAccount = ref(null)

// Watch filters and reload data
watch(filters, () => {
  router.get(route('accounts.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}, { deep: true })

const openCreateModal = () => {
  selectedAccount.value = null
  showModal.value = true
}

const editAccount = (account) => {
  selectedAccount.value = account
  showModal.value = true
}

const deleteAccount = (account) => {
  if (confirm('هل أنت متأكد من الحذف؟')) {
    router.delete(route('accounts.destroy', account.id))
  }
}

const closeModal = () => {
  showModal.value = false
  selectedAccount.value = null
}

const handleSaved = () => {
  closeModal()
  router.reload()
}
</script>
```

**💻 AccountModal Component:**
```vue
<!-- resources/js/Components/Accounts/AccountModal.vue -->
<template>
  <TransitionRoot :show="true" as="template">
    <Dialog as="div" class="relative z-50" @close="$emit('close')">
      <TransitionChild
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-50" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            enter="ease-out duration-300"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-lg bg-white p-6 shadow-xl transition-all">
              <DialogTitle class="text-lg font-medium text-gray-900 mb-4">
                {{ form.id ? 'تعديل حساب' : 'إضافة حساب جديد' }}
              </DialogTitle>

              <form @submit.prevent="submit" class="space-y-4">
                <!-- Parent Account -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    الحساب الأب
                  </label>
                  <select v-model="form.parent_id" class="form-select">
                    <option :value="null">حساب رئيسي</option>
                    <option
                      v-for="acc in accounts.filter(a => a.is_parent)"
                      :key="acc.id"
                      :value="acc.id"
                    >
                      {{ acc.code }} - {{ acc.name_ar }}
                    </option>
                  </select>
                  <div v-if="form.errors.parent_id" class="text-red-600 text-sm mt-1">
                    {{ form.errors.parent_id }}
                  </div>
                </div>

                <!-- Account Code -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    الكود
                  </label>
                  <input
                    v-model="form.code"
                    type="text"
                    class="form-input"
                    required
                  />
                  <div v-if="form.errors.code" class="text-red-600 text-sm mt-1">
                    {{ form.errors.code }}
                  </div>
                </div>

                <!-- Account Name Arabic -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    اسم الحساب (عربي)
                  </label>
                  <input
                    v-model="form.name_ar"
                    type="text"
                    class="form-input"
                    required
                  />
                  <div v-if="form.errors.name_ar" class="text-red-600 text-sm mt-1">
                    {{ form.errors.name_ar }}
                  </div>
                </div>

                <!-- Account Type -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    النوع
                  </label>
                  <select v-model="form.type" class="form-select" required>
                    <option value="asset">أصول</option>
                    <option value="liability">خصوم</option>
                    <option value="equity">حقوق ملكية</option>
                    <option value="revenue">إيرادات</option>
                    <option value="expense">مصروفات</option>
                  </select>
                </div>

                <!-- Nature -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    الطبيعة
                  </label>
                  <select v-model="form.nature" class="form-select" required>
                    <option value="debit">مدين</option>
                    <option value="credit">دائن</option>
                  </select>
                </div>

                <!-- Is Parent -->
                <div class="flex items-center">
                  <input
                    v-model="form.is_parent"
                    type="checkbox"
                    class="form-checkbox"
                  />
                  <label class="mr-2 text-sm text-gray-700">
                    حساب رئيسي (له حسابات فرعية)
                  </label>
                </div>

                <!-- Can Post -->
                <div class="flex items-center">
                  <input
                    v-model="form.can_post"
                    type="checkbox"
                    class="form-checkbox"
                  />
                  <label class="mr-2 text-sm text-gray-700">
                    يقبل القيود
                  </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                  <button
                    type="button"
                    @click="$emit('close')"
                    class="btn-secondary"
                  >
                    إلغاء
                  </button>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="btn-primary"
                  >
                    حفظ
                  </button>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from '@headlessui/vue'

const props = defineProps({
  account: Object,
  accounts: Array,
})

const emit = defineEmits(['close', 'saved'])

const form = useForm({
  id: props.account?.id || null,
  parent_id: props.account?.parent_id || null,
  code: props.account?.code || '',
  name_ar: props.account?.name_ar || '',
  name_en: props.account?.name_en || '',
  type: props.account?.type || 'asset',
  nature: props.account?.nature || 'debit',
  is_parent: props.account?.is_parent || false,
  can_post: props.account?.can_post ?? true,
})

const submit = () => {
  const url = form.id 
    ? route('accounts.update', form.id)
    : route('accounts.store')
  
  const method = form.id ? 'put' : 'post'
  
  form[method](url, {
    onSuccess: () => emit('saved'),
  })
}
</script>
```

**✅ النتيجة:** وحدة المحاسبة بـ Vue

---

### **اليوم 49-50: Pinia Store للحالة**
#### الدرس 49-50: إدارة الحالة باستخدام Pinia
**🎯 الهدف:** إدارة الحالة العامة للتطبيق

**💻 stores/auth.js:**
```javascript
// resources/js/stores/auth.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const permissions = ref([])
  const roles = ref([])
  
  const isAuthenticated = computed(() => user.value !== null)
  
  const hasPermission = (permission) => {
    return permissions.value.includes(permission)
  }
  
  const hasRole = (role) => {
    return roles.value.includes(role)
  }
  
  const hasAnyPermission = (perms) => {
    return perms.some(p => permissions.value.includes(p))
  }
  
  const setUser = (userData) => {
    user.value = userData
    permissions.value = userData?.permissions || []
    roles.value = userData?.roles || []
  }
  
  return {
    user,
    permissions,
    roles,
    isAuthenticated,
    hasPermission,
    hasRole,
    hasAnyPermission,
    setUser,
  }
})
```

**💻 stores/company.js:**
```javascript
// resources/js/stores/company.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useCompanyStore = defineStore('company', () => {
  const currentCompany = ref(null)
  const currentBranch = ref(null)
  const currentFiscalYear = ref(null)
  
  const setCompany = (company) => {
    currentCompany.value = company
    localStorage.setItem('current_company', JSON.stringify(company))
  }
  
  const setBranch = (branch) => {
    currentBranch.value = branch
    localStorage.setItem('current_branch', JSON.stringify(branch))
  }
  
  const setFiscalYear = (year) => {
    currentFiscalYear.value = year
    localStorage.setItem('current_fiscal_year', JSON.stringify(year))
  }
  
  const switchCompany = async (companyId) => {
    const response = await axios.post('/api/switch-company', { company_id: companyId })
    setCompany(response.data.company)
    window.location.reload()
  }
  
  return {
    currentCompany,
    currentBranch,
    currentFiscalYear,
    setCompany,
    setBranch,
    setFiscalYear,
    switchCompany,
  }
})
```

**💻 stores/invoice.js:**
```javascript
// resources/js/stores/invoice.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useInvoiceStore = defineStore('invoice', () => {
  const items = ref([])
  const customer = ref(null)
  const discount = ref(0)
  const taxRate = ref(15)
  
  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => {
      return sum + (item.quantity * item.unit_price)
    }, 0)
  })
  
  const discountAmount = computed(() => {
    return (subtotal.value * discount.value) / 100
  })
  
  const taxableAmount = computed(() => {
    return subtotal.value - discountAmount.value
  })
  
  const taxAmount = computed(() => {
    return (taxableAmount.value * taxRate.value) / 100
  })
  
  const total = computed(() => {
    return taxableAmount.value + taxAmount.value
  })
  
  const addItem = (product) => {
    const existingItem = items.value.find(i => i.product_id === product.id)
    
    if (existingItem) {
      existingItem.quantity++
    } else {
      items.value.push({
        product_id: product.id,
        product_name: product.name,
        quantity: 1,
        unit_price: product.selling_price,
        discount: 0,
        tax_rate: taxRate.value,
      })
    }
  }
  
  const removeItem = (index) => {
    items.value.splice(index, 1)
  }
  
  const updateItemQuantity = (index, quantity) => {
    items.value[index].quantity = quantity
  }
  
  const clearInvoice = () => {
    items.value = []
    customer.value = null
    discount.value = 0
  }
  
  return {
    items,
    customer,
    discount,
    taxRate,
    subtotal,
    discountAmount,
    taxAmount,
    total,
    addItem,
    removeItem,
    updateItemQuantity,
    clearInvoice,
  }
})
```

**✅ النتيجة:** إدارة حالة احترافية

---

### **اليوم 51-52: تحويل وحدة المبيعات**
#### الدرس 51-52: فواتير المبيعات بـ Vue
**🎯 الهدف:** نظام فواتير تفاعلي كامل

**💻 SalesInvoices/Create.vue:**
```vue
<!-- resources/js/Pages/SalesInvoices/Create.vue -->
<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">فاتورة مبيعات جديدة</h1>
      
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Customer & Date Info -->
        <div class="bg-white p-6 rounded-lg shadow">
          <div class="grid grid-cols-3 gap-4">
            <!-- Customer Selection -->
            <div class="col-span-2">
              <label class="block text-sm font-medium mb-2">العميل</label>
              <CustomerSearch
                v-model="form.customer_id"
                @selected="selectCustomer"
              />
              <div v-if="form.errors.customer_id" class="text-red-600 text-sm mt-1">
                {{ form.errors.customer_id }}
              </div>
            </div>
            
            <!-- Invoice Date -->
            <div>
              <label class="block text-sm font-medium mb-2">تاريخ الفاتورة</label>
              <input
                v-model="form.invoice_date"
                type="date"
                class="form-input"
                required
              />
            </div>
          </div>
          
          <!-- Customer Details (if selected) -->
          <div v-if="selectedCustomer" class="mt-4 p-4 bg-gray-50 rounded">
            <div class="grid grid-cols-3 gap-4 text-sm">
              <div>
                <span class="font-medium">الرقم الضريبي:</span>
                {{ selectedCustomer.tax_number }}
              </div>
              <div>
                <span class="font-medium">الهاتف:</span>
                {{ selectedCustomer.phone }}
              </div>
              <div>
                <span class="font-medium">الرصيد:</span>
                <span :class="selectedCustomer.balance > 0 ? 'text-red-600' : 'text-green-600'">
                  {{ formatNumber(selectedCustomer.balance) }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Products -->
        <div class="bg-white p-6 rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">الأصناف</h2>
            <button
              type="button"
              @click="showProductModal = true"
              class="btn-primary"
            >
              إضافة صنف
            </button>
          </div>
          
          <!-- Items Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-right">#</th>
                  <th class="px-4 py-2 text-right">الصنف</th>
                  <th class="px-4 py-2 text-right">الكمية</th>
                  <th class="px-4 py-2 text-right">السعر</th>
                  <th class="px-4 py-2 text-right">الخصم</th>
                  <th class="px-4 py-2 text-right">الضريبة</th>
                  <th class="px-4 py-2 text-right">الإجمالي</th>
                  <th class="px-4 py-2"></th>
                </tr>
              </thead>
              <tbody>
                <InvoiceItemRow
                  v-for="(item, index) in form.items"
                  :key="index"
                  :item="item"
                  :index="index"
                  @update="updateItem"
                  @remove="removeItem"
                />
                <tr v-if="form.items.length === 0">
                  <td colspan="8" class="text-center py-8 text-gray-500">
                    لا توجد أصناف. اضغط "إضافة صنف" للبدء
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Totals -->
        <div class="bg-white p-6 rounded-lg shadow">
          <div class="max-w-md ml-auto space-y-3">
            <div class="flex justify-between text-lg">
              <span>المجموع الفرعي:</span>
              <span class="font-semibold">{{ formatNumber(subtotal) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span>الخصم:</span>
              <div class="flex items-center gap-2">
                <input
                  v-model.number="form.discount_percentage"
                  type="number"
                  min="0"
                  max="100"
                  step="0.01"
                  class="form-input w-20"
                />
                <span>%</span>
                <span class="font-semibold">
                  {{ formatNumber(discountAmount) }}
                </span>
              </div>
            </div>
            
            <div class="flex justify-between text-lg">
              <span>الضريبة (15%):</span>
              <span class="font-semibold">{{ formatNumber(taxAmount) }}</span>
            </div>
            
            <div class="border-t-2 pt-3 flex justify-between text-xl font-bold">
              <span>الإجمالي:</span>
              <span class="text-blue-600">{{ formatNumber(total) }}</span>
            </div>
          </div>
        </div>
        
        <!-- Notes -->
        <div class="bg-white p-6 rounded-lg shadow">
          <label class="block text-sm font-medium mb-2">ملاحظات</label>
          <textarea
            v-model="form.notes"
            rows="3"
            class="form-input"
            placeholder="أي ملاحظات إضافية..."
          ></textarea>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <Link :href="route('sales-invoices.index')" class="btn-secondary">
            إلغاء
          </Link>
          <button
            type="submit"
            :disabled="form.processing || form.items.length === 0"
            class="btn-primary"
          >
            حفظ الفاتورة
          </button>
        </div>
      </form>
    </div>
    
    <!-- Product Selection Modal -->
    <ProductSelectionModal
      v-if="showProductModal"
      @close="showProductModal = false"
      @select="addProduct"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomerSearch from '@/Components/CustomerSearch.vue'
import InvoiceItemRow from '@/Components/Invoices/InvoiceItemRow.vue'
import ProductSelectionModal from '@/Components/Invoices/ProductSelectionModal.vue'

const props = defineProps({
  customers: Array,
  warehouses: Array,
})

const showProductModal = ref(false)
const selectedCustomer = ref(null)

const form = useForm({
  customer_id: null,
  invoice_date: new Date().toISOString().split('T')[0],
  due_date: null,
  warehouse_id: null,
  items: [],
  discount_percentage: 0,
  notes: '',
})

const subtotal = computed(() => {
  return form.items.reduce((sum, item) => {
    return sum + (item.quantity * item.unit_price)
  }, 0)
})

const discountAmount = computed(() => {
  return (subtotal.value * form.discount_percentage) / 100
})

const taxableAmount = computed(() => {
  return subtotal.value - discountAmount.value
})

const taxAmount = computed(() => {
  return form.items.reduce((sum, item) => {
    const itemTotal = item.quantity * item.unit_price
    return sum + (itemTotal * item.tax_percentage / 100)
  }, 0)
})

const total = computed(() => {
  return taxableAmount.value + taxAmount.value
})

const selectCustomer = (customer) => {
  selectedCustomer.value = customer
  form.customer_id = customer.id
  
  // Set default due date based on payment terms
  const dueDate = new Date()
  dueDate.setDate(dueDate.getDate() + customer.payment_terms_days)
  form.due_date = dueDate.toISOString().split('T')[0]
}

const addProduct = (product) => {
  const existingItem = form.items.find(i => i.product_id === product.id)
  
  if (existingItem) {
    existingItem.quantity++
  } else {
    form.items.push({
      product_id: product.id,
      product_name: product.name,
      warehouse_id: props.warehouses[0]?.id,
      quantity: 1,
      unit_price: product.selling_price,
      discount_percentage: 0,
      tax_percentage: 15,
    })
  }
  
  showProductModal.value = false
}

const updateItem = (index, field, value) => {
  form.items[index][field] = value
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const submit = () => {
  form.transform((data) => ({
    ...data,
    subtotal: subtotal.value,
    discount_amount: discountAmount.value,
    tax_amount: taxAmount.value,
    total_amount: total.value,
  })).post(route('sales-invoices.store'))
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('ar-SA', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(num)
}
</script>
```

**💻 InvoiceItemRow Component:**
```vue
<!-- resources/js/Components/Invoices/InvoiceItemRow.vue -->
<template>
  <tr class="border-b hover:bg-gray-50">
    <td class="px-4 py-3">{{ index + 1 }}</td>
    <td class="px-4 py-3">{{ item.product_name }}</td>
    <td class="px-4 py-3">
      <input
        :value="item.quantity"
        @input="$emit('update', index, 'quantity', parseFloat($event.target.value))"
        type="number"
        min="1"
        step="0.001"
        class="form-input w-24"
      />
    </td>
    <td class="px-4 py-3">
      <input
        :value="item.unit_price"
        @input="$emit('update', index, 'unit_price', parseFloat($event.target.value))"
        type="number"
        min="0"
        step="0.01"
        class="form-input w-28"
      />
    </td>
    <td class="px-4 py-3">
      <input
        :value="item.discount_percentage"
        @input="$emit('update', index, 'discount_percentage', parseFloat($event.target.value))"
        type="number"
        min="0"
        max="100"
        step="0.01"
        class="form-input w-20"
      />
    </td>
    <td class="px-4 py-3">
      {{ item.tax_percentage }}%
    </td>
    <td class="px-4 py-3 font-semibold">
      {{ formatNumber(itemTotal) }}
    </td>
    <td class="px-4 py-3">
      <button
        type="button"
        @click="$emit('remove', index)"
        class="text-red-600 hover:text-red-800"
      >
        <TrashIcon class="w-5 h-5" />
      </button>
    </td>
  </tr>
</template>

<script setup>
import { computed } from 'vue'
import { TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  item: Object,
  index: Number,
})

defineEmits(['update', 'remove'])

const itemTotal = computed(() => {
  const subtotal = props.item.quantity * props.item.unit_price
  const discount = (subtotal * props.item.discount_percentage) / 100
  const taxable = subtotal - discount
  const tax = (taxable * props.item.tax_percentage) / 100
  return taxable + tax
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('ar-SA', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(num)
}
</script>
```

**✅ النتيجة:** نظام فواتير تفاعلي كامل

---

### **اليوم 53-54: Composables وReusable Logic**
#### الدرس 53-54: إنشاء Composables قابلة لإعادة الاستخدام
**🎯 الهدف:** كود نظيف وقابل لإعادة الاستخدام

**💻 composables/useFormatting.js:**
```javascript
// resources/js/composables/useFormatting.js
import { ref } from 'vue'

export function useFormatting() {
  const formatNumber = (num, decimals = 2) => {
    return new Intl.NumberFormat('ar-SA', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    }).format(num || 0)
  }
  
  const formatCurrency = (num) => {
    return formatNumber(num) + ' ريال'
  }
  
  const formatDate = (date, format = 'long') => {
    if (!date) return ''
    
    const options = {
      long: { year: 'numeric', month: 'long', day: 'numeric' },
      short: { year: 'numeric', month: '2-digit', day: '2-digit' },
      time: { hour: '2-digit', minute: '2-digit' },
    }
    
    return new Intl.DateTimeFormat('ar-SA', options[format]).format(new Date(date))
  }
  
  const formatPercentage = (num) => {
    return formatNumber(num) + '%'
  }
  
  return {
    formatNumber,
    formatCurrency,
    formatDate,
    formatPercentage,
  }
}
```

**💻 composables/useFilters.js:**
```javascript
// resources/js/composables/useFilters.js
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

export function useFilters(routeName, initialFilters = {}) {
  const filters = ref({ ...initialFilters })
  const loading = ref(false)
  
  const applyFilters = debounce(() => {
    loading.value = true
    router.get(
      route(routeName),
      filters.value,
      {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
          loading.value = false
        },
      }
    )
  }, 300)
  
  watch(
    filters,
    () => {
      applyFilters()
    },
    { deep: true }
  )
  
  const resetFilters = () => {
    filters.value = { ...initialFilters }
  }
  
  return {
    filters,
    loading,
    resetFilters,
  }
}
```

**💻 composables/useInvoiceCalculations.js:**
```javascript
// resources/js/composables/useInvoiceCalculations.js
import { computed } from 'vue'

export function useInvoiceCalculations(items, discountPercentage = 0) {
  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => {
      return sum + (item.quantity * item.unit_price)
    }, 0)
  })
  
  const discountAmount = computed(() => {
    return (subtotal.value * discountPercentage.value) / 100
  })
  
  const taxableAmount = computed(() => {
    return subtotal.value - discountAmount.value
  })
  
  const taxAmount = computed(() => {
    return items.value.reduce((sum, item) => {
      const itemSubtotal = item.quantity * item.unit_price
      const itemDiscount = (itemSubtotal * (item.discount_percentage || 0)) / 100
      const itemTaxable = itemSubtotal - itemDiscount
      return sum + (itemTaxable * (item.tax_percentage || 0) / 100)
    }, 0)
  })
  
  const total = computed(() => {
    return taxableAmount.value + taxAmount.value
  })
  
  const calculateItemTotal = (item) => {
    const itemSubtotal = item.quantity * item.unit_price
    const itemDiscount = (itemSubtotal * (item.discount_percentage || 0)) / 100
    const itemTaxable = itemSubtotal - itemDiscount
    const itemTax = (itemTaxable * (item.tax_percentage || 0)) / 100
    return itemTaxable + itemTax
  }
  
  return {
    subtotal,
    discountAmount,
    taxableAmount,
    taxAmount,
    total,
    calculateItemTotal,
  }
}
```

**💻 composables/usePermissions.js:**
```javascript
// resources/js/composables/usePermissions.js
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermissions() {
  const page = usePage()
  
  const user = computed(() => page.props.auth.user)
  const permissions = computed(() => user.value?.permissions || [])
  const roles = computed(() => user.value?.roles || [])
  
  const can = (permission) => {
    return permissions.value.includes(permission)
  }
  
  const hasRole = (role) => {
    return roles.value.includes(role)
  }
  
  const canAny = (perms) => {
    return perms.some(p => permissions.value.includes(p))
  }
  
  const canAll = (perms) => {
    return perms.every(p => permissions.value.includes(p))
  }
  
  return {
    user,
    permissions,
    roles,
    can,
    hasRole,
    canAny,
    canAll,
  }
}
```

**✅ النتيجة:** Composables قابلة لإعادة الاستخدام

---

### **اليوم 55-56: تحويل التقارير والـ Dashboard**
#### الدرس 55-56: Dashboard تفاعلية مع Charts
**🎯 الهدف:** Dashboard بمؤشرات حية

**💻 Dashboard/Index.vue:**
```vue
<!-- resources/js/Pages/Dashboard/Index.vue -->
<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">لوحة التحكم</h1>
      
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <KPICard
          title="إجمالي المبيعات"
          :value="formatCurrency(stats.total_sales)"
          :change="stats.sales_change"
          icon="CurrencyDollarIcon"
          color="blue"
        />
        
        <KPICard
          title="صافي الربح"
          :value="formatCurrency(stats.net_profit)"
          :change="stats.profit_change"
          icon="TrendingUpIcon"
          color="green"
        />
        
        <KPICard
          title="المبيعات اليوم"
          :value="formatCurrency(stats.sales_today)"
          icon="CalendarIcon"
          color="purple"
        />
        
        <KPICard
          title="عدد الفواتير"
          :value="stats.invoice_count"
          :change="stats.invoice_change"
          icon="DocumentTextIcon"
          color="yellow"
        />
      </div>
      
      <!-- Charts Row 1 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">مبيعات آخر 12 شهر</h2>
          <LineChart :data="salesChartData" height="300" />
        </div>
        
        <!-- Revenue vs Expenses -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">الإيرادات والمصروفات</h2>
          <BarChart :data="revenueExpensesData" height="300" />
        </div>
      </div>
      
      <!-- Charts Row 2 -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Products -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">الأكثر مبيعاً</h2>
          <div class="space-y-3">
            <ProductRankItem
              v-for="(product, index) in topProducts"
              :key="product.id"
              :rank="index + 1"
              :product="product"
            />
          </div>
        </div>
        
        <!-- Top Customers -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">أفضل العملاء</h2>
          <div class="space-y-3">
            <CustomerRankItem
              v-for="(customer, index) in topCustomers"
              :key="customer.id"
              :rank="index + 1"
              :customer="customer"
            />
          </div>
        </div>
        
        <!-- Sales by Category -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">المبيعات حسب الفئة</h2>
          <PieChart :data="salesByCategoryData" height="250" />
        </div>
      </div>
      
      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Invoices -->
        <div class="bg-white p-6 rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">آخر الفواتير</h2>
            <Link :href="route('sales-invoices.index')" class="text-blue-600 hover:text-blue-800">
              عرض الكل
            </Link>
          </div>
          <div class="space-y-3">
            <InvoiceListItem
              v-for="invoice in recentInvoices"
              :key="invoice.id"
              :invoice="invoice"
            />
          </div>
        </div>
        
        <!-- Low Stock Alert -->
        <div class="bg-white p-6 rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">تنبيهات المخزون</h2>
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
              {{ lowStockProducts.length }} منتج
            </span>
          </div>
          <div class="space-y-3">
            <LowStockItem
              v-for="product in lowStockProducts"
              :key="product.id"
              :product="product"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import KPICard from '@/Components/Dashboard/KPICard.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import BarChart from '@/Components/Charts/BarChart.vue'
import PieChart from '@/Components/Charts/PieChart.vue'
import ProductRankItem from '@/Components/Dashboard/ProductRankItem.vue'
import CustomerRankItem from '@/Components/Dashboard/CustomerRankItem.vue'
import InvoiceListItem from '@/Components/Dashboard/InvoiceListItem.vue'
import LowStockItem from '@/Components/Dashboard/LowStockItem.vue'
import { useFormatting } from '@/composables/useFormatting'

const { formatCurrency } = useFormatting()

const props = defineProps({
  stats: Object,
  salesChartData: Object,
  revenueExpensesData: Object,
  salesByCategoryData: Object,
  topProducts: Array,
  topCustomers: Array,
  recentInvoices: Array,
  lowStockProducts: Array,
})
</script>
```

**💻 LineChart Component (Chart.js):**
```vue
<!-- resources/js/Components/Charts/LineChart.vue -->
<template>
  <canvas ref="chartRef"></canvas>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  data: Object,
  height: {
    type: String,
    default: '400',
  },
})

const chartRef = ref(null)
let chartInstance = null

const initChart = () => {
  if (chartInstance) {
    chartInstance.destroy()
  }
  
  const ctx = chartRef.value.getContext('2d')
  
  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: props.data.labels,
      datasets: [{
        label: props.data.label || 'البيانات',
        data: props.data.values,
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          rtl: true,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return new Intl.NumberFormat('ar-SA').format(value)
            }
          }
        }
      }
    }
  })
}

onMounted(() => {
  initChart()
})

watch(() => props.data, () => {
  initChart()
}, { deep: true })
</script>
```

**✅ النتيجة:** Dashboard تفاعلية مع charts

---

### **اليوم 57-58: Real-time Features مع Broadcasting**
#### الدرس 57-58: إضافة الإشعارات الحية
**🎯 الهدف:** إشعارات فورية للأحداث

**💻 تثبيت Laravel Echo:**
```bash
npm install --save laravel-echo pusher-js
```

**🔧 إعداد Echo:**
```javascript
// resources/js/echo.js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
})
```

**💻 Notification Component:**
```vue
<!-- resources/js/Components/Notifications/NotificationCenter.vue -->
<template>
  <div class="relative">
    <button
      @click="toggle"
      class="relative p-2 rounded-full hover:bg-gray-100"
    >
      <BellIcon class="w-6 h-6 text-gray-600" />
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 block h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>
    
    <Transition>
      <div
        v-if="isOpen"
        class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50"
      >
        <div class="p-4 border-b">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">الإشعارات</h3>
            <button
              v-if="unreadCount > 0"
              @click="markAllAsRead"
              class="text-sm text-blue-600 hover:text-blue-800"
            >
              تعليم الكل كمقروء
            </button>
          </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="p-4 border-b hover:bg-gray-50 cursor-pointer"
            :class="{ 'bg-blue-50': !notification.read_at }"
            @click="handleNotificationClick(notification)"
          >
            <div class="flex items-start">
              <component
                :is="getNotificationIcon(notification.type)"
                class="w-5 h-5 ml-3 mt-1"
                :class="getNotificationColor(notification.type)"
              />
              <div class="flex-1">
                <p class="text-sm font-medium">{{ notification.data.title }}</p>
                <p class="text-sm text-gray-600">{{ notification.data.message }}</p>
                <p class="text-xs text-gray-400 mt-1">
                  {{ formatDate(notification.created_at) }}
                </p>
              </div>
            </div>
          </div>
          
          <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500">
            لا توجد إشعارات
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { BellIcon, CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

const isOpen = ref(false)
const notifications = ref([])

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read_at).length
})

const toggle = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    loadNotifications()
  }
}

const loadNotifications = async () => {
  const response = await axios.get('/api/notifications')
  notifications.value = response.data
}

const markAllAsRead = async () => {
  await axios.post('/api/notifications/mark-all-read')
  notifications.value.forEach(n => n.read_at = new Date())
}

const handleNotificationClick = async (notification) => {
  if (!notification.read_at) {
    await axios.post(`/api/notifications/${notification.id}/read`)
    notification.read_at = new Date()
  }
  
  if (notification.data.url) {
    router.visit(notification.data.url)
  }
  
  isOpen.value = false
}

const getNotificationIcon = (type) => {
  const icons = {
    'success': CheckCircleIcon,
    'warning': ExclamationCircleIcon,
    'info': InformationCircleIcon,
  }
  return icons[type] || InformationCircleIcon
}

const getNotificationColor = (type) => {
  const colors = {
    'success': 'text-green-500',
    'warning': 'text-yellow-500',
    'info': 'text-blue-500',
  }
  return colors[type] || 'text-gray-500'
}

// Listen for real-time notifications
onMounted(() => {
  loadNotifications()
  
  window.Echo.private(`App.Models.User.${window.Laravel.user.id}`)
    .notification((notification) => {
      notifications.value.unshift(notification)
      
      // Show toast notification
      // You can use a toast library here
      console.log('New notification:', notification)
    })
})
</script>
```

**✅ النتيجة:** إشعارات حية وفورية

---

## 🗓️ المرحلة الثامنة: الإنهاء والنشر (الأسبوع 9-10)

### **اليوم 59-60: Testing**
#### الدرس 59-60: اختبارات شاملة للنظام
**🎯 الهدف:** ضمان جودة الكود وخلوه من الأخطاء

**💻 Feature Tests:**
```php
// tests/Feature/SalesInvoiceTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $customer;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->product = Product::factory()->create([
            'selling_price' => 100,
            'cost_price' => 60,
        ]);
    }

    /** @test */
    public function it_can_create_sales_invoice()
    {
        $this->actingAs($this->user);
        
        $response = $this->post(route('sales-invoices.store'), [
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'unit_price' => 100,
                    'tax_percentage' => 15,
                ]
            ],
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('sales_invoices', [
            'customer_id' => $this->customer->id,
            'subtotal' => 500,
            'tax_amount' => 75,
            'total_amount' => 575,
        ]);
    }

    /** @test */
    public function it_creates_journal_entry_when_invoice_confirmed()
    {
        $invoice = SalesInvoice::factory()->create([
            'customer_id' => $this->customer->id,
            'total_amount' => 575,
            'status' => 'draft',
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->post(route('sales-invoices.confirm', $invoice));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'sales_invoice',
            'reference_id' => $invoice->id,
            'status' => 'posted',
        ]);
    }

    /** @test */
    public function it_updates_inventory_when_invoice_confirmed()
    {
        $invoice = SalesInvoice::factory()
            ->hasItems(1, [
                'product_id' => $this->product->id,
                'quantity' => 5,
            ])
            ->create(['status' => 'draft']);
        
        $initialQuantity = $this->product->getTotalQuantity();
        
        $this->actingAs($this->user);
        $this->post(route('sales-invoices.confirm', $invoice));
        
        $this->product->refresh();
        $this->assertEquals($initialQuantity - 5, $this->product->getTotalQuantity());
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->actingAs($this->user);
        
        $response = $this->post(route('sales-invoices.store'), []);
        
        $response->assertSessionHasErrors(['customer_id', 'invoice_date']);
    }

    /** @test */
    public function it_prevents_confirming_invoice_with_insufficient_stock()
    {
        $this->product->update(['quantity' => 3]);
        
        $invoice = SalesInvoice::factory()
            ->hasItems(1, [
                'product_id' => $this->product->id,
                'quantity' => 5,
            ])
            ->create(['status' => 'draft']);
        
        $this->actingAs($this->user);
        
        $response = $this->post(route('sales-invoices.confirm', $invoice));
        
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('sales_invoices', [
            'id' => $invoice->id,
            'status' => 'draft',
        ]);
    }
}
```

**💻 Unit Tests:**
```php
// tests/Unit/InvoiceCalculationTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\InvoiceCalculationService;

class InvoiceCalculationTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoiceCalculationService();
    }

    /** @test */
    public function it_calculates_subtotal_correctly()
    {
        $items = [
            ['quantity' => 2, 'unit_price' => 100],
            ['quantity' => 3, 'unit_price' => 50],
        ];
        
        $result = $this->service->calculateSubtotal($items);
        
        $this->assertEquals(350, $result);
    }

    /** @test */
    public function it_calculates_tax_correctly()
    {
        $amount = 1000;
        $taxRate = 15;
        
        $result = $this->service->calculateTax($amount, $taxRate);
        
        $this->assertEquals(150, $result);
    }

    /** @test */
    public function it_calculates_discount_correctly()
    {
        $amount = 1000;
        $discountPercentage = 10;
        
        $result = $this->service->calculateDiscount($amount, $discountPercentage);
        
        $this->assertEquals(100, $result);
    }

    /** @test */
    public function it_calculates_total_correctly()
    {
        $subtotal = 1000;
        $discount = 100;
        $tax = 135; // 15% of (1000 - 100)
        
        $result = $this->service->calculateTotal($subtotal, $discount, $tax);
        
        $this->assertEquals(1035, $result);
    }
}
```

**💻 API Tests:**
```php
// tests/Feature/Api/SalesInvoiceApiTest.php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\SalesInvoice;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesInvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_invoices()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        SalesInvoice::factory()->count(15)->create();
        
        $response = $this->getJson('/api/sales-invoices');
        
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'invoice_number', 'customer', 'total_amount']
                ],
                'links',
                'meta',
            ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/sales-invoices');
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function it_filters_invoices_by_customer()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $invoice = SalesInvoice::factory()->create();
        SalesInvoice::factory()->count(5)->create();
        
        $response = $this->getJson("/api/sales-invoices?customer_id={$invoice->customer_id}");
        
        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
```

**💻 Vue Component Tests:**
```javascript
// resources/js/__tests__/InvoiceForm.spec.js
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import InvoiceForm from '@/Components/Invoices/InvoiceForm.vue'

describe('InvoiceForm', () => {
  it('calculates subtotal correctly', () => {
    const wrapper = mount(InvoiceForm, {
      props: {
        items: [
          { quantity: 2, unit_price: 100 },
          { quantity: 3, unit_price: 50 },
        ]
      }
    })
    
    expect(wrapper.vm.subtotal).toBe(350)
  })

  it('adds product to items', async () => {
    const wrapper = mount(InvoiceForm)
    
    await wrapper.vm.addProduct({
      id: 1,
      name: 'Product 1',
      selling_price: 100
    })
    
    expect(wrapper.vm.items).toHaveLength(1)
    expect(wrapper.vm.items[0].product_id).toBe(1)
  })

  it('removes item from list', async () => {
    const wrapper = mount(InvoiceForm, {
      data() {
        return {
          items: [
            { product_id: 1, quantity: 1 },
            { product_id: 2, quantity: 1 },
          ]
        }
      }
    })
    
    await wrapper.vm.removeItem(0)
    
    expect(wrapper.vm.items).toHaveLength(1)
    expect(wrapper.vm.items[0].product_id).toBe(2)
  })

  it('validates required fields', async () => {
    const wrapper = mount(InvoiceForm)
    
    await wrapper.vm.submit()
    
    expect(wrapper.vm.errors).toHaveProperty('customer_id')
  })
})
```

**✅ النتيجة:** نظام مختبَر ومضمون الجودة

---

## 📚 الخطة الكاملة - ملخص سريع

### **الأسابيع 1-2: الأساسيات + Blade**
- البنية التحتية والإعدادات
- المحاسبة الأساسية (شجرة حسابات، قيود)
- العملاء والموردين
- التقارير المالية الأساسية

### **الأسابيع 3-4: المبيعات والمخزون**
- نظام المنتجات والمستودعات
- فواتير المبيعات والمشتريات
- إدارة المخزون وحركاته
- نظام الإنتاج (BOM)

### **الأسابيع 5-6: HR والتقارير**
- نظام الموارد البشرية
- الحضور والرواتب
- التقارير المتقدمة والتحليلية
- Dashboard و KPIs

### **الأسابيع 7-8: التحول لـ Vue.js**
- إعداد Inertia.js + Vue 3
- تحويل جميع الصفحات لـ Vue
- Pinia Stores
- Composables قابلة لإعادة الاستخدام
- Real-time notifications

### **الأسابيع 9-10: الاحتراف والنشر**
- Testing شامل
- Security hardening
- Performance optimization
- Deployment

---

## 📋 قائمة الدروس الكاملة (60 درساً)

| اليوم | الدرس | الموضوع | المخرجات |
|------|------|---------|----------|
| 1 | 1 | تثبيت وإعداد المشروع | مشروع Laravel جاهز |
| 2 | 2 | نظام الشركات والفروع | Multi-tenant ready |
| 3 | 3 | السنوات المالية | إدارة فترات محاسبية |
| 4 | 4 | شجرة الحسابات | دليل حسابات كامل |
| 5 | 5 | مراكز التكلفة | نظام تتبع تكاليف |
| 6 | 6 | العملات والضرائب | نظام متعدد العملات |
| 7 | 7 | الصلاحيات والأدوار | نظام Permissions |
| 8 | 8 | القيود اليومية | محرك محاسبي |
| 9 | 9 | أرصدة الحسابات | ميزان مراجعة |
| 10 | 10 | سندات القبض والصرف | إدارة نقدية |
| 11-12 | 11-12 | العملاء والموردين | CRM/SRM |
| 13-14 | 13-14 | التقارير المالية | دفاتر محاسبية |
| 15-16 | 15-16 | نظام المنتجات | كتالوج منتجات |
| 17-18 | 17-18 | فواتير المبيعات | نظام مبيعات |
| 19-20 | 19-20 | فواتير المشتريات | نظام مشتريات |
| 21 | 21 | عروض الأسعار | Sales pipeline |
| 22-23 | 22-23 | إدارة المستودعات | نظام مخزون |
| 24 | 24 | تقييم المخزون | FIFO/Weighted Avg |
| 25-26 | 25-26 | الإنتاج والتصنيع | نظام BOM |
| 27-28 | 27-28 | تقارير المخزون | تقارير شاملة |
| 29-30 | 29-30 | إدارة الموظفين | نظام HR |
| 31-32 | 31-32 | الحضور والإجازات | تتبع حضور |
| 33-34 | 33-34 | الرواتب والبدلات | نظام رواتب |
| 35 | 35 | القروض والسلف | إدارة قروض |
| 36-37 | 36-37 | القوائم المالية | Income/Balance/Cash Flow |
| 38-39 | 38-39 | Dashboard & KPIs | لوحة تحكم |
| 40-41 | 40-41 | التقارير التحليلية | تحليلات متقدمة |
| 42 | 42 | تصدير التقارير | PDF/Excel |
| 43-44 | 43-44 | إعداد Vue.js | Inertia + Vue |
| 45-46 | 45-46 | Layout Components | واجهة Vue |
| 47-48 | 47-48 | تحويل المحاسبة | وحدة محاسبة Vue |
| 49-50 | 49-50 | Pinia Stores | إدارة حالة |
| 51-52 | 51-52 | تحويل المبيعات | فواتير Vue |
| 53-54 | 53-54 | Composables | كود قابل لإعادة استخدام |
| 55-56 | 55-56 | Dashboard Vue | لوحة تفاعلية |
| 57-58 | 57-58 | Real-time | إشعارات حية |
| 59-60 | 59-60 | Testing | اختبارات شاملة |

---

## 🎯 الخطوات التالية

**هل أنت مستعد؟ دعنا نبدأ!**

أخبرني:
1. **هل لديك خبرة سابقة في Laravel؟** (مبتدئ/متوسط/متقدم)
2. **هل البيئة جاهزة؟** (PHP 8.2+, Composer, MySQL)
3. **متى تريد البدء؟** (اليوم/غداً/الأسبوع القادم)

**بمجرد الرد، سأعطيك:**
✅ الدرس الأول كاملاً بالتفصيل الممل
✅ الكود الكامل للنسخ واللصق
✅ شرح كل سطر
✅ التمرين العملي
✅ المشاكل المتوقعة وحلولها

**جاهز لتبدأ رحلة الـ 60 يوماً؟** 🚀

---

## 💡 نصائح مهمة للنجاح

1. **الالتزام اليومي**: 3-6 ساعات يومياً بدون استثناء
2. **التطبيق العملي**: لا تقرأ فقط، اكتب الكود بنفسك
3. **حل التمارين**: كل درس له تمرين إجباري
4. **Git Commits**: احفظ تقدمك يومياً على GitHub
5. **المراجعة**: راجع كل أسبوع ما تعلمته
6. **الأسئلة**: اسأل فوراً عند أي التباس
7. **التوثيق**: وثّق ما تتعلمه بملاحظاتك الخاصة
8. **المجتمع**: انضم لمجموعات Laravel بالعربي

**تذكر:** 
- أول أسبوعين صعبان، بعدها يصير سهل
- الممارسة اليومية أهم من الساعات الطويلة
- الأخطاء جزء من التعلم، لا تخف منها
- كل محترف كان مبتدئ يوماً ما

**هل أنت جاهز لنبدأ الدرس الأول الآن؟** 💪
                
    