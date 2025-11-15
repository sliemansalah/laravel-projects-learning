@extends('layouts.app')

@section('title', 'الأرشيف')

@section('content')
<h1 class="mb-4">أرشيف المقالات</h1>

<div class="list-group">
    @foreach($archives as $archive)
    <a href="{{ route('posts.by-date', [$archive->year, $archive->month]) }}"
       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        <span>
            {{ \Carbon\Carbon::createFromDate($archive->year, $archive->month, 1)->locale('ar')->isoFormat('MMMM YYYY') }}
        </span>
        <span class="badge bg-primary rounded-pill">{{ $archive->count }}</span>
    </a>
    @endforeach
</div>
@endsection
