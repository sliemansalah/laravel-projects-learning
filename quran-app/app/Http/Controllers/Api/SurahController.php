<?php
// app/Http/Controllers/Api/SurahController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Surah;
use Illuminate\Http\Request;

class SurahController extends Controller
{
    // الحصول على جميع السور
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Surah::orderBy('number')->get()
        ]);
    }

    // الحصول على سورة محددة مع آياتها
    public function show($id)
    {
        $surah = Surah::with('ayahs')->find($id);

        if (!$surah) {
            return response()->json([
                'success' => false,
                'message' => 'السورة غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $surah
        ]);
    }

    // الحصول على السورة من خلال الرقم
    public function getByNumber($number)
    {
        $surah = Surah::where('number', $number)
            ->with('ayahs')
            ->first();

        if (!$surah) {
            return response()->json([
                'success' => false,
                'message' => 'السورة غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $surah
        ]);
    }

    // الحصول على آيات السورة
    public function ayahs($surahId)
    {
        $surah = Surah::find($surahId);

        if (!$surah) {
            return response()->json([
                'success' => false,
                'message' => 'السورة غير موجودة'
            ], 404);
        }

        $ayahs = $surah->ayahs()->orderBy('number')->get();

        return response()->json([
            'success' => true,
            'data' => $ayahs
        ]);
    }

    // البحث عن سور
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'الرجاء إدخال نص البحث'
            ], 400);
        }

        $surahs = Surah::where('name', 'like', "%$query%")
            ->orWhere('name_en', 'like', "%$query%")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $surahs
        ]);
    }
}
