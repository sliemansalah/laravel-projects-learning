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

    // عرض التعليقات المعلقة
    public function pending()
    {
        $comments = Comment::where('approved', false)
            ->with('post')
            ->latest()
            ->paginate(20);

        return view('comments.pending', compact('comments'));
    }

    // اعتماد التعليق
    public function approve(Comment $comment)
    {
        $comment->update(['approved' => true]);

        return back()->with('success', 'تمت الموافقة على التعليق بنجاح!');
    }

    // رفض/حذف التعليق
    public function reject(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'تم رفض وحذف التعليق!');
    }
}
