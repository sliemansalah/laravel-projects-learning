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
            'website' => 'nullable|string',
            'branches' => 'required|array|min:1',
            'branches.*.name' => 'required|string|max:255',
            'branches.*.code' => 'required|string|max:50',
            'branches.*.address' => 'required|string',
            'branches.*.city' => 'required|string',
            'branches.*.phone' => 'required|string',
            'branches.*.manager_name' => 'nullable|string|max:255',
            'branches.*.manager_phone' => 'nullable|string',
            'branches.*.is_main' => 'required|boolean',
        ]);

        $validated['country'] = 'SA';
        $validated['is_active'] = true;

        // حفظ الشركة
        $company = Company::create([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'],
            'tax_number' => $validated['tax_number'],
            'commercial_register' => $validated['commercial_register'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'website' => $validated['website'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        // حفظ الفروع
        foreach ($validated['branches'] as $branchData) {
            $company->branches()->create([
                'name' => $branchData['name'],
                'code' => $branchData['code'],
                'address' => $branchData['address'],
                'city' => $branchData['city'],
                'phone' => $branchData['phone'],
                'manager_name' => $branchData['manager_name'] ?? null,
                'manager_phone' => $branchData['manager_phone'] ?? null,
                'is_main' => $branchData['is_main'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('companies.index')
            ->with('success', 'تم إضافة الشركة بنجاح مع ' . count($validated['branches']) . ' فرع');
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
