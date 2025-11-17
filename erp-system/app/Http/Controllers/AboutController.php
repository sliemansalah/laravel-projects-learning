<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $aboutInfo = [
            'system_name' => 'نظام ERP المتكامل',
            'goal' => 'تعلم لارافيل بشكل احترافي',
            'programmer' => 'سليمان ماجد صلاح',
        ];

        return view('about', compact('aboutInfo'));
    }
}
