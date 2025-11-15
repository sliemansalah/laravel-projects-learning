<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public static function createFromString($tagString)
    {
        $tags = collect(explode(',', $tagString))->map(function($tag) {
            $tag = trim($tag);
            return self::firstOrCreate([
                'name' => $tag,
                'slug' => Str::slug($tag)
            ]);
        });

        return $tags->pluck('id')->toArray();
    }
}
