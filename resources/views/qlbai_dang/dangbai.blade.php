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
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">Tỉnh / Thành Phố <span
                                    class="text-rose-500">*</span></label>
                            <button type="button" id="btn-toggle-custom-province" onclick="toggleCustomProvinceMode()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i> <span id="toggle-custom-text">Tự nhập khác</span>
                            </button>
                        </div>

                        <!-- Mode 1: Searchable Dropdown Box -->
                        <div id="province-select-wrapper" class="relative">
                            <input type="hidden" id="select-province" name="province" value="TP. Hồ Chí Minh">
                            
                            <!-- Dropdown Trigger Button -->
                            <button type="button" id="province-dropdown-btn" onclick="toggleFormProvinceDropdown(event)"
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 text-left flex items-center justify-between focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer">
                                <span id="form-province-text" class="truncate font-bold text-slate-800">TP. Hồ Chí Minh</span>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="form-province-arrow"></i>
                            </button>

                            <!-- Floating Dropdown Panel -->
                            <div id="form-province-panel" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs animate-fadeIn">
                                <!-- Search Input Bar -->
                                <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0 z-10">
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" id="form-province-search" oninput="filterFormProvinces()" placeholder="Tìm tỉnh / thành phố..."
                                            class="w-full pl-8 pr-7 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium text-slate-800">
                                        <button type="button" id="form-clear-search-btn" onclick="clearFormProvinceSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- List of 63 Provinces & Cities -->
                                <div class="max-h-56 overflow-y-auto custom-scrollbar" id="form-provinces-list">
                                    <!-- Populated via JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Mode 2: Custom Text Input -->
                        <div id="province-custom-wrapper" class="hidden relative">
                            <input type="text" id="custom-province-input" oninput="onCustomProvinceInput()" placeholder="Nhập tên tỉnh / thành phố của bạn..."
                                class="w-full px-4 py-3 rounded-2xl bg-amber-50/40 border border-amber-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">Quận / Huyện <span
                                    class="text-rose-500">*</span></label>
                            <button type="button" id="btn-toggle-custom-district" onclick="toggleCustomDistrictMode()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i> <span id="toggle-custom-district-text">Tự nhập khác</span>
                            </button>
                        </div>

                        <!-- Mode 1: Searchable Dropdown Box for District -->
                        <div id="district-select-wrapper" class="relative">
                            <input type="hidden" id="select-district" name="district" value="Quận 1">
                            
                            <!-- Dropdown Trigger Button -->
                            <button type="button" id="district-dropdown-btn" onclick="toggleFormDistrictDropdown(event)"
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 text-left flex items-center justify-between focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer">
                                <span id="form-district-text" class="truncate font-bold text-slate-800">Quận 1</span>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="form-district-arrow"></i>
                            </button>

                            <!-- Floating Dropdown Panel -->
                            <div id="form-district-panel" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs animate-fadeIn">
                                <!-- Search Input Bar -->
                                <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0 z-10">
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" id="form-district-search" oninput="filterFormDistricts()" placeholder="Tìm quận / huyện..."
                                            class="w-full pl-8 pr-7 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium text-slate-800">
                                        <button type="button" id="form-clear-district-search-btn" onclick="clearFormDistrictSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- List of Districts -->
                                <div class="max-h-56 overflow-y-auto custom-scrollbar" id="form-districts-list">
                                    <!-- Populated via JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Mode 2: Custom Text Input for District -->
                        <div id="district-custom-wrapper" class="hidden relative">
                            <input type="text" id="custom-district-input" oninput="onCustomDistrictInput()" placeholder="Nhập tên quận / huyện của bạn..."
                                class="w-full px-4 py-3 rounded-2xl bg-amber-50/40 border border-amber-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Phường / Xã <span
                                class="text-rose-500">*</span></label>
                        <button type="button" id="btn-toggle-custom-ward" onclick="toggleCustomWardMode()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition-colors">
                            <i class="fa-solid fa-pen-to-square"></i> <span id="toggle-custom-ward-text">Tự nhập khác</span>
                        </button>
                    </div>

                    <!-- Mode 1: Searchable Dropdown Box for Ward -->
                    <div id="ward-select-wrapper" class="relative">
                        <input type="hidden" id="select-ward" name="ward" value="Phường Bến Nghé">
                        
                        <!-- Dropdown Trigger Button -->
                        <button type="button" id="ward-dropdown-btn" onclick="toggleFormWardDropdown(event)"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 text-left flex items-center justify-between focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer">
                            <span id="form-ward-text" class="truncate font-bold text-slate-800">Phường Bến Nghé</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="form-ward-arrow"></i>
                        </button>

                        <!-- Floating Dropdown Panel -->
                        <div id="form-ward-panel" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs animate-fadeIn">
                            <!-- Search Input Bar -->
                            <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0 z-10">
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" id="form-ward-search" oninput="filterFormWards()" placeholder="Tìm phường / xã..."
                                        class="w-full pl-8 pr-7 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium text-slate-800">
                                    <button type="button" id="form-clear-ward-search-btn" onclick="clearFormWardSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- List of Wards -->
                            <div class="max-h-56 overflow-y-auto custom-scrollbar" id="form-wards-list">
                                <!-- Populated via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Mode 2: Custom Text Input for Ward -->
                    <div id="ward-custom-wrapper" class="hidden relative">
                        <input type="text" id="custom-ward-input" oninput="onCustomWardInput()" placeholder="Nhập tên phường / xã của bạn..."
                            class="w-full px-4 py-3 rounded-2xl bg-amber-50/40 border border-amber-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none transition-all">
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
        // Dữ liệu Quận/Huyện/Thị xã & Phường/Xã/Thị trấn cho các Tỉnh Thành Việt Nam
        const vnLocationData = {
            "TP. Hồ Chí Minh": {
                "Quận 1": ["Phường Bến Nghé", "Phường Bến Thành", "Phường Cầu Kho", "Phường Cầu Ông Lãnh", "Phường Đa Kao", "Phường Nguyễn Thái Bình", "Phường Tân Định"],
                "Quận 3": ["Phường Võ Thị Sáu", "Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5"],
                "Quận 5": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6"],
                "Quận 7": ["Phường Tân Thuận Đông", "Phường Tân Thuận Tây", "Phường Tân Kiểng", "Phường Tân Phong", "Phường Phú Mỹ"],
                "Quận 10": ["Phường 1", "Phường 2", "Phường 10", "Phường 12", "Phường 14", "Phường 15"],
                "Quận Bình Thạnh": ["Phường 1", "Phường 2", "Phường 15", "Phường 22", "Phường 25", "Phường 26"],
                "TP. Thủ Đức": ["Phường Thảo Điền", "Phường An Phú", "Phường Linh Trung", "Phường Hiệp Bình Chánh", "Phường Tân Phú"],
                "Quận Gò Vấp": ["Phường 1", "Phường 3", "Phường 5", "Phường 7", "Phường 10", "Phường 11"],
                "Quận Tân Bình": ["Phường 1", "Phường 2", "Phường 4", "Phường 12", "Phường 13", "Phường 15"],
                "Quận Phú Nhuận": ["Phường 1", "Phường 2", "Phường 3", "Phường 5", "Phường 7", "Phường 9"],
                "Huyện Bình Chánh": ["Thị trấn Tân Túc", "Xã Bình Hưng", "Xã Phong Phú", "Xã Vĩnh Lộc A", "Xã Vĩnh Lộc B", "Xã Tân Kiên"],
                "Huyện Củ Chi": ["Thị trấn Củ Chi", "Xã Tân An Hội", "Xã Nhuận Đức", "Xã Phạm Văn Cội", "Xã Tân Thạnh Đông"],
                "Huyện Hóc Môn": ["Thị trấn Hóc Môn", "Xã Bà Điểm", "Xã Đông Thạnh", "Xã Tân Xuân", "Xã Thới Tam Thôn"],
                "Huyện Nhà Bè": ["Thị trấn Nhà Bè", "Xã Phước Kiển", "Xã Nhơn Đức", "Xã Phú Xuân", "Xã Hiệp Phước"],
                "Huyện Cần Giờ": ["Thị trấn Cần Thạnh", "Xã Long Hòa", "Xã Bình Khánh", "Xã Lý Nhơn"]
            },
            "Hà Nội": {
                "Quận Cầu Giấy": ["Phường Dịch Vọng", "Phường Dịch Vọng Hậu", "Phường Mai Dịch", "Phường Nghĩa Tân", "Phường Yên Hòa"],
                "Quận Ba Đình": ["Phường Cống Vị", "Phường Điện Biên", "Phường Kim Mã", "Phường Ngọc Khánh", "Phường Đội Cấn"],
                "Quận Hoàn Kiếm": ["Phường Hàng Bạc", "Phường Hàng Bồ", "Phường Hàng Gai", "Phường Tràng Tiền", "Phường Lý Thái Tổ"],
                "Quận Đống Đa": ["Phường Cát Linh", "Phường Láng Hạ", "Phường Láng Thượng", "Phường Ô Chợ Dừa", "Phường Trung Liệt"],
                "Quận Hai Bà Trưng": ["Phường Bách Khoa", "Phường Bạch Đằng", "Phường Minh Khai", "Phường Trương Định"],
                "Quận Thanh Xuân": ["Phường Hạ Đình", "Phường Khương Trung", "Phường Nhân Chính", "Phường Thanh Xuân Bắc"],
                "Quận Hà Đông": ["Phường Hà Cầu", "Phường Mộ Lao", "Phường Quang Trung", "Phường Văn Quán"],
                "Quận Nam Từ Liêm": ["Phường Cầu Diễn", "Phường Mỹ Đình 1", "Phường Mỹ Đình 2", "Phường Trung Văn"],
                "Quận Bắc Từ Liêm": ["Phường Cổ Nhuế 1", "Phường Cổ Nhuế 2", "Phường Xuân Đỉnh", "Phường Minh Khai"],
                "Quận Tây Hồ": ["Phường Bưởi", "Phường Thụy Khuê", "Phường Quảng An", "Phường Nhật Tân"],
                "Quận Long Biên": ["Phường Gia Thụy", "Phường Bồ Đề", "Phường Việt Hưng", "Phường Ngọc Lâm"],
                "Quận Hoàng Mai": ["Phường Giáp Bát", "Phường Định Công", "Phường Tân Mai", "Phường Hoàng Văn Thụ"],
                "Thị xã Sơn Tây": ["Phường Lê Lợi", "Phường Ngô Quyền", "Xã Sơn Đông", "Xã Đường Lâm"],
                "Huyện Đông Anh": ["Thị trấn Đông Anh", "Xã Hải Bối", "Xã Vĩnh Ngọc", "Xã Kim Chung", "Xã Tiên Dương"],
                "Huyện Gia Lâm": ["Thị trấn Trâu Quỳ", "Xã Đặng Xá", "Xã Ninh Hiệp", "Xã Cổ Bi"],
                "Huyện Hoài Đức": ["Thị trấn Trạm Trôi", "Xã An Khánh", "Xã Vân Canh", "Xã Đức Giang"],
                "Huyện Thanh Trì": ["Thị trấn Văn Điển", "Xã Tân Triều", "Xã Tả Thanh Oai", "Xã Tam Hiệp"],
                "Huyện Thạch Thất": ["Thị trấn Liên Quan", "Xã Tân Xã", "Xã Thạch Hòa", "Xã Tiến Xuân"],
                "Huyện Ba Vì": ["Thị trấn Tây Đằng", "Xã Tản Lĩnh", "Xã Chu Minh"]
            },
            "Đà Nẵng": {
                "Quận Hải Châu": ["Phường Hải Châu I", "Phường Hải Châu II", "Phường Thạch Thang", "Phường Thuận Phước", "Phường Phước Ninh"],
                "Quận Thanh Khê": ["Phường An Khê", "Phường Chính Gián", "Phường Hòa Khê", "Phường Tam Thuận"],
                "Quận Sơn Trà": ["Phường An Hải Bắc", "Phường An Hải Tây", "Phường Phước Mỹ", "Phường Thọ Quang"],
                "Quận Ngũ Hành Sơn": ["Phường Khuê Mỹ", "Phường Mỹ An", "Phường Hòa Hải", "Phường Hòa Quý"],
                "Quận Liên Chiểu": ["Phường Hòa Hiệp Bắc", "Phường Hòa Hiệp Nam", "Phường Hòa Khánh Bắc", "Phường Hòa Khánh Nam"],
                "Quận Cẩm Lệ": ["Phường Khuê Trung", "Phường Hòa Thọ Đông", "Phường Hòa Thọ Tây", "Phường Hòa Xuân"],
                "Huyện Hòa Vang": ["Xã Hòa Châu", "Xã Hòa Phước", "Xã Hòa Phong", "Xã Hòa Nhơn", "Xã Hòa Tiến"]
            },
            "Hải Phòng": {
                "Quận Hồng Bàng": ["Phường Hoàng Văn Thụ", "Phường Minh Khai", "Phường Phan Bội Châu"],
                "Quận Ngô Quyền": ["Phường Cầu Đất", "Phường Lạch Tray", "Phường Máy Tơ"],
                "Quận Lê Chân": ["Phường An Dương", "Phường Cát Dài", "Phường Hàng Kênh"],
                "Quận Hải An": ["Phường Đằng Hải", "Phường Đằng Lâm", "Phường Đông Hải 1"],
                "Quận Kiến An": ["Phường Bắc Sơn", "Phường Đồng Hòa", "Phường Trần Thành Ngọ"],
                "Huyện Thủy Nguyên": ["Thị trấn Núi Đèo", "Xã An Lư", "Xã Lập Lễ", "Xã Thủy Đường"],
                "Huyện An Dương": ["Thị trấn An Dương", "Xã An Đồng", "Xã Nam Sơn"],
                "Huyện Cát Hải": ["Thị trấn Cát Bà", "Thị trấn Cát Hải", "Xã Trân Châu"]
            },
            "Cần Thơ": {
                "Quận Ninh Kiều": ["Phường An Khánh", "Phường An Hòa", "Phường An Hội", "Phường Tân An", "Phường Xuân Khánh"],
                "Quận Bình Thủy": ["Phường Bình Thủy", "Phường Bùi Hữu Nghĩa", "Phường Trà Nóc", "Phường Long Tuyền"],
                "Quận Cái Răng": ["Phường Lê Bình", "Phường Hưng Phú", "Phường Hưng Thạnh"],
                "Quận Ô Môn": ["Phường Châu Văn Liêm", "Phường Phước Thới", "Phường Thới Hòa"],
                "Huyện Phong Điền": ["Thị trấn Phong Điền", "Xã Nhơn Ai", "Xã Giai Xuân"],
                "Huyện Thới Lai": ["Thị trấn Thới Lai", "Xã Định Môn", "Xã Trường Thành"]
            },
            "Bình Dương": {
                "TP. Thủ Dầu Một": ["Phường Phú Hòa", "Phường Phú Cường", "Phường Phú Lợi", "Phường Hiệp Thành", "Phường Chánh Nghĩa"],
                "TP. Thuận An": ["Phường Lái Thiêu", "Phường An Phú", "Phường Bình Hòa", "Phường Vĩnh Phú"],
                "TP. Dĩ An": ["Phường Dĩ An", "Phường Tân Bình", "Phường Đông Hòa", "Phường An Bình"],
                "TP. Bến Cát": ["Phường Mỹ Phước", "Phường Thới Hòa", "Phường Tân Định"],
                "TP. Tân Uyên": ["Phường Uyên Hưng", "Phường Tân Phước Khánh", "Phường Thái Hòa"],
                "Huyện Bàu Bàng": ["Thị trấn Lai Uyên", "Xã Trừ Văn Thố", "Xã Lai Hưng"],
                "Huyện Dầu Tiếng": ["Thị trấn Dầu Tiếng", "Xã Minh Thạnh", "Xã Định An"],
                "Huyện Phú Giáo": ["Thị trấn Phước Vĩnh", "Xã An Bình", "Xã Phước Hòa"]
            },
            "Đồng Nai": {
                "TP. Biên Hòa": ["Phường Tân Phong", "Phường Trảng Dài", "Phường Thống Nhất", "Phường Quyết Thắng", "Phường Trung Dũng"],
                "TP. Long Khánh": ["Phường Xuân Trung", "Phường Xuân Thanh", "Phường Phú Bình"],
                "Huyện Long Thành": ["Thị trấn Long Thành", "Xã An Phước", "Xã Bình Sơn", "Xã Lộc An"],
                "Huyện Nhơn Trạch": ["Thị trấn Hiệp Phước", "Xã Đại Phước", "Xã Phú Hữu", "Xã Phước Thiền"],
                "Huyện Trảng Bom": ["Thị trấn Trảng Bom", "Xã Hố Nai 3", "Xã Bắc Sơn", "Xã Sông Trầu"]
            },
            "Bà Rịa - Vũng Tàu": {
                "TP. Vũng Tàu": ["Phường 1", "Phường 2", "Phường 7", "Phường 8", "Phường 9", "Phường Nguyễn An Ninh", "Phường Rạch Dừa"],
                "TP. Bà Rịa": ["Phường Phước Trung", "Phường Phước Hưng", "Phường Phước Nguyên"],
                "Thị xã Phú Mỹ": ["Phường Phú Mỹ", "Phường Mỹ Xuân", "Phường Tân Phước"],
                "Huyện Long Điền": ["Thị trấn Long Điền", "Thị trấn Long Hải", "Xã Phước Hưng"],
                "Huyện Châu Đức": ["Thị trấn Ngãi Giao", "Xã Kim Long", "Xã Suối Nghệ"],
                "Huyện Xuyên Mộc": ["Thị trấn Phước Bửu", "Xã Bình Châu", "Xã Phước Thuận"]
            },
            "Khánh Hòa": {
                "TP. Nha Trang": ["Phường Lộc Thọ", "Phường Phương Sài", "Phường Phước Hải", "Phường Phước Tiến", "Phường Vĩnh Hải", "Phường Vĩnh Phước"],
                "TP. Cam Ranh": ["Phường Cam Lộc", "Phường Cam Phú", "Phường Cam Thuận"],
                "Thị xã Ninh Hòa": ["Phường Ninh Hiệp", "Phường Ninh Diễm", "Xã Ninh Sim"],
                "Huyện Diên Khánh": ["Thị trấn Diên Khánh", "Xã Diên An", "Xã Diên Toàn"],
                "Huyện Cam Lâm": ["Thị trấn Cam Đức", "Xã Cam Thành Bắc", "Xã Cam Hải Đông"]
            },
            "Lâm Đồng": {
                "TP. Đà Lạt": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 8", "Phường 9", "Phường 10"],
                "TP. Bảo Lộc": ["Phường 1", "Phường 2", "Phường B'Lao", "Phường Lộc Sơn"],
                "Huyện Đức Trọng": ["Thị trấn Liên Nghĩa", "Xã Hiệp Thạnh", "Xã Phú Hội"],
                "Huyện Đơn Dương": ["Thị trấn Thạnh Mỹ", "Thị trấn D'Ran", "Xã Ka Đơn"]
            }
        };

        function onProvinceChange() {
            const provinceEl = document.getElementById('select-province');
            const hiddenDist = document.getElementById('select-district');
            const textDist = document.getElementById('form-district-text');

            if (!provinceEl) return;
            const provName = provinceEl.value;

            let districtsMap = {};
            if (vnLocationData[provName]) {
                districtsMap = vnLocationData[provName];
            } else {
                districtsMap = {
                    [`TP. ${provName}`]: ["Phường 1", "Phường 2", "Phường Tân Tiến", "Phường Trung Tâm", "Xã Ngoại Thành"],
                    [`Thị xã ${provName}`]: ["Phường 1", "Phường 2", "Phường Hòa Bình", "Xã An Bình"],
                    [`Huyện ${provName}`]: ["Thị trấn Trung Tâm", "Xã Tân Lập", "Xã Hòa Phú", "Xã Mỹ Thạnh"],
                    [`Huyện Châu Thành`]: ["Thị trấn Châu Thành", "Xã Tân Bình", "Xã Phú Hữu", "Xã Đông Hòa"]
                };
            }

            let distList = Object.keys(districtsMap);
            let firstDist = distList.length > 0 ? distList[0] : '';
            if (hiddenDist) hiddenDist.value = firstDist;
            if (textDist) textDist.innerText = firstDist || 'Chọn Quận/Huyện';

            renderFormDistricts();
            onDistrictChange();
        }

        function onDistrictChange() {
            const provinceEl = document.getElementById('select-province');
            const districtEl = document.getElementById('select-district');
            const hiddenWard = document.getElementById('select-ward');
            const textWard = document.getElementById('form-ward-text');

            if (!provinceEl || !districtEl) return;
            const provName = provinceEl.value;
            const distName = districtEl.value;

            let wardsList = [];
            if (vnLocationData[provName] && vnLocationData[provName][distName]) {
                wardsList = vnLocationData[provName][distName];
            } else if (distName.startsWith('Huyện')) {
                wardsList = ["Thị trấn Trung Tâm", "Xã Tân Lập", "Xã Hòa Phú", "Xã Mỹ Thạnh", "Xã Đông Bình"];
            } else if (distName.startsWith('Thị xã')) {
                wardsList = ["Phường 1", "Phường 2", "Phường Hòa Bình", "Xã An Bình", "Xã Tân Hòa"];
            } else {
                wardsList = ["Phường 1", "Phường 2", "Phường 3", "Phường Tân Phú", "Phường Trung Tâm"];
            }

            let firstWard = wardsList.length > 0 ? wardsList[0] : '';
            if (hiddenWard) hiddenWard.value = firstWard;
            if (textWard) textWard.innerText = firstWard || 'Chọn Phường/Xã';

            renderFormWards();
        }

        // Khởi tạo các tùy chọn ban đầu khi tải trang
        document.addEventListener('DOMContentLoaded', function () {
            onProvinceChange();
            renderFormProvinces();
            renderFormDistricts();
            renderFormWards();
        });

        // Dữ liệu 63 Tỉnh Thành
        const vietnamLocations = {
            centralCities: [
                "TP. Hồ Chí Minh",
                "Hà Nội",
                "Đà Nẵng",
                "Hải Phòng",
                "Cần Thơ"
            ],
            provinces: [
                "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", "Bắc Ninh",
                "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", "Bình Thuận", "Cà Mau",
                "Cao Bằng", "Đắk Lắk", "Đắk Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp",
                "Gia Lai", "Hà Giang", "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang",
                "Hòa Bình", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu",
                "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An",
                "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình", "Quảng Nam",
                "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La", "Tây Ninh",
                "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh",
                "Tuyên Quang", "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
            ]
        };

        function removeVietnameseTones(str) {
            if (!str) return '';
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .toLowerCase();
        }

        // ------------------ TỈNH / THÀNH PHỐ ------------------
        let isCustomProvinceMode = false;

        function toggleCustomProvinceMode() {
            isCustomProvinceMode = !isCustomProvinceMode;
            const selectWrapper = document.getElementById('province-select-wrapper');
            const customWrapper = document.getElementById('province-custom-wrapper');
            const toggleText = document.getElementById('toggle-custom-text');
            const customInput = document.getElementById('custom-province-input');
            const hiddenInput = document.getElementById('select-province');

            if (isCustomProvinceMode) {
                if (selectWrapper) selectWrapper.classList.add('hidden');
                if (customWrapper) customWrapper.classList.remove('hidden');
                if (toggleText) toggleText.innerText = 'Chọn từ danh sách';
                if (customInput) {
                    customInput.focus();
                    if (hiddenInput) hiddenInput.value = customInput.value.trim() || 'TP. Hồ Chí Minh';
                }
            } else {
                if (selectWrapper) selectWrapper.classList.remove('hidden');
                if (customWrapper) customWrapper.classList.add('hidden');
                if (toggleText) toggleText.innerText = 'Tự nhập khác';
                const provinceText = document.getElementById('form-province-text')?.innerText || 'TP. Hồ Chí Minh';
                if (hiddenInput) hiddenInput.value = provinceText;
            }
            onProvinceChange();
        }

        function toggleFormProvinceDropdown(event) {
            if (event) event.stopPropagation();
            const panel = document.getElementById('form-province-panel');
            const arrow = document.getElementById('form-province-arrow');
            const searchInput = document.getElementById('form-province-search');

            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                document.getElementById('form-district-panel')?.classList.add('hidden');
                document.getElementById('form-ward-panel')?.classList.add('hidden');

                panel.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
                renderFormProvinces();
                setTimeout(() => {
                    if (searchInput) searchInput.focus();
                }, 50);
            } else {
                panel.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function renderFormProvinces(filterKeyword = '') {
            const container = document.getElementById('form-provinces-list');
            const currentVal = document.getElementById('select-province')?.value || 'TP. Hồ Chí Minh';
            if (!container) return;

            const cleanKeyword = removeVietnameseTones(filterKeyword.trim());
            let html = '';

            const filteredCities = vietnamLocations.centralCities.filter(c =>
                removeVietnameseTones(c).includes(cleanKeyword)
            );

            if (filteredCities.length > 0) {
                html += `<div class="px-3.5 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-600 border-y border-slate-200/70">🏙️ Thành phố trực thuộc TW</div>`;
                filteredCities.forEach(city => {
                    const isSelected = currentVal === city;
                    html += `
                        <div onclick="selectFormProvince('${city}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-800'}">
                            <span>${city}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            }

            const filteredProvinces = vietnamLocations.provinces.filter(p =>
                removeVietnameseTones(p).includes(cleanKeyword)
            );

            if (filteredProvinces.length > 0) {
                html += `<div class="px-3.5 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-600 border-y border-slate-200/70">🏔️ Tỉnh thành</div>`;
                filteredProvinces.forEach(prov => {
                    const isSelected = currentVal === prov;
                    html += `
                        <div onclick="selectFormProvince('${prov}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-medium transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-700'}">
                            <span>${prov}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            }

            if (filteredCities.length === 0 && filteredProvinces.length === 0) {
                html = `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy tỉnh / thành phố phù hợp</div>`;
            }

            container.innerHTML = html;
        }

        function selectFormProvince(name) {
            const hiddenInput = document.getElementById('select-province');
            const textSpan = document.getElementById('form-province-text');
            if (hiddenInput) hiddenInput.value = name;
            if (textSpan) textSpan.innerText = name;

            const panel = document.getElementById('form-province-panel');
            const arrow = document.getElementById('form-province-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';

            onProvinceChange();
        }

        function filterFormProvinces() {
            const input = document.getElementById('form-province-search');
            const clearBtn = document.getElementById('form-clear-search-btn');
            const val = input ? input.value : '';

            if (clearBtn) {
                if (val.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            renderFormProvinces(val);
        }

        function clearFormProvinceSearch() {
            const input = document.getElementById('form-province-search');
            const clearBtn = document.getElementById('form-clear-search-btn');
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            renderFormProvinces('');
            if (input) input.focus();
        }

        function onCustomProvinceInput() {
            const customInput = document.getElementById('custom-province-input');
            const hiddenInput = document.getElementById('select-province');
            if (customInput && hiddenInput) {
                hiddenInput.value = customInput.value.trim();
                onProvinceChange();
            }
        }

        // ------------------ QUẬN / HUYỆN ------------------
        let isCustomDistrictMode = false;

        function toggleCustomDistrictMode() {
            isCustomDistrictMode = !isCustomDistrictMode;
            const selectWrapper = document.getElementById('district-select-wrapper');
            const customWrapper = document.getElementById('district-custom-wrapper');
            const toggleText = document.getElementById('toggle-custom-district-text');
            const customInput = document.getElementById('custom-district-input');
            const hiddenInput = document.getElementById('select-district');

            if (isCustomDistrictMode) {
                if (selectWrapper) selectWrapper.classList.add('hidden');
                if (customWrapper) customWrapper.classList.remove('hidden');
                if (toggleText) toggleText.innerText = 'Chọn từ danh sách';
                if (customInput) {
                    customInput.focus();
                    if (hiddenInput) hiddenInput.value = customInput.value.trim() || 'Quận 1';
                }
            } else {
                if (selectWrapper) selectWrapper.classList.remove('hidden');
                if (customWrapper) customWrapper.classList.add('hidden');
                if (toggleText) toggleText.innerText = 'Tự nhập khác';
                const districtText = document.getElementById('form-district-text')?.innerText || 'Quận 1';
                if (hiddenInput) hiddenInput.value = districtText;
            }
            onDistrictChange();
        }

        function toggleFormDistrictDropdown(event) {
            if (event) event.stopPropagation();
            const panel = document.getElementById('form-district-panel');
            const arrow = document.getElementById('form-district-arrow');
            const searchInput = document.getElementById('form-district-search');

            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                document.getElementById('form-province-panel')?.classList.add('hidden');
                document.getElementById('form-ward-panel')?.classList.add('hidden');

                panel.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
                renderFormDistricts();
                setTimeout(() => {
                    if (searchInput) searchInput.focus();
                }, 50);
            } else {
                panel.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function renderFormDistricts(filterKeyword = '') {
            const container = document.getElementById('form-districts-list');
            const hiddenInput = document.getElementById('select-district');
            const currentVal = hiddenInput ? hiddenInput.value : '';
            const provName = document.getElementById('select-province')?.value || 'TP. Hồ Chí Minh';
            if (!container) return;

            let districtsMap = {};
            if (vnLocationData[provName]) {
                districtsMap = vnLocationData[provName];
            } else {
                districtsMap = {
                    [`TP. ${provName}`]: [],
                    [`Thị xã ${provName}`]: [],
                    [`Huyện ${provName}`]: [],
                    [`Huyện Châu Thành`]: []
                };
            }

            const distList = Object.keys(districtsMap);
            const cleanKeyword = removeVietnameseTones(filterKeyword.trim());
            const filteredDistricts = distList.filter(d => removeVietnameseTones(d).includes(cleanKeyword));

            let html = '';
            if (filteredDistricts.length > 0) {
                filteredDistricts.forEach(dist => {
                    const isSelected = currentVal === dist;
                    html += `
                        <div onclick="selectFormDistrict('${dist}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-800'}">
                            <span>${dist}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            } else {
                html = `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy quận / huyện phù hợp</div>`;
            }

            container.innerHTML = html;
        }

        function selectFormDistrict(name) {
            const hiddenInput = document.getElementById('select-district');
            const textSpan = document.getElementById('form-district-text');
            if (hiddenInput) hiddenInput.value = name;
            if (textSpan) textSpan.innerText = name;

            const panel = document.getElementById('form-district-panel');
            const arrow = document.getElementById('form-district-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';

            onDistrictChange();
        }

        function filterFormDistricts() {
            const input = document.getElementById('form-district-search');
            const clearBtn = document.getElementById('form-clear-district-search-btn');
            const val = input ? input.value : '';

            if (clearBtn) {
                if (val.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            renderFormDistricts(val);
        }

        function clearFormDistrictSearch() {
            const input = document.getElementById('form-district-search');
            const clearBtn = document.getElementById('form-clear-district-search-btn');
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            renderFormDistricts('');
            if (input) input.focus();
        }

        function onCustomDistrictInput() {
            const customInput = document.getElementById('custom-district-input');
            const hiddenInput = document.getElementById('select-district');
            if (customInput && hiddenInput) {
                hiddenInput.value = customInput.value.trim();
                onDistrictChange();
            }
        }

        // ------------------ PHƯỜNG / XÃ ------------------
        let isCustomWardMode = false;

        function toggleCustomWardMode() {
            isCustomWardMode = !isCustomWardMode;
            const selectWrapper = document.getElementById('ward-select-wrapper');
            const customWrapper = document.getElementById('ward-custom-wrapper');
            const toggleText = document.getElementById('toggle-custom-ward-text');
            const customInput = document.getElementById('custom-ward-input');
            const hiddenInput = document.getElementById('select-ward');

            if (isCustomWardMode) {
                if (selectWrapper) selectWrapper.classList.add('hidden');
                if (customWrapper) customWrapper.classList.remove('hidden');
                if (toggleText) toggleText.innerText = 'Chọn từ danh sách';
                if (customInput) {
                    customInput.focus();
                    if (hiddenInput) hiddenInput.value = customInput.value.trim() || 'Phường Bến Nghé';
                }
            } else {
                if (selectWrapper) selectWrapper.classList.remove('hidden');
                if (customWrapper) customWrapper.classList.add('hidden');
                if (toggleText) toggleText.innerText = 'Tự nhập khác';
                const wardText = document.getElementById('form-ward-text')?.innerText || 'Phường Bến Nghé';
                if (hiddenInput) hiddenInput.value = wardText;
            }
        }

        function toggleFormWardDropdown(event) {
            if (event) event.stopPropagation();
            const panel = document.getElementById('form-ward-panel');
            const arrow = document.getElementById('form-ward-arrow');
            const searchInput = document.getElementById('form-ward-search');

            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                document.getElementById('form-province-panel')?.classList.add('hidden');
                document.getElementById('form-district-panel')?.classList.add('hidden');

                panel.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
                renderFormWards();
                setTimeout(() => {
                    if (searchInput) searchInput.focus();
                }, 50);
            } else {
                panel.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function renderFormWards(filterKeyword = '') {
            const container = document.getElementById('form-wards-list');
            const hiddenInput = document.getElementById('select-ward');
            const currentVal = hiddenInput ? hiddenInput.value : '';
            const provName = document.getElementById('select-province')?.value || 'TP. Hồ Chí Minh';
            const distName = document.getElementById('select-district')?.value || '';

            if (!container) return;

            let wardsList = [];
            if (vnLocationData[provName] && vnLocationData[provName][distName]) {
                wardsList = vnLocationData[provName][distName];
            } else if (distName.startsWith('Huyện')) {
                wardsList = ["Thị trấn Trung Tâm", "Xã Tân Lập", "Xã Hòa Phú", "Xã Mỹ Thạnh", "Xã Đông Bình"];
            } else if (distName.startsWith('Thị xã')) {
                wardsList = ["Phường 1", "Phường 2", "Phường Hòa Bình", "Xã An Bình", "Xã Tân Hòa"];
            } else {
                wardsList = ["Phường 1", "Phường 2", "Phường 3", "Phường Tân Phú", "Phường Trung Tâm"];
            }

            const cleanKeyword = removeVietnameseTones(filterKeyword.trim());
            const filteredWards = wardsList.filter(w => removeVietnameseTones(w).includes(cleanKeyword));

            let html = '';
            if (filteredWards.length > 0) {
                filteredWards.forEach(ward => {
                    const isSelected = currentVal === ward;
                    html += `
                        <div onclick="selectFormWard('${ward}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-800'}">
                            <span>${ward}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            } else {
                html = `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy phường / xã phù hợp</div>`;
            }

            container.innerHTML = html;
        }

        function selectFormWard(name) {
            const hiddenInput = document.getElementById('select-ward');
            const textSpan = document.getElementById('form-ward-text');
            if (hiddenInput) hiddenInput.value = name;
            if (textSpan) textSpan.innerText = name;

            const panel = document.getElementById('form-ward-panel');
            const arrow = document.getElementById('form-ward-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }

        function filterFormWardSearch() {
            const input = document.getElementById('form-ward-search');
            const clearBtn = document.getElementById('form-clear-ward-search-btn');
            const val = input ? input.value : '';

            if (clearBtn) {
                if (val.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            renderFormWards(val);
        }

        function filterFormWards() {
            filterFormWardSearch();
        }

        function clearFormWardSearch() {
            const input = document.getElementById('form-ward-search');
            const clearBtn = document.getElementById('form-clear-ward-search-btn');
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            renderFormWards('');
            if (input) input.focus();
        }

        function onCustomWardInput() {
            const customInput = document.getElementById('custom-ward-input');
            const hiddenInput = document.getElementById('select-ward');
            if (customInput && hiddenInput) {
                hiddenInput.value = customInput.value.trim();
            }
        }

        document.addEventListener('click', function (event) {
            const provWrapper = document.getElementById('province-select-wrapper');
            const provPanel = document.getElementById('form-province-panel');
            const provArrow = document.getElementById('form-province-arrow');

            if (provWrapper && provPanel && !provWrapper.contains(event.target)) {
                provPanel.classList.add('hidden');
                if (provArrow) provArrow.style.transform = 'rotate(0deg)';
            }

            const distWrapper = document.getElementById('district-select-wrapper');
            const distPanel = document.getElementById('form-district-panel');
            const distArrow = document.getElementById('form-district-arrow');

            if (distWrapper && distPanel && !distWrapper.contains(event.target)) {
                distPanel.classList.add('hidden');
                if (distArrow) distArrow.style.transform = 'rotate(0deg)';
            }

            const wardWrapper = document.getElementById('ward-select-wrapper');
            const wardPanel = document.getElementById('form-ward-panel');
            const wardArrow = document.getElementById('form-ward-arrow');

            if (wardWrapper && wardPanel && !wardWrapper.contains(event.target)) {
                wardPanel.classList.add('hidden');
                if (wardArrow) wardArrow.style.transform = 'rotate(0deg)';
            }
        });
    </script>
</body>

</html>
