<?php
// app/Models/Surah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Surah extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'number',
        'ayah_count',
        'revelation_place',
        'juz_number',
        'description'
    ];

    protected $appends = ['name_arabic', 'name_english', 'verses_count'];

    public function ayahs(): HasMany
    {
        return $this->hasMany(Ayah::class);
    }

    public function userHifz(): HasMany
    {
        return $this->hasMany(UserHifz::class);
    }

    // Accessors for compatibility with frontend
    protected function nameArabic(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name,
        );
    }

    protected function nameEnglish(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name_en,
        );
    }

    protected function versesCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ayah_count,
        );
    }
}
