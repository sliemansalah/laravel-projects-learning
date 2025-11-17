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
