@extends('layouts.app')

@section('title', 'جميع المقالات')

@section('content')
    <h2>📝 جميع المقالات</h2>

    @if($posts->count() > 0)
        <div class="grid">
            @foreach($posts as $post)
                <div class="card">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                    @else
                        <img src="https://via.placeholder.com/350x200/667eea/ffffff?text=مقال" alt="صورة افتراضية">
                    @endif

                    <div class="card-body">
                        <h3 class="card-title">{{ $post->title }}</h3>
                        <p class="card-excerpt">{{ $post->excerpt }}</p>
                        <small style="color: #999;">{{ $post->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-sm">قراءة المزيد</a>
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm">تعديل</a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $posts->links() }}
        </div>
    @else
        <p style="text-align: center; color: #999; padding: 3rem;">
            لا توجد مقالات بعد. ابدأ بكتابة مقالك الأول!
        </p>
    @endif
@endsection
