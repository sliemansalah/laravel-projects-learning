<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'surah_id',
        'number',
        'text_arabic',
        'text_uthmani',
        'translation_en',
        'translation_ar',
        'tafseer',
        'audio_url'
    ];

    protected $casts = [
        'number' => 'integer',
    ];

    /**
     * Get the surah that owns this ayah
     */
    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    /**
     * Get all words in this ayah
     */
    public function words(): HasMany
    {
        return $this->hasMany(Word::class)->orderBy('position');
    }

    /**
     * Get memorization progress for this ayah
     */
    public function memorizationProgress(): HasMany
    {
        return $this->hasMany(MemorizationProgress::class);
    }

    /**
     * Get review sessions for this ayah
     */
    public function reviewSessions(): HasMany
    {
        return $this->hasMany(ReviewSession::class);
    }

    /**
     * Get user's progress for this specific ayah
     */
    public function getUserProgress($userId)
    {
        return $this->memorizationProgress()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Check if ayah is memorized by user
     */
    public function isMemorizedBy($userId): bool
    {
        return $this->memorizationProgress()
            ->where('user_id', $userId)
            ->where('status', 'mastered')
            ->exists();
    }
}
