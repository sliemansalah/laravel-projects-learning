<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Surah;
use App\Models\Ayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuranController extends Controller
{
    /**
     * Get all surahs
     */
    public function getSurahs(Request $request): JsonResponse
    {
        $surahs = Surah::all();

        // If user is authenticated, include progress
        if ($request->user()) {
            $surahs->each(function ($surah) use ($request) {
                $surah->progress_percentage = $surah->calculateProgress($request->user()->id);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $surahs
        ]);
    }

    /**
     * Get single surah with ayahs
     */
    public function getSurah(Request $request, int $surahId): JsonResponse
    {
        $surah = Surah::with(['ayahs.words'])->findOrFail($surahId);

        // Include user progress if authenticated
        if ($request->user()) {
            $userId = $request->user()->id;

            $surah->ayahs->each(function ($ayah) use ($userId) {
                $ayah->user_progress = $ayah->getUserProgress($userId);
                $ayah->words->each(function ($word) use ($userId) {
                    $word->user_status = $word->getUserStatus($userId);
                });
            });

            $surah->progress_percentage = $surah->calculateProgress($userId);
        }

        return response()->json([
            'success' => true,
            'data' => $surah
        ]);
    }

    /**
     * Get specific ayah with details
     */
    public function getAyah(Request $request, int $ayahId): JsonResponse
    {
        $ayah = Ayah::with(['surah', 'words'])->findOrFail($ayahId);

        // Include user progress if authenticated
        if ($request->user()) {
            $userId = $request->user()->id;
            $ayah->user_progress = $ayah->getUserProgress($userId);

            $ayah->words->each(function ($word) use ($userId) {
                $word->user_status = $word->getUserStatus($userId);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $ayah
        ]);
    }

    /**
     * Get ayahs for a specific surah
     */
    public function getSurahAyahs(Request $request, int $surahId): JsonResponse
    {
        $ayahs = Ayah::where('surah_id', $surahId)
            ->with('words')
            ->orderBy('number')
            ->get();

        // Include user progress if authenticated
        if ($request->user()) {
            $userId = $request->user()->id;

            $ayahs->each(function ($ayah) use ($userId) {
                $ayah->user_progress = $ayah->getUserProgress($userId);
                $ayah->words->each(function ($word) use ($userId) {
                    $word->user_status = $word->getUserStatus($userId);
                });
            });
        }

        return response()->json([
            'success' => true,
            'data' => $ayahs
        ]);
    }

    /**
     * Search in Quran
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');

        $results = Ayah::where('text_arabic', 'LIKE', "%{$query}%")
            ->orWhere('translation_ar', 'LIKE', "%{$query}%")
            ->orWhere('translation_en', 'LIKE', "%{$query}%")
            ->with('surah')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
