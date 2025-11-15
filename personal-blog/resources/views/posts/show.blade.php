@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}">
            @endif

            <div class="card-body">
                <h1 class="card-title">{{ $post->title }}</h1>

                <div class="mb-3">
                    @if($post->category)
                    <span class="badge bg-info">{{ $post->category->name }}</span>
                    @endif
                    <span class="badge {{ $post->published ? 'bg-success' : 'bg-secondary' }}">
                        {{ $post->published ? 'منشور' : 'مسودة' }}
                    </span>
                    <small class="text-muted">👁️ {{ $post->views }} مشاهدة</small>
                    <small class="text-muted">| {{ $post->created_at->format('Y-m-d') }}</small>
                </div>

                @if($post->tags->count() > 0)
                <div class="mb-3">
                    <strong>الوسوم:</strong>
                    @foreach($post->tags as $tag)
                        <span class="badge bg-secondary">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif

                <hr>

                <div class="content mt-4">
                    {!! $post->content !!}
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">رجوع</a>
                <a href="{{ route('posts.archive') }}" class="btn btn-info">📅 الأرشيف</a>
                <form action="{{ route('posts.bookmark', $post) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn {{ in_array($post->id, session()->get('bookmarks', [])) ? 'btn-warning' : 'btn-outline-warning' }}">
                        {{ in_array($post->id, session()->get('bookmarks', [])) ? '★ محفوظ' : '☆ حفظ' }}
                    </button>
                </form>
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">تعديل</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                </form>
            </div>
        </div>

        {{-- المقالات ذات الصلة --}}
        @if($post->relatedPosts()->count() > 0)
        <div class="card mt-4 related-posts">
            <div class="card-header">
                <h5>📚 مقالات ذات صلة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($post->relatedPosts() as $related)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            @if($related->image)
                            <img src="{{ asset('storage/' . $related->image) }}"
                                 class="card-img-top"
                                 alt="{{ $related->title }}"
                                 style="height: 150px; object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('posts.show', $related) }}" class="text-decoration-none">
                                        {{ Str::limit($related->title, 50) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    👁️ {{ $related->views }} | {{ $related->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="{{ route('posts.show', $related) }}" class="btn btn-sm btn-primary w-100">
                                    قراءة المزيد
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation عند النقر على زر المفضلة
    document.querySelectorAll('form[action*="bookmark"] button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.classList.add('saved');
            setTimeout(() => {
                this.classList.remove('saved');
            }, 600);
        });
    });
});
</script>
@endpush
