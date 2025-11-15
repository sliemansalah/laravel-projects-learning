<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
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
    // 1. التحقق من البيانات
    $validated = $request->validate([
        'title' => 'required|max:255',
        'content' => 'required',
        'image' => 'nullable|image|max:2048',
        'published' => 'boolean',
        'category_id' => 'nullable|exists:categories,id',
        'tags' => 'nullable|string'
    ]);

    // 2. معالجة الصورة إذا وجدت
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    }

    // 3. معالجة checkbox المنشور
    $validated['published'] = $request->has('published');

    // 4. إنشاء المقال (الآن $post معرّف)
    $post = Post::create([
        'title' => $validated['title'],
        'content' => $validated['content'],
        'image' => $validated['image'] ?? null,
        'published' => $validated['published'],
        'category_id' => $validated['category_id'] ?? null,
    ]);

    // 5. ربط الوسوم بعد إنشاء المقال
    if ($request->filled('tags')) {
        $tagIds = Tag::createFromString($request->tags);
        $post->tags()->attach($tagIds);
    }

    // 6. الرجوع مع رسالة نجاح
    return redirect()->route('posts.index')->with('success', 'تم إضافة المقال بنجاح');
}
    // عرض مقال واحد
    public function show(Post $post)
    {
        $post->incrementViews();
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
            'category_id' => 'nullable|exists:categories,id',
                    'tags' => 'nullable|string'

        ]);

        $validated['published'] = $request->has('published');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);
         // تحديث الوسوم
    if ($request->has('tags')) {
        $tagIds = Tag::createFromString($request->tags);
        $post->tags()->sync($tagIds);
    } else {
        $post->tags()->detach();
    }

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
