<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản Lý Tin Tức - RentHome Admin</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    @include('partials.header-admin')

    <main class="admin-main">
        <div class="page-header">
            <div>
                <h1 class="page-title">Quản Lý Tin Tức</h1>
                <p class="page-subtitle">Quản lý và đăng bài viết tin tức thị trường</p>
            </div>
            <a href="{{ url('/') }}" class="btn-main-site">
                <i class="fa-solid fa-globe"></i>
                <span>Xem giao diện</span>
            </a>
        </div>
    </main>
</body>
</html>