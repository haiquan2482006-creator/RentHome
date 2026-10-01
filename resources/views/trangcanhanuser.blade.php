<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - RentHome</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        @php
            $themeSettings = file_exists(storage_path('app/theme_settings.json')) 
                ? json_decode(file_get_contents(storage_path('app/theme_settings.json')), true) 
                : [];
            $tColor = $themeSettings['theme_color'] ?? '#16a34a';
        @endphp
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '{{ $tColor }}10',
                            100: '{{ $tColor }}20',
                            500: '{{ $tColor }}',
                            600: '{{ $tColor }}',
                            700: '{{ $tColor }}',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden text-slate-800">

    @php
        $user = \Illuminate\Support\Facades\Auth::user();
        $isEnterprise = $user && $user->account_type === 'doanhnghiep';
        $userName = $user ? ($user->account_name ?? $user->username ?? 'Người dùng') : 'Người dùng';
    @endphp

    @include('partials.sidebar-user')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50 relative">
        <!-- Top Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Bảng điều khiển</h1>
                <p class="text-sm text-slate-500 font-medium">Chào mừng bạn trở lại, {{ $userName }}!</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Ra trang chủ
                </a>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-6xl mx-auto space-y-8">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Tổng bài đăng</p>
                            <p class="text-3xl font-black text-slate-800">12</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Lượt xem</p>
                            <p class="text-3xl font-black text-slate-800">348</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Lượt lưu</p>
                            <p class="text-3xl font-black text-slate-800">25</p>
                        </div>
                    </div>
                    @if($isEnterprise)
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Tòa nhà</p>
                            <p class="text-3xl font-black text-slate-800">2</p>
                        </div>
                    </div>
                    @else
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Điểm uy tín</p>
                            <p class="text-3xl font-black text-slate-800">100</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Main Actions Grid -->
                <div>
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2"><i class="fa-solid fa-bolt text-amber-500"></i> Truy cập nhanh</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- Create Post -->
                        <a href="{{ route('dangbai') }}" class="group bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-brand-300 transition-all text-center flex flex-col items-center">
                            <div class="w-20 h-20 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Đăng bài mới</h3>
                            <p class="text-sm text-slate-500">Tạo một tin đăng cho thuê phòng, nhà ở hoặc mặt bằng mới.</p>
                        </a>

                        <!-- Manage Posts -->
                        <a href="#" class="group bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-purple-300 transition-all text-center flex flex-col items-center">
                            <div class="w-20 h-20 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all">
                                <i class="fa-solid fa-list"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Quản lý bài đăng</h3>
                            <p class="text-sm text-slate-500">Xem, chỉnh sửa hoặc xóa các bài đăng bạn đã tạo trên hệ thống.</p>
                        </a>

                        <!-- Update Profile -->
                        <a href="#" class="group bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all text-center flex flex-col items-center">
                            <div class="w-20 h-20 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all">
                                <i class="fa-solid fa-user-pen"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Cập nhật hồ sơ</h3>
                            <p class="text-sm text-slate-500">Chỉnh sửa thông tin liên hệ, avatar, hoặc đổi mật khẩu.</p>
                        </a>

                        @if($isEnterprise)
                        <!-- Manage Buildings (Enterprise Only) -->
                        <a href="#" class="group bg-gradient-to-br from-blue-500 to-blue-700 p-8 rounded-[2rem] shadow-lg hover:shadow-blue-500/30 hover:-translate-y-1 transition-all text-center flex flex-col items-center relative overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 text-blue-400/20 text-9xl">
                                <i class="fa-solid fa-city"></i>
                            </div>
                            <div class="w-20 h-20 rounded-full bg-white/20 text-white flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-all relative z-10 backdrop-blur-sm">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 relative z-10">Quản lý Tòa nhà</h3>
                            <p class="text-sm text-blue-100 relative z-10">Phân quyền, quản lý hệ thống phòng ốc tập trung dành riêng cho doanh nghiệp.</p>
                        </a>
                        @endif

                    </div>
                </div>
                
                <!-- Notice Banner -->
                <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-amber-900 mb-1">Mẹo nâng cao hiệu quả đăng tin</h4>
                        <p class="text-sm text-amber-800/80 leading-relaxed">Để bài đăng thu hút nhiều người xem hơn, hãy sử dụng hình ảnh sắc nét, mô tả chân thực tiện ích phòng và cung cấp mức giá hợp lý. Các bài đăng có đầy đủ thông tin thường nhận được sự quan tâm gấp 3 lần.</p>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
