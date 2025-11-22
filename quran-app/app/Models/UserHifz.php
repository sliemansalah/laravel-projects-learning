<?php
// app/Models/UserHifz.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHifz extends Model
{
    protected $table = 'user_hifz';
    protected $fillable = [
        'user_id',
        'surah_id',
        'start_ayah',
        'end_ayah',
        'status',
        'memorized_count',
        'reviewed_count',
        'start_date',
        'completion_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'completion_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }
}

// app/Models/HifzPage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzPage extends Model
{
    protected $fillable = [
        'user_id',
        'page_number',
        'status',
        'review_count',
        'memorized_date',
        'last_review_date',
        'notes'
    ];

    protected $casts = [
        'memorized_date' => 'date',
        'last_review_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
