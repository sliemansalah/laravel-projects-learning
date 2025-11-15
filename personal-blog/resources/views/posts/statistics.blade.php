@extends('layouts.app')

@section('title', 'إحصائيات المدونة')

@section('content')
<h1 class="mb-4">📊 إحصائيات المدونة</h1>

{{-- الإحصائيات الرئيسية --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center text-white bg-primary h-100">
            <div class="card-body">
                <div class="display-4 mb-2">{{ $stats['total_posts'] }}</div>
                <h5 class="card-title">إجمالي المقالات</h5>
                <p class="mb-0 small">{{ $stats['posts_this_month'] }} هذا الشهر</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-center text-white bg-success h-100">
            <div class="card-body">
                <div class="display-4 mb-2">{{ number_format($stats['total_views']) }}</div>
                <h5 class="card-title">إجمالي المشاهدات</h5>
                <p class="mb-0 small">{{ number_format($stats['views_this_month']) }} هذا الشهر</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-center text-white bg-info h-100">
            <div class="card-body">
                <div class="display-4 mb-2">{{ $stats['total_comments'] }}</div>
                <h5 class="card-title">التعليقات</h5>
                <p class="mb-0 small">{{ $stats['approved_comments'] }} معتمد</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-center text-white bg-warning h-100">
            <div class="card-body">
                <div class="display-4 mb-2">{{ $stats['pending_comments'] }}</div>
                <h5 class="card-title">تعليقات معلقة</h5>
                <p class="mb-0 small">تحتاج للمراجعة</p>
            </div>
        </div>
    </div>
</div>

{{-- إحصائيات إضافية --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-success mb-2">{{ $stats['published_posts'] }}</div>
                <h6 class="card-title">المقالات المنشورة</h6>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-secondary mb-2">{{ $stats['draft_posts'] }}</div>
                <h6 class="card-title">المقالات المسودة</h6>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-primary mb-2">
                    {{ $stats['total_posts'] > 0 ? number_format($stats['total_views'] / $stats['total_posts'], 1) : 0 }}
                </div>
                <h6 class="card-title">متوسط المشاهدات لكل مقال</h6>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-info mb-2">{{ $stats['total_categories'] }}</div>
                <h6 class="card-title">عدد الفئات</h6>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-info mb-2">{{ $stats['total_tags'] }}</div>
                <h6 class="card-title">عدد الوسوم</h6>
            </div>
        </div>
    </div>
</div>

{{-- الأكثر مشاهدة --}}
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">🔥 الأكثر مشاهدة</h5>
            </div>
            <div class="card-body">
                @if($stats['most_viewed']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['most_viewed'] as $index => $post)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2">#{{ $index + 1 }}</span>
                                <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                                    {{ Str::limit($post->title, 40) }}
                                </a>
                            </div>
                            <span class="badge bg-success rounded-pill">{{ number_format($post->views) }} 👁️</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">لا توجد بيانات بعد</p>
                @endif
            </div>
        </div>
    </div>

    {{-- أحدث المقالات --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📝 أحدث المقالات</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_posts']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['recent_posts'] as $post)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <a href="{{ route('posts.show', $post) }}" class="text-decoration-none fw-bold">
                                    {{ Str::limit($post->title, 40) }}
                                </a>
                                <br>
                                <small class="text-muted">
                                    <span class="badge {{ $post->published ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $post->published ? 'منشور' : 'مسودة' }}
                                    </span>
                                    {{ $post->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <span class="badge bg-primary">{{ $post->views }} 👁️</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">لا توجد مقالات بعد</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- نسب الإحصائيات --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">📈 نسب المقالات</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <h6>المقالات المنشورة</h6>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $stats['total_posts'] > 0 ? ($stats['published_posts'] / $stats['total_posts'] * 100) : 0 }}%">
                                {{ $stats['total_posts'] > 0 ? number_format($stats['published_posts'] / $stats['total_posts'] * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <h6>المقالات المسودة</h6>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-secondary" role="progressbar"
                                 style="width: {{ $stats['total_posts'] > 0 ? ($stats['draft_posts'] / $stats['total_posts'] * 100) : 0 }}%">
                                {{ $stats['total_posts'] > 0 ? number_format($stats['draft_posts'] / $stats['total_posts'] * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <h6>التعليقات المعتمدة</h6>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                 style="width: {{ $stats['total_comments'] > 0 ? ($stats['approved_comments'] / $stats['total_comments'] * 100) : 0 }}%">
                                {{ $stats['total_comments'] > 0 ? number_format($stats['approved_comments'] / $stats['total_comments'] * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('posts.index') }}" class="btn btn-secondary">عودة للرئيسية</a>
</div>

@endsection


