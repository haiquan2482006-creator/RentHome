<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Cá Nhân - RentHome</title>
    
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
        /* Tùy chỉnh input bỏ đi webkit mặc định */
        input:focus { outline: none !important; }
        
        /* Ẩn thanh trượt cho sidebar và các thẻ khác */
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .custom-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
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
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-white relative">
        <!-- Top Header -->
        <header class="h-20 border-b border-slate-100 flex items-center justify-between px-8 z-10 shrink-0 bg-white/80 backdrop-blur-md">
            <div class="flex items-center gap-4 sm:gap-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Hồ sơ cá nhân</h1>
                    <p class="text-sm text-slate-500 font-medium hidden md:block">Cập nhật thông tin tài khoản và giấy tờ xác minh</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" onclick="enableEditMode()" id="btn-edit-mode" class="flex px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors shadow-sm items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
                </button>
                
                <button type="button" onclick="cancelEditMode()" id="btn-cancel-edit" class="hidden px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-bold transition-colors shadow-sm items-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Hủy
                </button>
                
                <button type="submit" form="profile-form" id="btn-save-edit" class="hidden px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold transition-colors shadow-sm items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                </button>

                <div class="w-px h-6 bg-slate-200 mx-1 hidden sm:block"></div>
                <a href="{{ url('/') }}" class="hidden sm:flex px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-bold rounded-xl transition-colors items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Ra trang chủ
                </a>
            </div>
        </header>

        <!-- Profile Form Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <form id="profile-form" action="#" method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto space-y-10 pb-8">
                @csrf
                
                <!-- Form Content Starts Here -->
                
                <!-- Khu vực 1: Thông tin cơ bản -->
                <section class="space-y-6 bg-slate-50/50 p-6 sm:p-8 rounded-[2rem] border border-slate-100/80">
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm"><i class="fa-solid fa-id-card"></i></span>
                        Thông tin cơ bản
                    </h3>
                    
                    <div class="flex flex-col gap-8">
                        <!-- Top: Avatar & Name -->
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8">
                            <!-- Avatar -->
                            <div class="flex flex-col items-center justify-center gap-4 shrink-0">
                                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden relative group bg-slate-50 shadow-sm border border-slate-100">
                                    <img id="preview-avatar" src="{{ $user->display_avatar }}" alt="Avatar" class="w-full h-full object-cover">
                                    <div id="avatar-upload-overlay" class="absolute inset-0 bg-black/40 hidden items-center justify-center transition-all cursor-pointer backdrop-blur-[2px]">
                                        <i class="fa-solid fa-camera text-white text-2xl"></i>
                                    </div>
                                    <input type="file" name="avatar" id="avatar-upload" disabled class="absolute inset-0 opacity-0 cursor-pointer w-full h-full disabled:cursor-not-allowed" accept="image/*">
                                </div>
                                <button type="button" id="btn-change-avatar" class="text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors bg-transparent opacity-50 cursor-not-allowed pointer-events-none">
                                    Đổi ảnh
                                </button>
                            </div>

                            <!-- User Info Display -->
                            <div class="flex flex-col items-center sm:items-start justify-center pt-2 sm:pt-6">
                                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center sm:text-left">{{ $user->account_name ?? $user->username ?? 'Người dùng' }}</h2>
                                <p class="text-slate-500 mt-1.5 font-medium text-center sm:text-left">{{ $user->email ?? 'Chưa cập nhật email' }}</p>
                                <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $isEnterprise ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-brand-50 text-brand-600 border-brand-100' }} text-sm font-bold border">
                                    <i class="fa-solid {{ $isEnterprise ? 'fa-building' : 'fa-user' }}"></i> {{ $isEnterprise ? 'Tài khoản Doanh nghiệp' : 'Tài khoản Cá nhân' }}
                                </span>
                            </div>
                        </div>

                        <!-- Bottom: Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 pt-8 border-t border-slate-100">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-user-tag text-slate-400"></i> Tên hiển thị</label>
                                <input type="text" name="username" value="{{ $user->username ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-70 disabled:cursor-not-allowed">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-signature text-slate-400"></i> Họ tên</label>
                                <input type="text" name="account_name" value="{{ $user->account_name ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-70 disabled:cursor-not-allowed">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-phone text-slate-400"></i> Số điện thoại</label>
                                <input type="text" name="phone" value="{{ $user->phone ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-70 disabled:cursor-not-allowed">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-envelope text-slate-400"></i> Email</label>
                                <input type="email" value="{{ $user->email ?? '' }}" readonly class="w-full px-4 py-3 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed font-medium opacity-70">
                            </div>

                            @if($isEnterprise)
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-file-invoice text-slate-400"></i> Mã số thuế</label>
                                <input type="text" name="tax_code" value="{{ $user->tax_code ?? '' }}" disabled placeholder="VD: 0101234567" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-70 disabled:cursor-not-allowed">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-slate-400"></i> Địa chỉ trụ sở</label>
                                <input type="text" name="address" value="{{ $user->address ?? '' }}" disabled placeholder="Nhập địa chỉ trụ sở chính..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-70 disabled:cursor-not-allowed">
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                <!-- Khu vực 2: Giấy tờ xác minh -->
                <section class="space-y-6 bg-slate-50/50 p-6 sm:p-8 rounded-[2rem] border border-slate-100/80">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm"><i class="fa-solid fa-shield-halved"></i></span>
                            Giấy tờ xác minh
                        </h3>
                        <p class="text-base text-slate-500 mt-2 font-medium">Vui lòng tải lên ảnh chụp giấy tờ xác minh (CCCD/CMND) rõ nét. Doanh nghiệp cần tải thêm Giấy phép ĐKKD.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 {{ $isEnterprise ? 'lg:grid-cols-3' : '' }} gap-5">
                        <!-- Mặt trước -->
                        <label class="doc-label relative flex flex-col items-center justify-center p-6 sm:p-8 h-64 rounded-3xl bg-slate-50 transition-all cursor-not-allowed group overflow-hidden hover:bg-slate-100 {{ $user->id_front ? 'opacity-100' : 'opacity-70' }}">
                            <img id="preview-id-front" src="{{ $user->id_front ? $user->id_front_url : '' }}" class="absolute inset-0 w-full h-full object-contain bg-slate-100 {{ $user->id_front ? '' : 'hidden' }} z-0">
                            
                            <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mb-4 transition-transform doc-icon-wrapper relative z-10 {{ $user->id_front ? 'hidden' : '' }}">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 transition-colors doc-icon"></i>
                            </div>
                            <span class="text-base font-bold text-slate-700 text-center doc-text relative z-10 {{ $user->id_front ? 'hidden' : '' }}">CCCD mặt trước</span>
                            <span class="text-sm text-slate-500 mt-1.5 font-medium relative z-10 doc-subtext {{ $user->id_front ? 'hidden' : '' }}">PNG, JPG tối đa 5MB</span>
                            <input type="file" name="id_front" disabled class="doc-upload absolute inset-0 w-full h-full opacity-0 cursor-not-allowed disabled:cursor-not-allowed z-20" accept="image/*">
                        </label>

                        <!-- Mặt sau -->
                        <label class="doc-label relative flex flex-col items-center justify-center p-6 sm:p-8 h-64 rounded-3xl bg-slate-50 transition-all cursor-not-allowed group overflow-hidden hover:bg-slate-100 {{ $user->id_back ? 'opacity-100' : 'opacity-70' }}">
                            <img id="preview-id-back" src="{{ $user->id_back ? $user->id_back_url : '' }}" class="absolute inset-0 w-full h-full object-contain bg-slate-100 {{ $user->id_back ? '' : 'hidden' }} z-0">
                            
                            <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mb-4 transition-transform doc-icon-wrapper relative z-10 {{ $user->id_back ? 'hidden' : '' }}">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 transition-colors doc-icon"></i>
                            </div>
                            <span class="text-base font-bold text-slate-700 text-center doc-text relative z-10 {{ $user->id_back ? 'hidden' : '' }}">CCCD mặt sau</span>
                            <span class="text-sm text-slate-500 mt-1.5 font-medium relative z-10 doc-subtext {{ $user->id_back ? 'hidden' : '' }}">PNG, JPG tối đa 5MB</span>
                            <input type="file" name="id_back" disabled class="doc-upload absolute inset-0 w-full h-full opacity-0 cursor-not-allowed disabled:cursor-not-allowed z-20" accept="image/*">
                        </label>

                        @if($isEnterprise)
                        <!-- Giấy phép kinh doanh -->
                        <label class="doc-label relative flex flex-col items-center justify-center p-6 sm:p-8 h-64 rounded-3xl bg-slate-50 transition-all cursor-not-allowed group overflow-hidden hover:bg-slate-100 {{ $user->business_license ? 'opacity-100' : 'opacity-70' }}">
                            <img id="preview-business-license" src="{{ $user->business_license ? $user->business_license_url : '' }}" class="absolute inset-0 w-full h-full object-contain bg-slate-100 {{ $user->business_license ? '' : 'hidden' }} z-0">
                            
                            <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mb-4 transition-transform doc-icon-wrapper relative z-10 {{ $user->business_license ? 'hidden' : '' }}">
                                <i class="fa-solid fa-file-signature text-3xl text-slate-400 transition-colors doc-icon"></i>
                            </div>
                            <span class="text-base font-bold text-slate-700 text-center doc-text relative z-10 {{ $user->business_license ? 'hidden' : '' }}">Giấy phép ĐKKD</span>
                            <span class="text-sm text-slate-500 mt-1.5 font-medium relative z-10 doc-subtext {{ $user->business_license ? 'hidden' : '' }}">PNG, JPG tối đa 5MB</span>
                            <input type="file" name="business_license" disabled class="doc-upload absolute inset-0 w-full h-full opacity-0 cursor-not-allowed disabled:cursor-not-allowed z-20" accept="image/*">
                        </label>
                        @endif
                    </div>
                </section>

                <!-- Khu vực 3: Thông tin thanh toán -->
                <section class="space-y-6 bg-slate-50/50 p-6 sm:p-8 rounded-[2rem] border border-slate-100/80">
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm"><i class="fa-solid fa-building-columns"></i></span>
                        Thông tin thanh toán
                    </h3>
                    
                    <!-- Form Inputs -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-5 mt-4">
                            <!-- Custom Bank Dropdown -->
                            <div class="space-y-2 relative" id="bank-dropdown-container">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-building-columns text-slate-400"></i> Ngân hàng</label>
                                <input type="hidden" name="bank_name" id="input-bank-name" value="{{ $user->bank_name ?? '' }}">
                                
                                <button type="button" disabled id="btn-select-bank" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all text-left flex items-center justify-between disabled:opacity-70 disabled:cursor-not-allowed group border border-transparent">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div id="selected-bank-logo-container" class="w-8 h-8 rounded-md bg-white shadow-sm flex items-center justify-center p-1 hidden shrink-0">
                                            <img id="selected-bank-logo" src="" class="w-full h-full object-contain">
                                        </div>
                                        <span id="selected-bank-text" class="font-bold text-slate-800 truncate">{{ $user->bank_name ?? 'Chọn ngân hàng...' }}</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-brand-500 transition-colors"></i>
                                </button>
                                
                                <!-- Dropdown List -->
                                <div id="bank-dropdown-list" class="hidden absolute bottom-full mb-2 left-0 right-0 bg-white rounded-xl shadow-xl border border-slate-100 max-h-72 overflow-y-auto z-50 py-2">
                                    <div class="p-4 text-center text-sm text-slate-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang tải dữ liệu...</div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-hashtag text-slate-400"></i> Số tài khoản</label>
                                <input type="text" inputmode="numeric" id="input-bank-account" name="bank_account" value="{{ $user->bank_account ?? '' }}" disabled placeholder="VD: 190300000000" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-bold text-slate-800 placeholder-slate-400 font-mono tracking-wider disabled:opacity-70 disabled:cursor-not-allowed border border-transparent">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-user text-slate-400"></i> Tên chủ tài khoản</label>
                                <input type="text" id="input-bank-owner" name="bank_owner" value="{{ $user->bank_owner ?? '' }}" disabled placeholder="VD: NGUYEN VAN A" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 transition-all font-bold text-slate-800 placeholder-slate-400 uppercase disabled:opacity-70 disabled:cursor-not-allowed border border-transparent">
                            </div>
                        </div>
                    </div>
                </section>

            </form>
        </div>
    </main>

    <script>
        function enableEditMode() {
            // Ẩn nút chỉnh sửa, hiện Lưu và Hủy
            document.getElementById('btn-edit-mode').classList.add('hidden');
            document.getElementById('btn-edit-mode').classList.remove('flex');
            
            const btnCancel = document.getElementById('btn-cancel-edit');
            btnCancel.classList.remove('hidden');
            btnCancel.classList.add('flex');
            
            const btnSave = document.getElementById('btn-save-edit');
            btnSave.classList.remove('hidden');
            btnSave.classList.add('flex');
            // Mở khóa các ô input chữ
            const inputs = document.querySelectorAll('.editable-input');
            inputs.forEach(input => {
                input.removeAttribute('disabled');
            });
            
            // Mở khóa avatar
            const avatarUpload = document.getElementById('avatar-upload');
            avatarUpload.removeAttribute('disabled');
            avatarUpload.classList.remove('disabled:cursor-not-allowed');
            
            const avatarOverlay = document.getElementById('avatar-upload-overlay');
            avatarOverlay.classList.add('group-hover:flex');
            
            const btnAvatar = document.getElementById('btn-change-avatar');
            btnAvatar.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            btnAvatar.classList.add('hover:bg-slate-200');

            // Mở khóa tải giấy tờ
            const docUploads = document.querySelectorAll('.doc-upload');
            docUploads.forEach(input => {
                input.removeAttribute('disabled');
                input.classList.remove('disabled:cursor-not-allowed');
                input.classList.add('cursor-pointer');
            });
            
            const docLabels = document.querySelectorAll('.doc-label');
            docLabels.forEach(label => {
                label.classList.remove('opacity-70', 'cursor-not-allowed');
                label.classList.add('hover:bg-slate-100', 'cursor-pointer');
                
                // Add hover styles manually using group-hover classes
                const iconWrapper = label.querySelector('.doc-icon-wrapper');
                iconWrapper.classList.add('group-hover:scale-105');
                
                const icon = label.querySelector('.doc-icon');
                icon.classList.add('group-hover:text-brand-500');
                
                const text = label.querySelector('.doc-text');
                text.classList.add('group-hover:text-brand-600');
            });
            
            // Focus vào input đầu tiên
            inputs[0].focus();
        }

        function cancelEditMode() {
            // Hủy bỏ các thay đổi bằng cách tải lại trang
            window.location.reload();
        }

        // JS preview hình ảnh cơ bản
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                if(this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        if (input.name === 'avatar') {
                            document.getElementById('preview-avatar').src = e.target.result;
                        } else if (input.name === 'id_front') {
                            const preview = document.getElementById('preview-id-front');
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            
                            // Ẩn icon và chữ đi
                            const label = input.closest('label');
                            label.classList.remove('opacity-70');
                            label.classList.add('opacity-100');
                            label.querySelector('.doc-icon-wrapper').classList.add('hidden');
                            label.querySelector('.doc-text').classList.add('hidden');
                            label.querySelector('.doc-subtext').classList.add('hidden');
                        } else if (input.name === 'id_back') {
                            const preview = document.getElementById('preview-id-back');
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            
                            // Ẩn icon và chữ đi
                            const label = input.closest('label');
                            label.classList.remove('opacity-70');
                            label.classList.add('opacity-100');
                            label.querySelector('.doc-icon-wrapper').classList.add('hidden');
                            label.querySelector('.doc-text').classList.add('hidden');
                            label.querySelector('.doc-subtext').classList.add('hidden');
                        } else if (input.name === 'business_license') {
                            const preview = document.getElementById('preview-business-license');
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            
                            // Ẩn icon và chữ đi
                            const label = input.closest('label');
                            label.classList.remove('opacity-70');
                            label.classList.add('opacity-100');
                            label.querySelector('.doc-icon-wrapper').classList.add('hidden');
                            label.querySelector('.doc-text').classList.add('hidden');
                            label.querySelector('.doc-subtext').classList.add('hidden');
                        }
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        // ============================================
        // JS Logic Ngân Hàng - Virtual Card - VietQR API
        // ============================================
        const bankDropdownBtn = document.getElementById('btn-select-bank');
        const bankDropdownList = document.getElementById('bank-dropdown-list');
        const inputBankName = document.getElementById('input-bank-name');
        
        const inputBankAccount = document.getElementById('input-bank-account');
        const inputBankOwner = document.getElementById('input-bank-owner');
        
        // 1. Fetch Banks
        let banksData = [];
        fetch('https://api.vietqr.io/v2/banks')
            .then(res => res.json())
            .then(data => {
                if(data.code === '00') {
                    banksData = data.data;
                    renderBanks(banksData);
                    
                    // Khôi phục UI logo nếu đã có bank_name từ server
                    const currentBank = inputBankName.value;
                    if(currentBank) {
                        // So khớp theo shortName
                        const bank = banksData.find(b => b.shortName.toLowerCase() === currentBank.toLowerCase());
                        if(bank) {
                            updateSelectedBank(bank);
                        }
                    }
                }
            })
            .catch(err => {
                bankDropdownList.innerHTML = '<div class="p-4 text-center text-sm text-rose-500">Lỗi tải danh sách ngân hàng. Vui lòng thử lại sau.</div>';
            });
            
        // 2. Render Bank List
        function renderBanks(banks) {
            bankDropdownList.innerHTML = '';
            
            // Search Input
            const searchDiv = document.createElement('div');
            searchDiv.className = 'px-3 pb-2 mb-2 border-b border-slate-100 sticky top-0 bg-white z-10 pt-2';
            searchDiv.innerHTML = '<input type="text" id="search-bank" placeholder="Tìm ngân hàng..." class="w-full px-4 py-2 bg-slate-50 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">';
            bankDropdownList.appendChild(searchDiv);
            
            const listContainer = document.createElement('div');
            bankDropdownList.appendChild(listContainer);
            
            const renderList = (data) => {
                listContainer.innerHTML = '';
                if(data.length === 0) {
                    listContainer.innerHTML = '<div class="p-4 text-center text-sm text-slate-500">Không tìm thấy ngân hàng</div>';
                    return;
                }
                
                data.forEach(bank => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-4 py-2 hover:bg-slate-50 transition-colors flex items-center gap-3 border-b border-slate-50 last:border-0';
                    btn.innerHTML = `
                        <div class="w-12 h-8 rounded bg-white shadow-sm border border-slate-100 p-1 flex items-center justify-center shrink-0">
                            <img src="${bank.logo}" class="w-full h-full object-contain">
                        </div>
                        <div class="flex flex-col overflow-hidden w-full">
                            <span class="font-bold text-sm text-slate-800">${bank.shortName}</span>
                            <span class="text-xs text-slate-500 truncate">${bank.name}</span>
                        </div>
                    `;
                    btn.onclick = () => {
                        updateSelectedBank(bank);
                        bankDropdownList.classList.add('hidden');
                    };
                    listContainer.appendChild(btn);
                });
            };
            
            renderList(banks);
            
            // Lắng nghe tìm kiếm
            document.getElementById('search-bank').addEventListener('input', function(e) {
                const keyword = e.target.value.toLowerCase();
                const filtered = banks.filter(b => b.shortName.toLowerCase().includes(keyword) || b.name.toLowerCase().includes(keyword));
                renderList(filtered);
            });
        }
        
        // 3. Update Selected Bank
        function updateSelectedBank(bank) {
            // Save value for Form submit
            inputBankName.value = bank.shortName;
            
            // Update Dropdown UI
            document.getElementById('selected-bank-text').textContent = bank.shortName + ' - ' + bank.name;
            document.getElementById('selected-bank-logo').src = bank.logo;
            document.getElementById('selected-bank-logo-container').classList.remove('hidden');
        }
        
        // 4. Toggle Dropdown Menu
        bankDropdownBtn.addEventListener('click', () => {
            bankDropdownList.classList.toggle('hidden');
            if(!bankDropdownList.classList.contains('hidden')) {
                const searchInput = document.getElementById('search-bank');
                if(searchInput) setTimeout(() => searchInput.focus(), 100);
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if(!bankDropdownBtn.contains(e.target) && !bankDropdownList.contains(e.target)) {
                bankDropdownList.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
