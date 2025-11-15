<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
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
    public function archive()
{
    $archives = Post::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
        ->groupBy('year', 'month')
        ->orderByDesc('year')
        ->orderByDesc('month')
        ->get();

    return view('posts.archive', compact('archives'));
}

public function byDate($year, $month)
{
    $posts = Post::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->latest()
        ->paginate(10);

    $date = \Carbon\Carbon::createFromDate($year, $month, 1);

    return view('posts.by-date', compact('posts', 'date'));
}
public function toggleBookmark(Post $post)
{
    $bookmarks = session()->get('bookmarks', []);

    if (in_array($post->id, $bookmarks)) {
        // إزالة من المفضلة
        $bookmarks = array_diff($bookmarks, [$post->id]);
        $message = 'تم الإزالة من المفضلة';
    } else {
        // إضافة للمفضلة
        $bookmarks[] = $post->id;
        $message = 'تم الإضافة للمفضلة';
    }

    session()->put('bookmarks', $bookmarks);

    return back()->with('success', $message);
}

public function bookmarks()
{
    $bookmarkIds = session()->get('bookmarks', []);
    $posts = Post::whereIn('id', $bookmarkIds)->latest()->paginate(10);

    return view('posts.bookmarks', compact('posts'));
}

public function statistics()
{
    $stats = [
        'total_posts' => Post::count(),
        'published_posts' => Post::where('published', true)->count(),
        'draft_posts' => Post::where('published', false)->count(),
        'total_views' => Post::sum('views'),
        'total_comments' => Comment::count(),
        'approved_comments' => Comment::where('approved', true)->count(),
        'pending_comments' => Comment::where('approved', false)->count(),
        'total_categories' => Category::count(),
        'total_tags' => Tag::count(),
        'most_viewed' => Post::orderByDesc('views')->take(5)->get(),
        'recent_posts' => Post::latest()->take(5)->get(),
        'posts_this_month' => Post::whereMonth('created_at', now()->month)->count(),
        'views_this_month' => Post::whereMonth('created_at', now()->month)->sum('views'),
    ];

    return view('posts.statistics', compact('stats'));
}


}
