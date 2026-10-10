<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu - RentHome</title>
    
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #ffffff; }
        input:focus { outline: none !important; }
        
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .custom-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden text-slate-800 bg-white">

    @php
        $user = \Illuminate\Support\Facades\Auth::user();
        $isEnterprise = $user && $user->account_type === 'doanhnghiep';
        $userName = $user ? ($isEnterprise ? ($user->representative ?? $user->account_name ?? $user->company_name ?? $user->username ?? 'Doanh nghiệp') : ($user->account_name ?? $user->username ?? 'Người dùng')) : 'Người dùng';
    @endphp

    @include('partials.sidebar-user')
    @include('partials.sidebar-user', ['isDrawer' => true])

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full min-w-0 overflow-hidden bg-white relative">
        <!-- Top Header (Đồng bộ với Trang cá nhân) -->
        <header class="h-20 border-b border-slate-100 flex items-center justify-between px-6 lg:px-10 z-10 shrink-0 bg-white/90 backdrop-blur-md">
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Nút toggle sidebar cho màn hình nhỏ -->
                <button type="button" onclick="toggleGlobalSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Đổi mật khẩu</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            Bảo mật
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium hidden sm:block">Thiết lập mật khẩu mới an toàn để bảo vệ tài khoản RentHome của bạn</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 sm:gap-3">
                <a href="{{ url('/trangcanhanuser') }}" class="flex px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors shadow-xs items-center gap-2">
                    <i class="fa-solid fa-id-card"></i> <span class="hidden sm:inline">Hồ sơ cá nhân</span>
                </a>

                <div class="w-px h-6 bg-slate-200 mx-1 hidden sm:block"></div>
                
                <a href="{{ url('/') }}" class="hidden sm:flex px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-bold rounded-xl transition-colors border border-slate-200/60 items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Ra trang chủ
                </a>
            </div>
        </header>

        <!-- Change Password Content -->
        <div class="flex-1 overflow-y-auto w-full px-6 lg:px-10 py-8 custom-scrollbar">
            <div class="max-w-4xl mx-auto space-y-8 pb-20">
                
                <!-- Feedback Alert: Thành công -->
                @if(session('success'))
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 shadow-sm flex items-start justify-between gap-4 animate-fade-in">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm sm:text-base text-emerald-900 leading-snug">{{ session('success') }}</h4>
                                <p class="text-xs sm:text-sm text-emerald-700 mt-1">Mật khẩu mới của bạn đã được cập nhật thành công. Phiên đăng nhập hiện tại vẫn được duy trì an toàn.</p>
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 transition-colors p-1">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                @endif

                <!-- Feedback Alert: Lỗi tổng quan nếu có -->
                @if($errors->has('general_error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 animate-fade-in">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                        <span class="text-sm font-semibold">{{ $errors->first('general_error') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left Column: Form đổi mật khẩu chính (7 Cols) -->
                    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                        
                        <div class="border-b border-slate-100 pb-5">
                            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                                    <i class="fa-solid fa-key"></i>
                                </span>
                                Thay đổi mật khẩu
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">Vui lòng nhập mật khẩu hiện tại để xác thực trước khi cập nhật mật khẩu mới.</p>
                        </div>

                        <form id="change-password-form" action="{{ url('/doi-mat-khau') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- 1. Mật khẩu hiện tại (Current Password) -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                        Mật khẩu hiện tại <span class="text-rose-500">*</span>
                                    </label>
                                    <a href="{{ url('/quen-mat-khau') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 transition-colors hover:underline">
                                        Quên mật khẩu?
                                    </a>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 text-slate-400">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </span>
                                    <input type="password" 
                                           id="current_password" 
                                           name="current_password" 
                                           placeholder="Nhập mật khẩu đang sử dụng"
                                           class="w-full pl-11 pr-11 py-3 bg-slate-50 border {{ $errors->has('current_password') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:bg-white focus:border-brand-500' }} rounded-xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:ring-4 focus:ring-brand-500/10 transition-all"
                                           required>
                                    <button type="button" 
                                            onclick="togglePasswordVisibility('current_password', this)" 
                                            class="absolute right-3.5 text-slate-400 hover:text-slate-600 p-1 transition-colors"
                                            title="Ẩn/Hiện mật khẩu">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <!-- Báo lỗi màu đỏ ngay dưới input nếu sai mật khẩu cũ -->
                                @error('current_password')
                                    <p class="text-xs font-semibold text-rose-500 mt-1.5 flex items-center gap-1.5 animate-fade-in">
                                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- 2. Mật khẩu mới (New Password) -->
                            <div class="space-y-1.5">
                                <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Mật khẩu mới <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 text-slate-400">
                                        <i class="fa-solid fa-shield-halved text-sm"></i>
                                    </span>
                                    <input type="password" 
                                           id="new_password" 
                                           name="new_password" 
                                           placeholder="Tối thiểu 6 ký tự, không trùng mật khẩu hiện tại"
                                           oninput="checkPasswordStrength(this.value)"
                                           class="w-full pl-11 pr-11 py-3 bg-slate-50 border {{ $errors->has('new_password') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:bg-white focus:border-brand-500' }} rounded-xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:ring-4 focus:ring-brand-500/10 transition-all"
                                           required>
                                    <button type="button" 
                                            onclick="togglePasswordVisibility('new_password', this)" 
                                            class="absolute right-3.5 text-slate-400 hover:text-slate-600 p-1 transition-colors"
                                            title="Ẩn/Hiện mật khẩu">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                
                                <!-- Password Strength Meter (Thước đo độ mạnh) -->
                                <div class="pt-1.5 space-y-1.5">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-slate-400 font-medium">Độ mạnh mật khẩu:</span>
                                        <span id="strength-label" class="font-bold text-slate-400">Chưa nhập</span>
                                    </div>
                                    <div class="grid grid-cols-4 gap-1.5 h-1.5">
                                        <div id="bar-1" class="h-full rounded-full bg-slate-200 transition-colors duration-300"></div>
                                        <div id="bar-2" class="h-full rounded-full bg-slate-200 transition-colors duration-300"></div>
                                        <div id="bar-3" class="h-full rounded-full bg-slate-200 transition-colors duration-300"></div>
                                        <div id="bar-4" class="h-full rounded-full bg-slate-200 transition-colors duration-300"></div>
                                    </div>
                                </div>

                                <!-- Báo lỗi màu đỏ ngay dưới input nếu không thỏa mãn -->
                                @error('new_password')
                                    <p class="text-xs font-semibold text-rose-500 mt-1.5 flex items-center gap-1.5 animate-fade-in">
                                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- 3. Xác nhận mật khẩu mới (Confirm Password) -->
                            <div class="space-y-1.5">
                                <label for="new_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Xác nhận mật khẩu mới <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 text-slate-400">
                                        <i class="fa-solid fa-circle-check text-sm"></i>
                                    </span>
                                    <input type="password" 
                                           id="new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           placeholder="Nhập lại chính xác mật khẩu mới"
                                           oninput="checkPasswordMatch()"
                                           class="w-full pl-11 pr-11 py-3 bg-slate-50 border {{ $errors->has('new_password_confirmation') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:bg-white focus:border-brand-500' }} rounded-xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:ring-4 focus:ring-brand-500/10 transition-all"
                                           required>
                                    <button type="button" 
                                            onclick="togglePasswordVisibility('new_password_confirmation', this)" 
                                            class="absolute right-3.5 text-slate-400 hover:text-slate-600 p-1 transition-colors"
                                            title="Ẩn/Hiện mật khẩu">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <div id="match-status" class="hidden text-xs font-semibold mt-1.5 items-center gap-1.5"></div>
                                
                                <!-- Báo lỗi màu đỏ ngay dưới input nếu không khớp -->
                                @error('new_password_confirmation')
                                    <p class="text-xs font-semibold text-rose-500 mt-1.5 flex items-center gap-1.5 animate-fade-in">
                                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-4 flex flex-col sm:flex-row items-center gap-3">
                                <button type="submit" 
                                        class="w-full sm:w-auto flex-1 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-500/25 flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-[0.99] transition-all">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Lưu thay đổi mật khẩu</span>
                                </button>
                                
                                <a href="{{ url('/trangcanhanuser') }}" 
                                   class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors text-center">
                                    Hủy bỏ
                                </a>
                            </div>

                        </form>
                    </div>

                    <!-- Right Column: Quy tắc & Mẹo an toàn (5 Cols) -->
                    <div class="lg:col-span-5 space-y-6">
                        
                        <!-- Card Tiêu chuẩn mật khẩu -->
                        <div class="bg-slate-50/80 rounded-3xl border border-slate-200/80 p-6 space-y-4">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-brand-600"></i>
                                Yêu cầu mật khẩu mới
                            </h3>
                            <ul class="space-y-3 text-xs text-slate-600">
                                <li id="rule-length" class="flex items-center gap-2.5 transition-colors">
                                    <i class="fa-solid fa-circle-dot text-slate-300 text-[10px] w-4 text-center"></i>
                                    <span>Tối thiểu <strong>6 ký tự</strong> trở lên</span>
                                </li>
                                <li id="rule-diff" class="flex items-center gap-2.5 transition-colors">
                                    <i class="fa-solid fa-circle-dot text-slate-300 text-[10px] w-4 text-center"></i>
                                    <span><strong>Không được trùng</strong> với mật khẩu hiện tại</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Card Hướng dẫn bảo mật -->
                        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-6 shadow-xl shadow-slate-900/10 space-y-4">
                            <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-amber-400">
                                <i class="fa-solid fa-shield-halved text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm tracking-tight text-white">Bảo mật tài khoản RentHome</h4>
                                <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                                    Đổi mật khẩu định kỳ 3 - 6 tháng một lần giúp bảo vệ tài sản, bài đăng và các hợp đồng cho thuê nhà của bạn trước các rủi ro xâm nhập trái phép.
                                </p>
                            </div>
                            <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                                <span><i class="fa-solid fa-lock text-emerald-400 mr-1"></i> Mã hóa chuẩn Bcrypt</span>
                                <span>Phiên an toàn</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- Javascript tương tác -->
    <script>
        // Toggle ẩn/hiện mật khẩu
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash text-brand-600';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye text-slate-400';
            }
        }

        // Kiểm tra độ mạnh mật khẩu và cập nhật checklist thời gian thực
        function checkPasswordStrength(val) {
            const curVal = document.getElementById('current_password').value;
            
            // 1. Cập nhật 2 điều kiện bắt buộc ở cột Yêu cầu
            const hasLength = val.length >= 6;
            const isDiff = val.length > 0 && val !== curVal;

            updateRuleItem('rule-length', hasLength);
            updateRuleItem('rule-diff', isDiff);

            // 2. Tính toán ĐỘ MẠNH thực tế của mật khẩu
            const bar1 = document.getElementById('bar-1');
            const bar2 = document.getElementById('bar-2');
            const bar3 = document.getElementById('bar-3');
            const bar4 = document.getElementById('bar-4');
            const label = document.getElementById('strength-label');

            // Reset thanh độ mạnh
            [bar1, bar2, bar3, bar4].forEach(b => {
                b.className = 'h-full rounded-full bg-slate-200 transition-colors duration-300';
            });

            if (val.length === 0) {
                label.textContent = 'Chưa nhập';
                label.className = 'font-bold text-slate-400';
                checkPasswordMatch();
                return;
            }

            // Tính điểm đánh giá độ mạnh:
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 8) score++;
            if (val.length >= 10) score++;

            const hasLower = /[a-z]/.test(val);
            const hasUpper = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSpecial = /[^A-Za-z0-9]/.test(val);

            const variety = (hasLower ? 1 : 0) + (hasUpper ? 1 : 0) + (hasNumber ? 1 : 0) + (hasSpecial ? 1 : 0);
            if (variety >= 2) score++;
            if (variety >= 3) score++;
            if (variety >= 4) score++;

            // Phân mức độ mạnh chuẩn:
            // Cấp 1: Yếu (1 vạch đỏ)
            // Cấp 2: Trung bình (2 vạch vàng)
            // Cấp 3: Mạnh (3 vạch xanh lá)
            // Cấp 4: Rất mạnh (4 vạch xanh lá đậm)
            if (val.length < 6 || score <= 1) {
                bar1.className = 'h-full rounded-full bg-rose-500 transition-colors duration-300';
                label.textContent = 'Yếu';
                label.className = 'font-bold text-rose-500';
            } else if (score <= 3) {
                bar1.className = 'h-full rounded-full bg-amber-500 transition-colors duration-300';
                bar2.className = 'h-full rounded-full bg-amber-500 transition-colors duration-300';
                label.textContent = 'Trung bình';
                label.className = 'font-bold text-amber-500';
            } else if (score <= 4) {
                bar1.className = 'h-full rounded-full bg-emerald-500 transition-colors duration-300';
                bar2.className = 'h-full rounded-full bg-emerald-500 transition-colors duration-300';
                bar3.className = 'h-full rounded-full bg-emerald-500 transition-colors duration-300';
                label.textContent = 'Mạnh';
                label.className = 'font-bold text-emerald-600';
            } else {
                [bar1, bar2, bar3, bar4].forEach(b => {
                    b.className = 'h-full rounded-full bg-emerald-600 transition-colors duration-300';
                });
                label.textContent = 'Rất mạnh';
                label.className = 'font-bold text-emerald-600';
            }

            checkPasswordMatch();
        }

        function updateRuleItem(id, satisfied) {
            const el = document.getElementById(id);
            if (!el) return;
            const icon = el.querySelector('i');
            if (satisfied) {
                el.className = 'flex items-center gap-2.5 text-emerald-600 font-semibold transition-colors';
                icon.className = 'fa-solid fa-circle-check text-emerald-500 text-xs w-4 text-center';
            } else {
                el.className = 'flex items-center gap-2.5 text-slate-600 transition-colors';
                icon.className = 'fa-solid fa-circle-dot text-slate-300 text-[10px] w-4 text-center';
            }
        }

        // Kiểm tra khớp mật khẩu
        function checkPasswordMatch() {
            const pass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('new_password_confirmation').value;
            const status = document.getElementById('match-status');
            if (!status) return;

            if (confirmPass.length === 0) {
                status.className = 'hidden';
                return;
            }

            status.classList.remove('hidden');
            status.style.display = 'flex';

            if (pass === confirmPass) {
                status.className = 'flex text-xs font-semibold mt-1.5 items-center gap-1.5 text-emerald-600';
                status.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i> Mật khẩu xác nhận trùng khớp';
            } else {
                status.className = 'flex text-xs font-semibold mt-1.5 items-center gap-1.5 text-rose-500';
                status.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-500 text-[11px]"></i> Mật khẩu xác nhận chưa khớp';
            }
        }
    </script>
</body>
</html>
