<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'مدونتي')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('posts.index') }}">📝 مدونتي</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.index') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.create') }}">مقال جديد</a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.archive') }}">📅 الأرشيف</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.bookmarks') }}">
                            ⭐ المفضلة
                            @if(session()->has('bookmarks') && count(session('bookmarks')) > 0)
                                <span class="badge bg-warning text-dark">{{ count(session('bookmarks')) }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('posts.statistics') }}">📊 الإحصائيات</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/j5nm2shixajllst64xg217wk3us82u13qh5op8h2sfj69z4g/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    @stack('scripts')
</body>
</html>


<style>
.related-posts .card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.related-posts .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.related-posts .card-title a {
    color: #333;
    transition: color 0.3s;
}

.related-posts .card-title a:hover {
    color: #007bff;
}

/* تحسين زر المفضلة */
.btn-outline-warning:hover {
    color: #fff;
}

/* Badge المفضلة في Navbar */
.navbar .badge {
    position: relative;
    top: -2px;
    font-size: 0.7rem;
}

/* Animation عند الحفظ */
@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1); }
    75% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.btn-warning.saved {
    animation: heartBeat 0.6s;
}

/* تحسين بطاقات الإحصائيات */
.card.text-white .display-4,
.card.text-white .display-6 {
    font-weight: bold;
}

.card.text-white {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.card.text-white:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

/* تحسين Progress Bars */
.progress {
    border-radius: 10px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.progress-bar {
    font-weight: bold;
    font-size: 14px;
}

/* تحسين القوائم */
.list-group-item {
    border-left: 3px solid transparent;
    transition: all 0.3s;
}

.list-group-item:hover {
    border-left-color: #007bff;
    background-color: #f8f9fa;
    transform: translateX(-5px);
}

/* Animation للأرقام */
@keyframes countUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.display-4, .display-6 {
    animation: countUp 0.6s ease-out;
}
</style>
