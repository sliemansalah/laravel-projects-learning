<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'published',
        'category_id',
        'views'
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('approved', true);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getTagsStringAttribute()
    {
        return $this->tags->pluck('name')->implode(', ');
    }

    public function relatedPosts($limit = 3)
{
    return Post::where('id', '!=', $this->id)
        ->where('category_id', $this->category_id)
        ->where('published', true)
        ->latest()
        ->take($limit)
        ->get();
}




}
