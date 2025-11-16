<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemorizationProgress;
use App\Models\ReviewSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'daily_goal' => 'sometimes|integer|min:1|max:100',
            'reminder_time' => 'sometimes|date_format:H:i',
            'notifications_enabled' => 'sometimes|boolean',
            'preferred_reciter' => 'sometimes|string',
            'dark_mode' => 'sometimes|boolean'
        ]);

        $user = $request->user();
        $user->update($request->only([
            'daily_goal',
            'reminder_time',
            'notifications_enabled',
            'preferred_reciter',
            'dark_mode'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعدادات بنجاح',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Get detailed user statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Get total sessions
        $totalSessions = ReviewSession::where('user_id', $userId)->count();

        // Get total time spent (in minutes)
        $totalTime = ReviewSession::where('user_id', $userId)
            ->sum('duration_seconds') / 60;

        // Calculate average accuracy
        $sessions = ReviewSession::where('user_id', $userId)->get();
        $totalWords = $sessions->sum(function ($session) {
            return $session->correct_words + $session->wrong_words;
        });
        $correctWords = $sessions->sum('correct_words');
        $averageAccuracy = $totalWords > 0 ? ($correctWords / $totalWords) * 100 : 0;

        // Get weekly progress
        $weeklyProgress = $this->getWeeklyProgress($userId);

        // Get daily average
        $daysActive = ReviewSession::where('user_id', $userId)
            ->distinct(DB::raw('DATE(completed_at)'))
            ->count();
        $wordsPerDay = $daysActive > 0 ?
            (MemorizationProgress::where('user_id', $userId)->count() / $daysActive) : 0;

        // Get mastered words by surah
        $progressBySurah = $this->getProgressBySurah($userId);

        return response()->json([
            'success' => true,
            'data' => [
                'total_sessions' => $totalSessions,
                'total_time_minutes' => round($totalTime, 2),
                'average_accuracy' => round($averageAccuracy, 2),
                'best_streak' => $request->user()->longest_streak,
                'current_streak' => $request->user()->current_streak,
                'words_per_day' => round($wordsPerDay, 2),
                'weekly_progress' => $weeklyProgress,
                'progress_by_surah' => $progressBySurah
            ]
        ]);
    }

    /**
     * Get user achievements
     */
    public function getAchievements(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $achievements = [];

        // Calculate achievements
        $masteredWords = MemorizationProgress::where('user_id', $userId)
            ->where('status', 'mastered')
            ->count();

        $totalReviews = MemorizationProgress::where('user_id', $userId)
            ->sum('review_count');

        $currentStreak = $request->user()->current_streak;

        // First Word
        if ($masteredWords >= 1) {
            $achievements[] = [
                'id' => 1,
                'title' => 'الكلمة الأولى',
                'description' => 'أتقنت أول كلمة',
                'icon' => '🌟',
                'earned' => true
            ];
        }

        // First Ayah
        if ($masteredWords >= 4) {
            $achievements[] = [
                'id' => 2,
                'title' => 'الآية الأولى',
                'description' => 'أتقنت آية كاملة',
                'icon' => '📖',
                'earned' => true
            ];
        }

        // First Surah
        if ($masteredWords >= 15) { // Assuming smallest surah
            $achievements[] = [
                'id' => 3,
                'title' => 'السورة الأولى',
                'description' => 'أتقنت سورة كاملة',
                'icon' => '🕌',
                'earned' => true
            ];
        }

        // Review Master
        if ($totalReviews >= 100) {
            $achievements[] = [
                'id' => 4,
                'title' => 'ملك المراجعة',
                'description' => 'أكملت 100 مراجعة',
                'icon' => '👑',
                'earned' => true
            ];
        }

        // Streak achievements
        if ($currentStreak >= 7) {
            $achievements[] = [
                'id' => 5,
                'title' => 'أسبوع من الحفظ',
                'description' => 'حافظت على سلسلة 7 أيام',
                'icon' => '🔥',
                'earned' => true
            ];
        }

        if ($currentStreak >= 30) {
            $achievements[] = [
                'id' => 6,
                'title' => 'شهر من الحفظ',
                'description' => 'حافظت على سلسلة 30 يوم',
                'icon' => '💎',
                'earned' => true
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $achievements
        ]);
    }

    /**
     * Get weekly progress data
     */
    private function getWeeklyProgress($userId): array
    {
        $weekDays = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
        $progress = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayName = $weekDays[$date->dayOfWeek];

            $wordsReviewed = ReviewSession::where('user_id', $userId)
                ->whereDate('completed_at', $date)
                ->sum(DB::raw('correct_words + wrong_words'));

            $progress[] = [
                'day' => $dayName,
                'date' => $date->format('Y-m-d'),
                'words' => $wordsReviewed
            ];
        }

        return $progress;
    }

    /**
     * Get progress by surah
     */
    private function getProgressBySurah($userId): array
    {
        $surahs = DB::table('memorization_progress')
            ->join('words', 'memorization_progress.word_id', '=', 'words.id')
            ->join('ayahs', 'words.ayah_id', '=', 'ayahs.id')
            ->join('surahs', 'ayahs.surah_id', '=', 'surahs.id')
            ->where('memorization_progress.user_id', $userId)
            ->select(
                'surahs.id',
                'surahs.name_arabic',
                DB::raw('COUNT(*) as total_words'),
                DB::raw("SUM(CASE WHEN memorization_progress.status = 'mastered' THEN 1 ELSE 0 END) as mastered_words")
            )
            ->groupBy('surahs.id', 'surahs.name_arabic')
            ->get();

        return $surahs->map(function ($surah) {
            return [
                'surah_id' => $surah->id,
                'surah_name' => $surah->name_arabic,
                'total_words' => $surah->total_words,
                'mastered_words' => $surah->mastered_words,
                'progress_percentage' => ($surah->mastered_words / $surah->total_words) * 100
            ];
        })->toArray();
    }
}
