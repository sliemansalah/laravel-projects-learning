@extends('layouts.app')

@section('title', 'تعديل: ' . $post->title)

@section('content')
    <h2>✏️ تعديل المقال</h2>

    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">عنوان المقال *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required>
            @error('title')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="excerpt">الملخص</label>
            <textarea name="excerpt" id="excerpt" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="content">المحتوى *</label>
            <textarea name="content" id="content" required>{{ old('content', $post->content) }}</textarea>
            @error('content')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">صورة المقال</label>
            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}"
                     style="max-width: 200px; display: block; margin-bottom: 1rem; border-radius: 8px;">
            @endif
            <input type="file" name="image" id="image" accept="image/*">
            @error('image')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="published" id="published" value="1"
                    {{ old('published', $post->published) ? 'checked' : '' }}>
                <label for="published" style="margin: 0;">نشر المقال</label>
            </div>
        </div>

        <button type="submit" class="btn">حفظ التعديلات</button>
        <a href="{{ route('posts.show', $post->slug) }}" class="btn" style="background: #6c757d;">إلغاء</a>
    </form>
@endsection
