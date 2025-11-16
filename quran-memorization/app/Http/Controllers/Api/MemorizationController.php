<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemorizationProgress;
use App\Models\Ayah;
use App\Models\Word;
use App\Models\ReviewSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemorizationController extends Controller
{
    /**
     * Start memorizing a new ayah
     */
    public function startAyah(Request $request): JsonResponse
    {
        $request->validate([
            'ayah_id' => 'required|exists:ayahs,id'
        ]);

        $ayah = Ayah::with('words')->findOrFail($request->ayah_id);
        $userId = $request->user()->id;

        DB::beginTransaction();
        try {
            // Create progress records for each word
            foreach ($ayah->words as $word) {
                MemorizationProgress::firstOrCreate([
                    'user_id' => $userId,
                    'ayah_id' => $ayah->id,
                    'word_id' => $word->id,
                ], [
                    'status' => 'learning',
                    'strength' => 1,
                    'next_review_at' => now()->addDay()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'بدأت حفظ الآية بنجاح',
                'data' => $ayah->load('words.memorizationProgress')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء بدء الحفظ'
            ], 500);
        }
    }

    /**
     * Submit word memorization result
     */
    public function submitWord(Request $request): JsonResponse
    {
        $request->validate([
            'word_id' => 'required|exists:words,id',
            'is_correct' => 'required|boolean'
        ]);

        $userId = $request->user()->id;
        $word = Word::findOrFail($request->word_id);

        $progress = MemorizationProgress::where('user_id', $userId)
            ->where('word_id', $word->id)
            ->firstOrFail();

        // Record the review
        $mistakes = $request->is_correct ? 0 : 1;
        $progress->recordReview($mistakes);

        // Update user points
        if ($request->is_correct) {
            $request->user()->increment('total_points', 10);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'progress' => $progress->fresh(),
                'next_word' => $word->nextWord(),
                'user_points' => $request->user()->total_points
            ]
        ]);
    }

    /**
     * Get today's review items
     */
    public function getTodayReview(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $dueItems = MemorizationProgress::where('user_id', $userId)
            ->due()
            ->with(['word.ayah.surah', 'ayah'])
            ->orderBy('next_review_at')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $dueItems,
                'total_due' => $dueItems->count(),
                'daily_goal' => $request->user()->daily_goal
            ]
        ]);
    }

    /**
     * Get user's overall progress
     */
    public function getProgress(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $stats = [
            'total_words' => MemorizationProgress::where('user_id', $userId)->count(),
            'mastered_words' => MemorizationProgress::where('user_id', $userId)
                ->where('status', 'mastered')
                ->count(),
            'reviewing_words' => MemorizationProgress::where('user_id', $userId)
                ->where('status', 'reviewing')
                ->count(),
            'learning_words' => MemorizationProgress::where('user_id', $userId)
                ->where('status', 'learning')
                ->count(),
            'due_reviews' => MemorizationProgress::where('user_id', $userId)
                ->due()
                ->count(),
            'total_reviews' => MemorizationProgress::where('user_id', $userId)
                ->sum('review_count'),
            'accuracy' => $this->calculateAccuracy($userId)
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Complete a review session
     */
    public function completeSession(Request $request): JsonResponse
    {
        $request->validate([
            'ayah_id' => 'required|exists:ayahs,id',
            'correct_words' => 'required|integer|min:0',
            'wrong_words' => 'required|integer|min:0',
            'duration_seconds' => 'required|integer|min:0'
        ]);

        $session = ReviewSession::create([
            'user_id' => $request->user()->id,
            'ayah_id' => $request->ayah_id,
            'correct_words' => $request->correct_words,
            'wrong_words' => $request->wrong_words,
            'duration_seconds' => $request->duration_seconds,
            'completed_at' => now()
        ]);

        // Update streak
        $this->updateStreak($request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم إكمال الجلسة بنجاح',
            'data' => $session
        ]);
    }

    /**
     * Calculate user accuracy
     */
    private function calculateAccuracy($userId): float
    {
        $total = MemorizationProgress::where('user_id', $userId)
            ->sum(DB::raw('review_count + mistake_count'));

        if ($total === 0) {
            return 100;
        }

        $mistakes = MemorizationProgress::where('user_id', $userId)
            ->sum('mistake_count');

        return round((($total - $mistakes) / $total) * 100, 2);
    }

    /**
     * Update user streak
     */
    private function updateStreak($user): void
    {
        $lastActivity = $user->last_activity_at;
        $now = now();

        if (!$lastActivity || $lastActivity->isYesterday()) {
            $user->increment('current_streak');
            if ($user->current_streak > $user->longest_streak) {
                $user->longest_streak = $user->current_streak;
            }
        } elseif (!$lastActivity->isToday()) {
            $user->current_streak = 1;
        }

        $user->last_activity_at = $now;
        $user->save();
    }
}
