<?php
// app/Http/Controllers/Api/AyahController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ayah;
use App\Models\Surah;
use Illuminate\Http\Request;

class AyahController extends Controller
{
    // الحصول على آية محددة
    public function show($id)
    {
        $ayah = Ayah::with('surah')->find($id);

        if (!$ayah) {
            return response()->json([
                'success' => false,
                'message' => 'الآية غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ayah
        ]);
    }

    // الحصول على آيات حسب الصفحة
    public function getByPage($pageNumber)
    {
        $ayahs = Ayah::where('page_number', $pageNumber)
            ->orderBy('number')
            ->get();

        if ($ayahs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد آيات لهذه الصفحة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ayahs
        ]);
    }

    // الحصول على آيات حسب الجزء
    public function getByJuz($juzNumber)
    {
        $ayahs = Ayah::where('juz_number', $juzNumber)
            ->with('surah')
            ->orderBy('number')
            ->get();

        if ($ayahs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد آيات في هذا الجزء'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ayahs
        ]);
    }

    // البحث عن آيات
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'الرجاء إدخال نص البحث'
            ], 400);
        }

        $ayahs = Ayah::where('text', 'like', "%$query%")
            ->with('surah')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ayahs
        ]);
    }

    // الحصول على آيات من سورة معينة
    public function getBySurah($surahId, Request $request)
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
}
