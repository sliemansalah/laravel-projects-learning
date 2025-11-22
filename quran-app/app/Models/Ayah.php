<?php
// app/Models/Ayah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ayah extends Model
{
    protected $fillable = [
        'surah_id',
        'number',
        'text',
        'transliteration',
        'translation',
        'page_number',
        'line_number',
        'juz_number',
        'hizb',
        'quarter'
    ];

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }
}
