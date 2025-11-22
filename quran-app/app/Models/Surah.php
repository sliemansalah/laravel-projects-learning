<?php
// app/Models/Surah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function ayahs(): HasMany
    {
        return $this->hasMany(Ayah::class);
    }

    public function userHifz(): HasMany
    {
        return $this->hasMany(UserHifz::class);
    }
}
