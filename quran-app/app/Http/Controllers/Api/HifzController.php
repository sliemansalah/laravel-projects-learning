<?php
// app/Http/Controllers/Api/HifzController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserHifz;
use App\Models\HifzPage;
use App\Models\Surah;
use Illuminate\Http\Request;

class HifzController extends Controller
{
    // بدء حفظ سورة جديدة
    public function startHifz(Request $request)
    {
        $validated = $request->validate([
            'surah_id' => 'required|exists:surahs,id',
            'start_ayah' => 'required|integer|min:1',
            'end_ayah' => 'required|integer|min:1'
        ]);

        $user = auth()->user();
        $surah = Surah::find($validated['surah_id']);

        // التحقق من أن الآيات صحيحة
        if ($validated['start_ayah'] > $validated['end_ayah'] || $validated['end_ayah'] > $surah->ayah_count) {
            return response()->json([
                'success' => false,
                'message' => 'نطاق الآيات غير صحيح'
            ], 422);
        }

        $userHifz = UserHifz::updateOrCreate(
            [
                'user_id' => $user->id,
                'surah_id' => $validated['surah_id']
            ],
            [
                'start_ayah' => $validated['start_ayah'],
                'end_ayah' => $validated['end_ayah'],
                'status' => 'in_progress',
                'start_date' => now()
            ]
        );

        // إعادة تحميل البيانات مع العلاقة
        $userHifz->load('surah');

        return response()->json([
            'success' => true,
            'message' => 'تم بدء الحفظ بنجاح',
            'data' => $userHifz
        ]);
    }

    // الحصول على حفظ المستخدم
    public function getUserHifz()
    {
        $user = auth()->user();
        $hifz = $user->hifz()->with('surah')->get();

        return response()->json([
            'success' => true,
            'data' => $hifz
        ]);
    }

    // تحديث حالة الحفظ
    public function updateHifzStatus(Request $request, $hifzId)
    {
        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,reviewing',
            'memorized_count' => 'sometimes|integer|min:0',
            'reviewed_count' => 'sometimes|integer|min:0'
        ]);

        $hifz = UserHifz::where('id', $hifzId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$hifz) {
            return response()->json([
                'success' => false,
                'message' => 'الحفظ غير موجود'
            ], 404);
        }

        // تحضير البيانات للتحديث
        $updateData = $validated;

        if ($validated['status'] === 'completed') {
            $updateData['completion_date'] = now();
        }

        // إذا تم تحديث reviewed_count، نحدث last_reviewed_at
        if (isset($validated['reviewed_count']) && $validated['reviewed_count'] > ($hifz->reviewed_count ?? 0)) {
            $updateData['last_reviewed_at'] = now();
        }

        // تحديث البيانات دفعة واحدة
        $hifz->update($updateData);

        // إعادة تحميل البيانات مع العلاقة
        $hifz->load('surah');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح',
            'data' => $hifz
        ]);
    }

    // حفظ صفحة
    public function markPageMemorized(Request $request)
    {
        $validated = $request->validate([
            'page_number' => 'required|integer|min:1|max:604'
        ]);

        $user = auth()->user();

        $page = HifzPage::updateOrCreate(
            [
                'user_id' => $user->id,
                'page_number' => $validated['page_number']
            ],
            [
                'status' => 'memorized',
                'memorized_date' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الصفحة بنجاح',
            'data' => $page
        ]);
    }

    // مراجعة صفحة
    public function reviewPage(Request $request)
    {
        $validated = $request->validate([
            'page_number' => 'required|integer|min:1|max:604'
        ]);

        $user = auth()->user();

        $page = HifzPage::where('user_id', $user->id)
            ->where('page_number', $validated['page_number'])
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'الصفحة غير موجودة'
            ], 404);
        }

        $page->update([
            'review_count' => $page->review_count + 1,
            'last_review_date' => now(),
            'status' => 'reviewing'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المراجعة',
            'data' => $page
        ]);
    }

    // الحصول على إحصائيات المستخدم
    public function getStats()
    {
        $user = auth()->user();

        $stats = [
            'total_hifz' => $user->hifz()->count(),
            'completed' => $user->hifz()->where('status', 'completed')->count(),
            'in_progress' => $user->hifz()->where('status', 'in_progress')->count(),
            'total_pages_memorized' => $user->pages()->where('status', 'memorized')->count(),
            'total_pages_reviewing' => $user->pages()->where('status', 'reviewing')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // الحصول على صفحات المستخدم
    public function getUserPages()
    {
        $user = auth()->user();
        $pages = $user->pages()->orderBy('page_number')->get();

        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }
}
