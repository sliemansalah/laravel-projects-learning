<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'ayah_id',
        'position',
        'text_arabic',
        'text_uthmani',
        'transliteration',
        'translation',
        'meaning'
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    /**
     * Get the ayah that owns this word
     */
    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }

    /**
     * Get memorization progress for this word
     */
    public function memorizationProgress(): HasMany
    {
        return $this->hasMany(MemorizationProgress::class);
    }

    /**
     * Get user's memorization status for this word
     */
    public function getUserStatus($userId)
    {
        return $this->memorizationProgress()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Check if word is memorized by user
     */
    public function isMemorizedBy($userId): bool
    {
        return $this->memorizationProgress()
            ->where('user_id', $userId)
            ->where('status', 'mastered')
            ->exists();
    }

    /**
     * Get next word in the ayah
     */
    public function nextWord()
    {
        return static::where('ayah_id', $this->ayah_id)
            ->where('position', '>', $this->position)
            ->orderBy('position')
            ->first();
    }

    /**
     * Get previous word in the ayah
     */
    public function previousWord()
    {
        return static::where('ayah_id', $this->ayah_id)
            ->where('position', '<', $this->position)
            ->orderBy('position', 'desc')
            ->first();
    }
}
