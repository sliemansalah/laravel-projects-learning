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
                     @if($post->tags->count() > 0)
                        <div class="mb-3">
                            <strong>الوسوم:</strong>
                            @foreach($post->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    <small class="text-muted">👁️ {{ $post->views }} مشاهدة</small>
                    <small class="text-muted">| {{ $post->created_at->format('Y-m-d') }}</small>
                </div>

                <div class="content mt-4">
                    {!! $post->content !!}
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">رجوع</a>
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
