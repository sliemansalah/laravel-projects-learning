<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('content', 'LIKE', "%{$search}%");
        }

        $posts = $query->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    // عرض صفحة إضافة مقال جديد
    public function create()
    {
        return view('posts.create');
    }

    // حفظ المقال الجديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($validated);

        return redirect()->route('posts.index')
            ->with('success', 'تم إضافة المقال بنجاح!');
    }

    // عرض مقال واحد
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    // عرض صفحة تعديل المقال
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    // تحديث المقال
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('posts.index')
            ->with('success', 'تم تحديث المقال بنجاح!');
    }

    // حذف المقال
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'تم حذف المقال بنجاح!');
    }
}
