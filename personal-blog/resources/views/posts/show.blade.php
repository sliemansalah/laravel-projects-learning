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

<div class="card mt-4">
    <div class="card-header">
        <h5>التعليقات ({{ $post->approvedComments->count() }})</h5>
    </div>
    <div class="card-body">
        @foreach($post->approvedComments as $comment)
        <div class="mb-3 pb-3 border-bottom">
            <strong>{{ $comment->name }}</strong>
            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
            <p class="mt-2 mb-0">{{ $comment->comment }}</p>
        </div>
        @endforeach

        @if($post->approvedComments->count() == 0)
            <p class="text-muted">لا توجد تعليقات بعد. كن أول من يعلق!</p>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5>إضافة تعليق</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('comments.store', $post) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">الاسم</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">التعليق</label>
                <textarea name="comment" rows="4" class="form-control @error('comment') is-invalid @enderror"
                          required>{{ old('comment') }}</textarea>
                @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">إضافة تعليق</button>
        </form>
    </div>
</div>

@endsection
