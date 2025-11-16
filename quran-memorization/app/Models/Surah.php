<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surah extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name_arabic',
        'name_english',
        'name_transliteration',
        'total_ayahs',
        'revelation_type',
        'revelation_order'
    ];

    protected $casts = [
        'number' => 'integer',
        'total_ayahs' => 'integer',
        'revelation_order' => 'integer',
    ];

    /**
     * Get all ayahs for this surah
     */
    public function ayahs(): HasMany
    {
        return $this->hasMany(Ayah::class)->orderBy('number');
    }

    /**
     * Get user's memorization progress for this surah
     */
    public function userProgress($userId)
    {
        return $this->ayahs()
            ->with(['memorizationProgress' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get();
    }

    /**
     * Calculate memorization percentage for a user
     */
    public function calculateProgress($userId): float
    {
        $totalWords = $this->ayahs()
            ->withCount('words')
            ->get()
            ->sum('words_count');

        if ($totalWords === 0) {
            return 0;
        }

        $memorizedWords = MemorizationProgress::where('user_id', $userId)
            ->whereHas('word.ayah', function ($query) {
                $query->where('surah_id', $this->id);
            })
            ->where('status', 'mastered')
            ->count();

        return ($memorizedWords / $totalWords) * 100;
    }
}
