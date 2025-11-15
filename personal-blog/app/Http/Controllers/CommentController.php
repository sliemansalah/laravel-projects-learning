<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'comment' => 'required|min:3'
        ]);

        $post->comments()->create($validated);

        return back()->with('success', 'تم إضافة تعليقك بنجاح! سيظهر بعد الموافقة عليه.');
    }
}
