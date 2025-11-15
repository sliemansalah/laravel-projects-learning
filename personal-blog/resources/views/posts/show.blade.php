@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            @if($post->category)
                <span class="badge bg-info">{{ $post->category->name }}</span>
            @endif
            <p class="text-muted">
                👁️ {{ $post->views }} مشاهدة
            </p>
            @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}">
            @endif
            <div class="card-body">
                <h1 class="card-title">{{ $post->title }}</h1>
                <p class="text-muted">
                    <span class="badge {{ $post->published ? 'bg-success' : 'bg-secondary' }}">
                        {{ $post->published ? 'منشور' : 'مسودة' }}
                    </span>
                    | نُشر {{ $post->created_at->format('Y-m-d') }}
                    @if($post->updated_at != $post->created_at)
                        | آخر تحديث {{ $post->updated_at->diffForHumans() }}
                    @endif
                </p>
                <hr>
                <div class="post-content">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">عودة للمقالات</a>
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">تعديل</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
