@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <article>
        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="post-image">
        @endif

        <h1 style="color: #667eea; margin-bottom: 1rem;">{{ $post->title }}</h1>

        <div style="color: #999; margin-bottom: 2rem;">
            <small>تم النشر: {{ $post->created_at->format('Y-m-d') }}</small>
        </div>

        <div class="post-content">
            {!! nl2br(e($post->content)) !!}
        </div>

        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #eee;">
            <a href="{{ route('posts.index') }}" class="btn">← العودة للمقالات</a>
            <a href="{{ route('posts.edit', $post) }}" class="btn">تعديل المقال</a>
        </div>
    </article>
@endsection
