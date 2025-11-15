@extends('layouts.app')

@section('title', 'مقال جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">إضافة مقال جديد</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">عنوان المقال</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المحتوى</label>
                        <textarea name="content" rows="8" class="form-control @error('content') is-invalid @enderror"
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">صورة المقال</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                               accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="published" value="1" class="form-check-input"
                               id="published" {{ old('published') ? 'checked' : '' }}>
                        <label class="form-check-label" for="published">نشر المقال</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">حفظ المقال</button>
                        <a href="{{ route('posts.index') }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
