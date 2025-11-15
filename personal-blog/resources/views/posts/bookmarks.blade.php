@extends('layouts.app')

@section('title', 'المقالات المحفوظة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        ⭐ المقالات المحفوظة
        <span class="badge bg-warning">{{ $posts->total() }}</span>
    </h1>
    <a href="{{ route('posts.index') }}" class="btn btn-secondary">العودة للرئيسية</a>
</div>

@if($posts->count() > 0)
    <div class="row">
        @foreach($posts as $post)
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                @endif
                <div class="card-body">
                    @if($post->category)
                    <span class="badge bg-info">{{ $post->category->name }}</span>
                    @endif
                    <small>👁️ {{ $post->views }}</small>

                    <h5 class="card-title mt-2">{{ $post->title }}</h5>
                    <p class="card-text">{!! Str::limit(strip_tags($post->content), 150) !!}</p>
                    <p class="text-muted small">
                        <span class="badge {{ $post->published ? 'bg-success' : 'bg-secondary' }}">
                            {{ $post->published ? 'منشور' : 'مسودة' }}
                        </span>
                        | {{ $post->created_at->format('Y-m-d') }}
                    </p>

                    @if($post->tags->count() > 0)
                    <div class="mt-2">
                        @foreach($post->tags->take(3) as $tag)
                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info">قراءة</a>

                    {{-- زر إزالة من المفضلة --}}
                    <form action="{{ route('posts.bookmark', $post) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            ★ إزالة من المفضلة
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
@else
    <div class="alert alert-info">
        <h5>📚 لا توجد مقالات محفوظة بعد</h5>
        <p>ابدأ بحفظ المقالات المفضلة لديك لتسهيل الوصول إليها لاحقاً!</p>
        <a href="{{ route('posts.index') }}" class="btn btn-primary">تصفح المقالات</a>
    </div>
@endif
@endsection
