<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Tin Bất Động Sản - RentHome</title>
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="bg-slate-900/60 min-h-screen flex items-center justify-center p-3 sm:p-6 antialiased overflow-y-auto">

@php
    $currentUser = Auth::user();
    $reqType = request()->query('account_type', request()->query('type', ''));
    $accountType = (!empty($reqType) && in_array($reqType, ['canhan', 'doanhnghiep'])) ? $reqType : ($currentUser->account_type ?? 'canhan');
    $isEnterprise = ($accountType === 'doanhnghiep');
    $displayName = $currentUser 
        ? ($isEnterprise ? ($currentUser->company_name ?? $currentUser->account_name ?? 'Tập Đoàn BĐS Đạt Phát') : ($currentUser->account_name ?? $currentUser->username ?? 'Nguyễn Văn Tuấn'))
        : ($isEnterprise ? 'Tập Đoàn BĐS Đạt Phát' : 'Nguyễn Văn Tuấn');
    $badgeText = $isEnterprise ? 'Doanh Nghiệp' : 'Cá Nhân';
    $subTitle = $isEnterprise ? 'Đối tác Doanh Nghiệp' : 'Tài khoản Cá nhân';
@endphp

    <!-- Modal Container -->
    <div
        class="bg-white rounded-3xl max-w-2xl w-full my-auto shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[88vh]">

        <!-- ==================== BƯỚC 1: XÁC NHẬN VỊ TRÍ CHI TIẾT ==================== -->
        <div id="step-location" class="flex flex-col h-full min-h-0">
            <!-- Modal Header -->
            <div class="px-6 py-4 sm:py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-lg">Xác nhận vị trí chi tiết</h3>
                </div>
                <a href="{{ url('Overview') }}"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </a>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tỉnh / Thành Phố <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="select-province"
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none appearance-none cursor-pointer">
                                <option>Thành phố Hà Nội</option>
                                <option selected>Thành phố Hồ Chí Minh</option>
                                <option>Thành phố Đà Nẵng</option>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Quận / Huyện <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="select-district"
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none appearance-none cursor-pointer">
                                <option value="">Chọn Quận/Huyện</option>
                                <option selected>Quận 1</option>
                                <option>Quận Bình Thạnh</option>
                                <option>Quận Cầu Giấy</option>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Phường / Xã <span
                            class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select id="select-ward"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none appearance-none cursor-pointer">
                            <option value="">Chọn phường/xã</option>
                            <option selected>Phường Bến Nghé</option>
                            <option>Phường 22</option>
                            <option>Phường Dịch Vọng</option>
                        </select>
                        <i
                            class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Địa chỉ chi tiết (Tên đường, số
                        nhà)</label>
                    <input type="text" id="input-address" placeholder="Ví dụ: Số 12, Ngõ 45"
                        class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-amber-400 focus:outline-none transition-all">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-white shrink-0">
                <a href="{{ url('Overview') }}"
                    class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors inline-block">
                    Quay lại
                </a>
                <button type="button" onclick="goToStepDetails()"
                    class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md shadow-amber-500/20 transition-all">
                    Xác nhận & Tiếp tục
                </button>
            </div>
        </div>

        <!-- ==================== BƯỚC 2: THÔNG TIN CHI TIẾT BẤT ĐỘNG SẢN ==================== -->
        <div id="step-details" class="flex flex-col h-full min-h-0 hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 sm:py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-lg">Thông tin chi tiết bất động sản</h3>
                </div>
                <a href="{{ url('Overview') }}"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </a>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
                <!-- Tiêu đề bài đăng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tiêu đề bài đăng <span
                            class="text-rose-500">*</span></label>
                    <input type="text" id="post-title" placeholder="VD: Bán căn hộ cao cấp 2 phòng ngủ Quận 1"
                        class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- Thông tin Loại tài khoản đăng tin (Cố định theo tài khoản đang đăng nhập) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tài khoản đăng tin</label>
                    <div class="p-3.5 rounded-2xl border {{ $isEnterprise ? 'border-purple-200 bg-purple-50/40' : 'border-emerald-200 bg-emerald-50/40' }} flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ $isEnterprise ? 'bg-purple-600 text-white' : 'bg-emerald-600 text-white' }} flex items-center justify-center text-sm font-bold shadow-xs">
                                <i class="{{ $isEnterprise ? 'fa-solid fa-building-user' : 'fa-solid fa-user-check' }}"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900">{{ $displayName }}</h4>
                                <p class="text-[11px] font-medium text-slate-500">{{ $subTitle }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $isEnterprise ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                            {{ $badgeText }}
                        </span>
                    </div>
                    <input type="hidden" name="account_role" value="{{ $accountType }}">
                </div>

                <!-- Loại hình BĐS -->
                <div class="space-y-3">
                    <!-- Mục dành cho Doanh nghiệp -->
                    <div id="building-select-box" class="{{ $isEnterprise ? '' : 'hidden' }}">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Chọn tòa nhà đã tạo <span
                                class="text-purple-600">(Doanh nghiệp)</span></label>
                        <div class="relative">
                            <select
                                class="w-full px-4 py-3 rounded-2xl bg-purple-50/40 border border-purple-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-purple-500 focus:outline-none appearance-none cursor-pointer">
                                <option>Tòa Landmark Plus (Bình Thạnh)</option>
                                <option>Khu Căn Hộ S5 Vinhomes Grand Park (TP. Thủ Đức)</option>
                                <option>Chung cư Masteri Thảo Điền (Quận 2)</option>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Loại hình BĐS chung -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Loại hình BĐS <span
                                class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            <label
                                class="flex items-center justify-center p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                <input type="radio" name="property_type" value="nha_o" checked
                                    onchange="togglePropertyType('nha_o')" class="hidden">
                                Nhà ở
                            </label>
                            <label
                                class="flex items-center justify-center p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                <input type="radio" name="property_type" value="dat_nen"
                                    onchange="togglePropertyType('dat_nen')" class="hidden">
                                Đất nền
                            </label>
                            <label
                                class="flex items-center justify-center p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                <input type="radio" name="property_type" value="phong_tro"
                                    onchange="togglePropertyType('phong_tro')" class="hidden">
                                Phòng trọ
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Giá & Diện tích -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Giá mong muốn (Triệu, Tỷ / tháng)
                            <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" id="post-price" placeholder="VD: 14.5 Triệu hoặc 3.5 Tỷ"
                                class="w-full pl-4 pr-10 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-amber-600 text-xs">đ</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Diện tích (m²) <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" id="post-area" placeholder="VD: 85"
                                class="w-full pl-4 pr-10 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-amber-600 text-xs">m²</span>
                        </div>
                    </div>
                </div>

                <!-- Số phòng ngủ & Số phòng tắm (Counter Stepper - Ẩn khi chọn Đất nền) -->
                <div id="room-counter-box" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Số phòng ngủ</label>
                        <div
                            class="flex items-center justify-between px-3 py-2 rounded-2xl border border-slate-200 bg-white">
                            <button type="button" onclick="stepCount('bedroom', -1)"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">-</button>
                            <span id="bedroom-count" class="font-bold text-xs text-slate-800">1</span>
                            <button type="button" onclick="stepCount('bedroom', 1)"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">+</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Số phòng tắm</label>
                        <div
                            class="flex items-center justify-between px-3 py-2 rounded-2xl border border-slate-200 bg-white">
                            <button type="button" onclick="stepCount('bathroom', -1)"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">-</button>
                            <span id="bathroom-count" class="font-bold text-xs text-slate-800">1</span>
                            <button type="button" onclick="stepCount('bathroom', 1)"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>

                <!-- Tiện ích & Đặc điểm nổi bật (Cho phép chọn hoặc ghi tiện ích) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Tiện ích & Đặc điểm nổi bật</span>
                        <span class="text-[11px] text-slate-400 font-normal">Nhập tùy chỉnh hoặc chọn gợi ý</span>
                    </label>

                    <!-- Gợi ý tiện ích chọn nhanh -->
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <button type="button" onclick="addAmenityTag('Bãi đỗ xe')"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Bãi đỗ xe
                        </button>
                        <button type="button" onclick="addAmenityTag('Mặt tiền đường')"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Mặt tiền đường
                        </button>
                        <button type="button" onclick="addAmenityTag('Điện âm nước máy')"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Điện âm nước máy
                        </button>
                        <button type="button" onclick="addAmenityTag('Sổ hồng riêng')"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Sổ hồng riêng
                        </button>
                        <button type="button" onclick="addAmenityTag('Gần chợ / Siêu thị')"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Gần chợ / Siêu thị
                        </button>
                        <button type="button" onclick="addAmenityTag('Bảo vệ 24/7')"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Bảo vệ 24/7
                        </button>
                    </div>

                    <!-- Textarea cho phép tự nhập / ghi tiện ích ra -->
                    <textarea id="amenities-input" name="amenities" rows="2.5"
                        placeholder="Ghi các tiện ích của BĐS (VD: Đường rộng 12m, điện âm nước máy, gần chợ, khu dân cư đông đúc, sổ hồng chính chủ...)"
                        class="w-full px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"></textarea>
                </div>

                <!-- Mô tả chi tiết -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Mô tả chi tiết</label>
                    <textarea id="post-description" rows="3"
                        placeholder="Mô tả thông tin chi tiết về căn nhà (vị trí địa lý, tiện ích xung quanh, phong thủy, pháp lý...)"
                        class="w-full px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                </div>

                <!-- Hình ảnh dự án / Bất động sản -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Hình ảnh thực tế bất động sản <span
                                class="text-rose-500">*</span></label>
                        <span id="image-count-badge" class="text-[11px] font-bold text-emerald-600 hidden">Đã chọn:
                            0/10 ảnh</span>
                    </div>

                    <!-- Dropzone / Local File Input Trigger -->
                    <label for="property-images-input"
                        class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-emerald-50/30 cursor-pointer block transition-all group">
                        <input type="file" id="property-images-input" name="images[]" multiple
                            accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden"
                            onchange="handleImageUpload(event)">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto transition-colors">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <p
                            class="text-xs text-slate-700 mt-2 font-bold group-hover:text-emerald-700 transition-colors">
                            Nhấp vào đây để chọn ảnh từ máy tính của bạn</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Hỗ trợ JPG, PNG, WEBP (Tối đa 10 ảnh, mỗi ảnh ≤
                            5MB)</p>
                    </label>

                    <!-- Image Preview Grid Gallery -->
                    <div id="image-preview-container" class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-3 hidden">
                        <!-- JS dynamically renders uploaded thumbnails here -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-white shrink-0">
                <button type="button" onclick="goToStepLocation()"
                    class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors">
                    Quay lại
                </button>
                <button type="button" onclick="submitPost()"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Đăng Bài Viết
                </button>
            </div>
        </div>

    </div>

    <!-- JavaScript Controller -->
    <script>
        function goToStepDetails() {
            document.getElementById('step-location').classList.add('hidden');
            document.getElementById('step-details').classList.remove('hidden');
        }

        function goToStepLocation() {
            document.getElementById('step-details').classList.add('hidden');
            document.getElementById('step-location').classList.remove('hidden');
        }

        // Tăng giảm phòng ngủ / phòng tắm
        function stepCount(type, delta) {
            const el = document.getElementById(type + '-count');
            let val = parseInt(el.innerText) + delta;
            if (val < 0) val = 0;
            el.innerText = val;
        }

        // Ẩn hiện phần chọn tòa nhà dựa vào vai trò
        function toggleRoleForm(role) {
            const box = document.getElementById('building-select-box');
            if (role === 'doanhnghiep') {
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
            }
        }

        // Ẩn hiện số phòng ngủ & phòng tắm khi chuyển loại BĐS (Đất nền vs Nhà ở / Phòng trọ)
        function togglePropertyType(type) {
            const roomBox = document.getElementById('room-counter-box');
            if (roomBox) {
                if (type === 'dat_nen') {
                    roomBox.classList.add('hidden');
                } else {
                    roomBox.classList.remove('hidden');
                }
            }
        }

        // Thêm tiện ích vào ô nhập tiện ích
        function addAmenityTag(text) {
            const input = document.getElementById('amenities-input');
            if (!input) return;
            let currentVal = input.value.trim();
            if (currentVal.includes(text)) return;
            if (currentVal.length > 0) {
                input.value = currentVal + ', ' + text;
            } else {
                input.value = text;
            }
        }

        // Quản lý và xem trước ảnh tải lên từ máy tính
        let uploadedFilesArray = [];

        function handleImageUpload(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            files.forEach(file => {
                if (uploadedFilesArray.length < 10 && file.type.startsWith('image/')) {
                    uploadedFilesArray.push(file);
                }
            });

            renderImagePreviews();
        }

        function removeUploadedImage(index) {
            uploadedFilesArray.splice(index, 1);
            renderImagePreviews();
        }

        function renderImagePreviews() {
            const container = document.getElementById('image-preview-container');
            const badge = document.getElementById('image-count-badge');
            if (!container) return;

            container.innerHTML = '';

            if (uploadedFilesArray.length > 0) {
                container.classList.remove('hidden');
                if (badge) {
                    badge.classList.remove('hidden');
                    badge.innerText = `Đã chọn: ${uploadedFilesArray.length}/10 ảnh`;
                }
            } else {
                container.classList.add('hidden');
                if (badge) badge.classList.add('hidden');
                return;
            }

            uploadedFilesArray.forEach((file, idx) => {
                const fileUrl = URL.createObjectURL(file);
                const card = document.createElement('div');
                card.className =
                    'relative group aspect-square rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-100';

                card.innerHTML = `
                    <img src="${fileUrl}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeUploadedImage(${idx})"
                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/70 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition-colors shadow-sm"
                        title="Xóa ảnh này">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="absolute bottom-0 inset-x-0 bg-slate-900/60 backdrop-blur-xs p-1 text-[9px] text-white font-medium truncate text-center">
                        ${file.name}
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Xử lý gửi bài viết lên hệ thống chờ duyệt
        function submitPost() {
            const titleInput = document.getElementById('post-title');
            const title = titleInput ? titleInput.value.trim() : '';

            if (!title) {
                alert('Vui lòng nhập tiêu đề bài đăng!');
                if (titleInput) titleInput.focus();
                return;
            }

            const province = document.getElementById('select-province')?.value || '';
            const district = document.getElementById('select-district')?.value || '';
            const ward = document.getElementById('select-ward')?.value || '';
            const address = document.getElementById('input-address')?.value || '';

            const price = document.getElementById('post-price')?.value.trim() || 'Thỏa thuận';
            const area = document.getElementById('post-area')?.value ? document.getElementById('post-area').value + ' m²' : '';
            const desc = document.getElementById('post-description')?.value.trim() || '';

            let locationParts = [address, ward, district, province].filter(Boolean);
            let locationStr = locationParts.length > 0 ? locationParts.join(', ') : 'TP. Hồ Chí Minh';

            const postObj = {
                id: '#RH-' + Math.floor(1000 + Math.random() * 9000),
                title: title,
                price: price.includes('Tr') || price.includes('Tỷ') || price.includes('đ') ? price : (price + ' Tr/tháng'),
                location: locationStr,
                time: 'Gửi lúc ' + new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' - Hôm nay',
                image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=120&q=80',
                status: 'Chờ phê duyệt',
                createdAt: Date.now()
            };

            const saveAndRedirect = (finalPost) => {
                let existing = [];
                try {
                    existing = JSON.parse(localStorage.getItem('pendingPosts') || '[]');
                } catch (e) {
                    existing = [];
                }
                existing.unshift(finalPost);
                localStorage.setItem('pendingPosts', JSON.stringify(existing));

                alert('Đã gửi bài viết lên hệ thống chờ duyệt!');
                window.location.href = "{{ url('Overview') }}?tab=pending-posts";
            };

            if (uploadedFilesArray && uploadedFilesArray.length > 0) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    postObj.image = e.target.result;
                    saveAndRedirect(postObj);
                };
                reader.onerror = function () {
                    saveAndRedirect(postObj);
                };
                reader.readAsDataURL(uploadedFilesArray[0]);
            } else {
                saveAndRedirect(postObj);
            }
        }
    </script>
</body>

</html>
