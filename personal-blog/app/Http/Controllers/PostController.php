<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Display all published posts
    public function index()
    {
        $posts = Post::published()
            ->latest()
            ->paginate(6);

        return view('posts.index', compact('posts'));
    }

    // Show single post
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        return view('posts.show', compact('post'));
    }

    // Show create form
    public function create()
    {
        return view('posts.create');
    }

    // Store new post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('posts', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);

        Post::create($validated);

        return redirect()->route('posts.index')
            ->with('success', 'تم نشر المقال بنجاح!');
    }

    // Show edit form
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    // Update post
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image) {
                \Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')
                ->store('posts', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $post->update($validated);

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'تم تحديث المقال بنجاح!');
    }

    // Delete post
    public function destroy(Post $post)
    {
        if ($post->image) {
            \Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'تم حذف المقال بنجاح!');
    }
}
