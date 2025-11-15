@extends('layouts.app')

@section('title', 'تعديل: ' . $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">تعديل المقال</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">عنوان المقال</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $post->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المحتوى</label>
                        <textarea name="content" rows="8" class="form-control @error('content') is-invalid @enderror"
                                  required>{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الفئة</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- اختر الفئة --</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $post->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>



                    @if($post->image)
                    <div class="mb-3">
                        <label class="form-label">الصورة الحالية</label>
                        <div>
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"
                                 class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                               accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="published" value="1" class="form-check-input"
                               id="published" {{ old('published', $post->published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="published">نشر المقال</label>
                    </div>
                           <div class="mb-3">
                        <label class="form-label">الوسوم (افصل بينها بفاصلة)</label>
                        <input type="text" name="tags" class="form-control @error('tags') is-invalid @enderror"
                            value="{{ old('tags', $post->tags_string ?? '') }}"
                            placeholder="مثال: برمجة, لارافيل, php">
                        <small class="text-muted">افصل بين الوسوم بفاصلة (,)</small>
                        @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">تحديث المقال</button>
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: 'textarea[name="content"]',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            language: 'ar',
            directionality: 'rtl',
            height: 400,
            menubar: false,
            branding: false,
            setup: function(editor) {
                // إزالة خاصية required من textarea عند تحميل المحرر
                editor.on('init', function() {
                    var textarea = document.querySelector('textarea[name="content"]');
                    if (textarea) {
                        textarea.removeAttribute('required');
                    }
                });

                // التحقق من المحتوى عند إرسال النموذج
                editor.on('submit', function() {
                    editor.save(); // حفظ المحتوى في textarea
                });
            }
        });

        // التحقق اليدوي عند إرسال النموذج
        document.querySelector('form').addEventListener('submit', function(e) {
            var content = tinymce.get('mce_0') ? tinymce.get('mce_0').getContent() : '';

            if (!content || content.trim() === '') {
                e.preventDefault();
                alert('يرجى كتابة محتوى المقال');
                return false;
            }

            // تحديث textarea قبل الإرسال
            tinymce.triggerSave();
        });
    });
</script>
@endpush
