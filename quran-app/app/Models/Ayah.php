<?php
// app/Models/Ayah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = ['ayah_number', 'text_arabic'];

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    // Accessors for compatibility with frontend
    protected function ayahNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->number,
        );
    }

    protected function textArabic(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->text,
        );
    }
}
