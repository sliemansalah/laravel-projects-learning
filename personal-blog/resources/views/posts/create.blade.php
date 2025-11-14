@extends('layouts.app')

@section('title', 'مقال جديد')

@section('content')
    <h2>✍️ كتابة مقال جديد</h2>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title">عنوان المقال *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>
            @error('title')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="excerpt">الملخص</label>
            <textarea name="excerpt" id="excerpt" rows="3">{{ old('excerpt') }}</textarea>
            @error('excerpt')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="content">المحتوى *</label>
            <textarea name="content" id="content" required>{{ old('content') }}</textarea>
            @error('content')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">صورة المقال</label>
            <input type="file" name="image" id="image" accept="image/*">
            @error('image')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="published" id="published" value="1" {{ old('published') ? 'checked' : '' }}>
                <label for="published" style="margin: 0;">نشر المقال مباشرة</label>
            </div>
        </div>

        <button type="submit" class="btn">نشر المقال</button>
        <a href="{{ route('posts.index') }}" class="btn" style="background: #6c757d;">إلغاء</a>
    </form>
@endsection
