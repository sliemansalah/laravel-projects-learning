<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('category');

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
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    // حفظ المقال الجديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $validated['published'] = $request->has('published');

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
        $post->load('category');
        return view('posts.show', compact('post'));
    }

    // عرض صفحة تعديل المقال
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    // تحديث المقال
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $validated['published'] = $request->has('published');

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
        // حذف الصورة من التخزين إن وجدت
        if ($post->image) {
            \Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'تم حذف المقال بنجاح!');
    }
}
