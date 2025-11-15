@extends('layouts.app')

@section('title', 'التعليقات المعلقة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        💬 التعليقات المعلقة
        <span class="badge bg-warning text-dark">{{ $comments->total() }}</span>
    </h1>
    <a href="{{ route('posts.index') }}" class="btn btn-secondary">عودة للرئيسية</a>
</div>

@if($comments->count() > 0)
    <div class="row">
        @foreach($comments as $comment)
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $comment->name }}</strong>
                        <small class="text-muted">({{ $comment->email }})</small>
                    </div>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <div class="card-body">
                    <h6 class="text-muted mb-2">
                        على المقال:
                        <a href="{{ route('posts.show', $comment->post) }}" target="_blank">
                            {{ $comment->post->title }}
                        </a>
                    </h6>
                    <p class="mb-0">{{ $comment->comment }}</p>
                </div>
                <div class="card-footer bg-transparent">
                    <form action="{{ route('comments.approve', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            ✓ الموافقة
                        </button>
                    </form>

                    <form action="{{ route('comments.reject', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('هل أنت متأكد من رفض هذا التعليق؟')">
                            ✗ رفض
                        </button>
                    </form>

                    <a href="{{ route('posts.show', $comment->post) }}" class="btn btn-info btn-sm" target="_blank">
                        👁️ عرض المقال
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $comments->links() }}
    </div>
@else
    <div class="alert alert-success">
        <h5>🎉 لا توجد تعليقات معلقة</h5>
        <p>جميع التعليقات تم مراجعتها!</p>
    </div>
@endif
@endsection

