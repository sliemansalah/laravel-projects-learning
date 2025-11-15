@extends('layouts.app')

@section('title', 'جميع المقالات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>جميع المقالات</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary">+ مقال جديد</a>
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
                    <h5 class="card-title">{{ $post->title }}</h5>
                    <p class="card-text">{{ Str::limit($post->content, 150) }}</p>
                    <p class="text-muted small">
                        <span class="badge {{ $post->published ? 'bg-success' : 'bg-secondary' }}">
                            {{ $post->published ? 'منشور' : 'مسودة' }}
                        </span>
                        | {{ $post->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-info">قراءة</a>
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning">تعديل</a>
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
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
        لا توجد مقالات حالياً. <a href="{{ route('posts.create') }}">أضف مقالك الأول!</a>
    </div>
@endif
@endsection
