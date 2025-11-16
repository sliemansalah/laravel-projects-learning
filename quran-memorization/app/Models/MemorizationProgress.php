<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MemorizationProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ayah_id',
        'word_id',
        'status',
        'strength',
        'review_count',
        'mistake_count',
        'last_reviewed_at',
        'next_review_at'
    ];

    protected $casts = [
        'strength' => 'integer',
        'review_count' => 'integer',
        'mistake_count' => 'integer',
        'last_reviewed_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    /**
     * Get the user that owns this progress
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ayah associated with this progress
     */
    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }

    /**
     * Get the word associated with this progress
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Calculate next review date based on strength
     */
    public function calculateNextReview(int $mistakes = 0): array
    {
        $intervals = [
            1 => 1,      // 1 day
            2 => 3,      // 3 days
            3 => 7,      // 1 week
            4 => 14,     // 2 weeks
            5 => 30,     // 1 month
            6 => 60,     // 2 months
            7 => 90,     // 3 months
            8 => 180,    // 6 months
            9 => 365,    // 1 year
            10 => null   // mastered
        ];

        $newStrength = $this->strength;

        // Adjust strength based on mistakes
        if ($mistakes === 0) {
            $newStrength = min(10, $newStrength + 1);
        } elseif ($mistakes > 2) {
            $newStrength = max(1, $newStrength - 2);
        } else {
            $newStrength = max(1, $newStrength - 1);
        }

        $days = $intervals[$newStrength];
        $nextReview = $days ? Carbon::now()->addDays($days) : null;

        return [
            'strength' => $newStrength,
            'next_review' => $nextReview,
            'status' => $newStrength >= 8 ? 'mastered' : ($newStrength >= 4 ? 'reviewing' : 'learning')
        ];
    }

    /**
     * Update progress after review
     */
    public function recordReview(int $mistakes = 0): void
    {
        $result = $this->calculateNextReview($mistakes);

        $this->update([
            'strength' => $result['strength'],
            'status' => $result['status'],
            'review_count' => $this->review_count + 1,
            'mistake_count' => $this->mistake_count + $mistakes,
            'last_reviewed_at' => now(),
            'next_review_at' => $result['next_review']
        ]);
    }

    /**
     * Check if review is due
     */
    public function isDue(): bool
    {
        if (!$this->next_review_at) {
            return false;
        }

        return $this->next_review_at->isPast();
    }

    /**
     * Scope for due reviews
     */
    public function scopeDue($query)
    {
        return $query->where('next_review_at', '<=', now())
            ->orWhereNull('next_review_at');
    }
}
