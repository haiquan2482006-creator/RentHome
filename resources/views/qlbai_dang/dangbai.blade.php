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
    if ($currentUser && $currentUser->isIndividual()) {
        header('Location: ' . url('/'));
        exit;
    }
    $accountType = $currentUser->role ?? 'canhan';
    $isEnterprise = $currentUser && $currentUser->isEnterprise();
    $isSubAccount = $currentUser && $currentUser->isSubAccount();
    $displayName = $currentUser ? $currentUser->display_name : 'Quản lý';
    $badgeText = ($isEnterprise && !$isSubAccount) ? 'Doanh Nghiệp RentHome' : 'Quản Lý Tòa Nhà';
    $subTitle = ($isEnterprise && !$isSubAccount) ? 'RentHome Quản Trị' : 'Tài Khoản Phân Quyền';
    $preselectedBuildingId = request()->query('building_id', '');
@endphp

    <!-- Modal Container -->
    <div
        class="bg-white rounded-3xl max-w-2xl w-full my-auto shadow-2xl border border-slate-100 overflow-visible flex flex-col">

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
            <div class="p-6 space-y-4 flex-1 min-h-0 overflow-visible">
                <!-- Chọn Tòa Nhà Trực Thuộc -->
                <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200/80 mb-2">
                    <label class="block text-xs font-bold text-purple-900 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-city text-purple-600"></i> Chọn Tòa Nhà Trực Thuộc
                            @if($isSubAccount)
                                <span class="text-rose-500">*</span>
                            @else
                                <span class="text-slate-400 font-normal">(Tùy chọn)</span>
                            @endif
                        </span>
                        @if($isSubAccount)
                            <span class="text-[10px] font-bold text-purple-700 bg-purple-100 px-2.5 py-0.5 rounded-full border border-purple-200">
                                <i class="fa-solid fa-lock mr-1"></i>Chỉ tòa nhà được phân công
                            </span>
                        @else
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                <i class="fa-solid fa-crown mr-1"></i>Doanh nghiệp RentHome
                            </span>
                        @endif
                    </label>

                    <div class="relative">
                        <select id="select-building-id" name="building_id" class="w-full px-4 py-3 rounded-xl bg-white border border-purple-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-purple-400 focus:outline-none appearance-none cursor-pointer" onchange="onBuildingSelected(this)">
                            @if($isSubAccount)
                                <option value="" data-address="" data-type="">-- Chọn tòa nhà bạn được giao quản lý --</option>
                            @else
                                <option value="" data-address="" data-type="nha_o">-- Không thuộc tòa nhà nào (Đăng nhà riêng lẻ) --</option>
                            @endif
                            @foreach($buildings as $b)
                                @php
                                    $bFullAddr = implode(', ', array_filter([$b->address_detail, $b->ward, $b->district, $b->province]));
                                    $bAmenities = is_array($b->amenities) ? $b->amenities : (is_string($b->amenities) ? json_decode($b->amenities, true) : []);
                                    $bAmenities = is_array($bAmenities) ? array_values(array_filter($bAmenities)) : [];
                                    if (empty($bAmenities)) {
                                        $bAmenities = ['Thang máy', 'Bảo vệ 24/7', 'Hầm để xe', 'Camera an ninh'];
                                    }
                                @endphp
                                <option value="{{ $b->_id ?? $b->id }}" 
                                    data-address="{{ $bFullAddr ?: ($b->address_detail ?? '') }}"
                                    data-name="{{ $b->name }}"
                                    data-type="{{ $b->type ?? $b->business_type ?? $b->property_group ?? 'nha_o' }}"
                                    data-business-type="{{ $b->business_type ?? ($b->type ?? '') }}"
                                    data-floors="{{ $b->total_floors ?? $b->floors ?? 0 }}"
                                    data-amenities="{{ json_encode($bAmenities, JSON_UNESCAPED_UNICODE) }}"
                                    data-elec="{{ $b->electricity_price ?? '' }}"
                                    data-water="{{ $b->water_price ?? '' }}"
                                    data-water-type="{{ $b->water_calc_type ?? 'm3' }}"
                                    data-service="{{ $b->service_fee ?? '' }}"
                                    data-internet="{{ $b->internet_fee ?? '' }}"
                                    data-parking="{{ $b->parking_fee ?? '' }}"
                                    {{ ($preselectedBuildingId == ($b->_id ?? $b->id)) ? 'selected' : '' }}>
                                    🏢 {{ $b->name }} ({{ $b->province }} - {{ $b->total_rooms ?? 0 }} phòng)
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>

                    <!-- Preview Card địa chỉ tòa nhà -->
                    <div id="building-address-preview" class="d-none hidden mt-3.5 p-4 rounded-2xl bg-white border border-purple-200/90 text-slate-800 shadow-xs transition-all">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5 shadow-xs text-base">
                                📍
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <h4 class="font-extrabold text-slate-900 text-xs truncate" id="preview-building-name"></h4>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span id="preview-building-floors-badge" class="hidden px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] border border-amber-200 whitespace-nowrap"><i class="fa-solid fa-layer-group text-[9px] mr-1"></i><span id="preview-building-floors-text"></span></span>
                                        <span id="preview-building-type-badge" class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-bold text-[10px] shrink-0 whitespace-nowrap"></span>
                                    </div>
                                </div>
                                <div class="text-xs text-slate-700 font-medium leading-relaxed flex items-center gap-1.5">
                                    <span class="text-rose-500 font-bold shrink-0 text-sm">📍</span>
                                    <span id="preview-building-address" class="break-words font-semibold text-slate-800"></span>
                                </div>
                                <div class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 inline-flex">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Hệ thống tự động kế thừa 100% địa chỉ từ tòa nhà đã chọn</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isSubAccount)
                        <p class="text-[11px] text-purple-700 font-medium mt-3 flex items-center gap-1">
                            <i class="fa-solid fa-shield-halved"></i> Bạn chỉ có thể quản lý và đăng bài cho các tòa nhà do Doanh nghiệp RentHome phân quyền.
                        </p>
                    @else
                        <p class="text-[11px] text-purple-700 font-medium mt-3 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info"></i> Vị trí bài đăng sẽ tự động lấy theo địa chỉ hồ sơ của tòa nhà đã chọn.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-white rounded-b-3xl shrink-0">
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
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
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
            <div class="p-6 space-y-4 flex-1 min-h-0 overflow-visible">
                <!-- Tiêu đề bài đăng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tiêu đề bài đăng <span class="text-rose-500">*</span></label>
                    <input type="text" id="post-title" placeholder="VD: Bán căn hộ cao cấp 2 phòng ngủ Quận 1"
                        class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- Thông tin Loại tài khoản đăng tin -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tài khoản đăng tin</label>
                    <div class="p-3.5 rounded-2xl border {{ ($isEnterprise && !$isSubAccount) ? 'border-purple-200 bg-purple-50/40' : 'border-emerald-200 bg-emerald-50/40' }} flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl {{ ($isEnterprise && !$isSubAccount) ? 'bg-purple-600 text-white' : 'bg-emerald-600 text-white' }} flex items-center justify-center text-sm font-bold shadow-xs shrink-0">
                                <i class="{{ ($isEnterprise && !$isSubAccount) ? 'fa-solid fa-building-user' : 'fa-solid fa-user-check' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-extrabold text-slate-900 truncate">{{ $displayName }}</h4>
                                <p class="text-[11px] font-medium text-slate-500 truncate">{{ $subTitle }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap {{ ($isEnterprise && !$isSubAccount) ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                            {{ $badgeText }}
                        </span>
                    </div>
                    <input type="hidden" name="account_role" value="{{ $accountType }}">
                </div>

                <!-- Tòa nhà trực thuộc đã chọn (Hiển thị tóm tắt ở Bước 2) -->
                <div id="step2-building-summary" class="hidden p-3.5 rounded-2xl bg-purple-50/70 border border-purple-200/90 text-xs shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <i class="fa-solid fa-building text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="font-extrabold text-purple-950 text-xs truncate" id="step2-building-name"></p>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="step2-building-floors-badge" class="hidden px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] border border-amber-200 whitespace-nowrap"><i class="fa-solid fa-layer-group text-[9px] mr-1"></i><span id="step2-building-floors-text"></span></span>
                                    <span id="step2-building-type-badge" class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700 font-bold text-[10px] border border-purple-200 shrink-0 whitespace-nowrap"></span>
                                </div>
                            </div>
                            <p class="text-[11px] text-purple-800/90 font-medium leading-relaxed break-words" id="step2-building-address"></p>
                        </div>
                    </div>
                </div>

                <!-- Input Hidden chứa Loại hình BĐS (Được đồng bộ tự động từ Tòa nhà đã chọn) -->
                <input type="hidden" name="property_type" id="hidden_property_type" value="nha_o">

                <!-- Giá, Diện tích & Vị trí tầng (Cân đối tỉ lệ 5 - 3 - 4, đồng bộ chiều cao tuyệt đối) -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                    <!-- 1. Giá thuê mong muốn (5 cols) -->
                    <div class="sm:col-span-5 transition-all" id="price-col">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 truncate h-5 leading-5" id="price-label-text">
                            Giá thuê mong muốn <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center shadow-xs rounded-2xl bg-white border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500 transition-all relative h-11">
                            <!-- Ô nhập giá -->
                            <input type="text" id="post-price" placeholder="VD: 15.000.000"
                                oninput="formatPriceInput(this)" onblur="validatePriceInput(this)"
                                class="w-full pl-3.5 pr-2 py-2.5 h-full text-xs font-medium text-slate-800 focus:outline-none bg-transparent">
                            <!-- Đường kẻ dọc -->
                            <div class="w-px h-6 bg-slate-200 shrink-0"></div>
                            <!-- Dropdown chọn chu kỳ -->
                            <select id="post-price-unit" class="bg-slate-50 hover:bg-slate-100 text-amber-700 font-bold text-[11px] h-full pl-2.5 pr-6 focus:outline-none cursor-pointer border-none outline-none appearance-none relative shrink-0">
                                <option value="thang">/ tháng</option>
                                <option value="nam">/ năm</option>
                                <option value="tong">Tổng tiền</option>
                                <option value="m2">/ m²</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[9px] pointer-events-none"></i>
                        </div>
                        <p id="price-helper-text" class="text-[11px] mt-1 text-slate-500 font-semibold h-4 leading-4 truncate"></p>
                    </div>

                    <!-- 2. Diện tích (3 cols) -->
                    <div class="sm:col-span-3 transition-all" id="area-col">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 truncate h-5 leading-5">
                            Diện tích (m²) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative h-11">
                            <input type="number" id="post-area" placeholder="VD: 85"
                                class="w-full pl-3.5 pr-9 py-2.5 h-11 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-amber-600 text-xs pointer-events-none">m²</span>
                        </div>
                        <p class="h-4 mt-1"></p>
                    </div>

                    <!-- 3. Số tầng (4 cols - rộng rãi, không bị tràn dòng) -->
                    <div class="sm:col-span-4 transition-all" id="floor-col">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between gap-1 h-5 leading-5">
                            <span class="truncate" id="floor-label-title">Vị trí tầng</span>
                            <span id="floor-hint" class="text-slate-400 font-normal text-[10px] shrink-0 whitespace-nowrap leading-none">(Tùy chọn)</span>
                        </label>
                        <div class="relative h-11">
                            <input type="text" id="post-floor" placeholder="VD: 3"
                                class="w-full pl-3.5 pr-14 py-2.5 h-11 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all">
                            <div id="floor-lock-badge" class="hidden absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-slate-400 pointer-events-none select-none">
                                <span class="font-bold text-slate-500 text-[11px]">tầng</span>
                                <i class="fa-solid fa-lock text-[10px] text-amber-500"></i>
                            </div>
                        </div>
                        <p id="floor-helper-note" class="text-[11px] mt-1 text-slate-500 font-medium h-4 leading-4 truncate"></p>
                    </div>
                </div>

                <!-- Số phòng ngủ & Số phòng tắm -->
                <div id="room-counter-box" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Số phòng ngủ</label>
                        <div class="flex items-center justify-between px-3 py-2 rounded-2xl border border-slate-200 bg-white">
                            <button type="button" onclick="stepCount('bedroom', -1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">-</button>
                            <span id="bedroom-count" class="font-bold text-xs text-slate-800">1</span>
                            <button type="button" onclick="stepCount('bedroom', 1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">+</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Số phòng tắm</label>
                        <div class="flex items-center justify-between px-3 py-2 rounded-2xl border border-slate-200 bg-white">
                            <button type="button" onclick="stepCount('bathroom', -1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">-</button>
                            <span id="bathroom-count" class="font-bold text-xs text-slate-800">1</span>
                            <button type="button" onclick="stepCount('bathroom', 1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-600 flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>

                <!-- Tiện ích & Đặc điểm nổi bật -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i> Tiện ích & Đặc điểm nổi bật
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Kế thừa tòa nhà & Tùy chọn riêng</span>
                    </label>

                    <!-- Khối Bảng giá dịch vụ dùng chung từ Tòa nhà (Kế thừa tự động - Chỉ xem) -->
                    <div id="building-service-prices-box" class="hidden mb-3 p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/90 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold text-amber-950 flex items-center gap-1.5">
                                <i class="fa-solid fa-file-invoice-dollar text-amber-600"></i> Bảng giá dịch vụ dùng chung từ Tòa nhà
                            </span>
                            <span class="text-[10px] text-amber-800 font-bold bg-amber-100/90 px-2.5 py-0.5 rounded-full border border-amber-200 shrink-0 flex items-center gap-1">
                                <i class="fa-solid fa-lock text-[9px]"></i> Kế thừa tự động (Cố định)
                            </span>
                        </div>
                        <div id="building-service-prices-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-2.5">
                            <!-- Populated dynamically via JS -->
                        </div>
                        <p class="text-[11px] text-amber-800/80 leading-relaxed font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-amber-600 text-[10px] shrink-0"></i>
                            <span>Các đơn giá dịch vụ trên được cấu hình cố định từ Tòa nhà (Lớp Vỏ) và tự động áp dụng cho bài đăng này.</span>
                        </p>
                    </div>

                    <!-- Khối 1: Tiện ích dùng chung kế thừa từ Tòa nhà (Read-only) -->
                    <div id="building-amenities-box" class="hidden mb-3 p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/90 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-xs font-bold text-blue-950 flex items-center gap-1.5">
                                <i class="fa-solid fa-building-circle-check text-blue-600"></i> Tiện ích dùng chung từ Tòa nhà
                            </span>
                            <span class="text-[10px] text-blue-700 font-bold bg-blue-100/90 px-2.5 py-0.5 rounded-full border border-blue-200 shrink-0 flex items-center gap-1">
                                <i class="fa-solid fa-lock text-[9px]"></i> Kế thừa tự động (Cố định)
                            </span>
                        </div>
                        <p class="text-[11px] text-blue-800/80 mb-2 leading-relaxed font-medium">
                            Các tiện ích này được áp dụng chung cho toàn bộ phòng trong tòa nhà đã chọn:
                        </p>
                        <div id="building-amenities-tags" class="flex flex-wrap gap-1.5">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Khối 2: Tiện ích riêng của từng phòng (Room-specific) -->
                    <div class="mb-2">
                        <p class="text-[11px] font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-bed text-emerald-600"></i> Chọn nhanh tiện ích riêng của phòng:
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="addAmenityTag('Có Ban công')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Có Ban công
                            </button>
                            <button type="button" onclick="addAmenityTag('Có Gác xép (Mezzanine)')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Có Gác xép (Mezzanine)
                            </button>
                            <button type="button" onclick="addAmenityTag('Cửa sổ lớn / View thoáng')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Cửa sổ lớn / View thoáng
                            </button>
                            <button type="button" onclick="addAmenityTag('Nội thất đầy đủ')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Nội thất đầy đủ
                            </button>
                            <button type="button" onclick="addAmenityTag('Nội thất cơ bản')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Nội thất cơ bản
                            </button>
                            <button type="button" onclick="addAmenityTag('Cho phép nuôi Pet')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Cho phép nuôi Pet
                            </button>
                            <button type="button" onclick="addAmenityTag('Máy giặt riêng')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-700 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px] text-emerald-600"></i> Máy giặt riêng
                            </button>
                        </div>
                    </div>

                    <textarea id="amenities-input" name="amenities" rows="2.5"
                        placeholder="Nhập thêm các tiện ích riêng của phòng (VD: Có Ban công, cửa sổ lớn, máy giặt riêng, cho nuôi pet...)"
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
                        <label class="block text-xs font-bold text-slate-700">Hình ảnh thực tế bất động sản <span class="text-rose-500">*</span></label>
                        <span id="image-count-badge" class="text-[11px] font-bold text-emerald-600 hidden">Đã chọn: 0/10 ảnh</span>
                    </div>

                    <label for="property-images-input"
                        class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-emerald-50/30 cursor-pointer block transition-all group">
                        <input type="file" id="property-images-input" name="images[]" multiple
                            accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden"
                            onchange="handleImageUpload(event)">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto transition-colors">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <p class="text-xs text-slate-700 mt-2 font-bold group-hover:text-emerald-700 transition-colors">
                            Nhấp vào đây để chọn ảnh từ máy tính của bạn</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Hỗ trợ JPG, PNG, WEBP (Tối đa 10 ảnh, mỗi ảnh ≤ 5MB)</p>
                    </label>

                    <div id="image-preview-container" class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-3 hidden">
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-white rounded-b-3xl shrink-0">
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
 const API_BASE = 'https://esgoo.net/api-tinhthanh';

        // Khai báo các biến dùng chung cho API (Chống lỗi undefined)
        let provincesList = [];
        let currentDistrictsList = [];
        let currentWardsList = [];
        let selectedProvinceCode = null;
        let selectedDistrictCode = null;
        let editPostId = null;

        document.addEventListener('DOMContentLoaded', () => {
            const editDataRaw = sessionStorage.getItem('edit_post_data');
            if (editDataRaw) {
                try {
                    const post = JSON.parse(editDataRaw);
                    editPostId = post.id;
                    
                    if(post.price) {
                        const priceInput = document.getElementById('post-price');
                        if(priceInput) {
                            priceInput.value = post.price;
                            formatPriceInput(priceInput);
                        }
                    }
                    if(post.price_unit) {
                        const unitSelect = document.getElementById('post-price-unit');
                        if(unitSelect) unitSelect.value = post.price_unit;
                    }
                    if(post.area) {
                        const areaInput = document.getElementById('post-area');
                        if(areaInput) areaInput.value = post.area;
                    }
                    const savedFloor = (post.floor !== undefined && post.floor !== null) ? post.floor : post.floor_number;
                    if(savedFloor !== undefined && savedFloor !== null && savedFloor !== '') {
                        const floorInput = document.getElementById('post-floor');
                        if(floorInput) floorInput.value = savedFloor;
                    }
                    if(post.bedroom) {
                        const bedSpan = document.getElementById('bedroom-count');
                        if(bedSpan) bedSpan.innerText = post.bedroom;
                    }
                    if(post.bathroom) {
                        const bathSpan = document.getElementById('bathroom-count');
                        if(bathSpan) bathSpan.innerText = post.bathroom;
                    }
                    if(post.amenities) {
                        const amenitiesInput = document.getElementById('amenities-input');
                        if(amenitiesInput) amenitiesInput.value = post.amenities;
                    }
                    if(post.description) {
                        const descInput = document.getElementById('post-description');
                        if(descInput) descInput.value = post.description;
                    }

                    if(post.property_type) {
                        const hiddenTypeInput = document.getElementById('hidden_property_type');
                        if (hiddenTypeInput) hiddenTypeInput.value = post.property_type;
                        if(typeof togglePropertyType === 'function') {
                            togglePropertyType(post.property_type);
                        }
                    }

                    if (post.province || post.district || post.ward) {
                        window.autoSelectLocation = {
                            province: post.province,
                            district: post.district,
                            ward: post.ward
                        };
                    }

                    if (post.address_detail) {
                        const detailInput = document.getElementById('address-detail-input');
                        if (detailInput) detailInput.value = post.address_detail;
                    }

                    if (typeof goToStepDetails === 'function') {
                        goToStepDetails();
                    }

                } catch (e) {
                    console.error('Lỗi parse edit_post_data:', e);
                }
            }
            fetchProvinces();
        });

        // Hàm khử dấu tiếng Việt (Sửa lỗi crash khi tìm kiếm)
        function removeVietnameseTones(str) {
            if (!str) return '';
            return str.normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .toLowerCase();
        }

        // ======================= API FETCHING =======================
        async function fetchProvinces() {
            if (!document.getElementById('select-province')) return;
            try {
                const res = await fetch(`${API_BASE}/1/0.htm`);
                const json = await res.json();
                if(json.error === 0) {
                    provincesList = json.data.map(p => ({ code: p.id, name: p.full_name || p.name }));
                    
                    const currentProvVal = document.getElementById('select-province')?.value;
                    if (currentProvVal) {
                        const cleanProv = removeVietnameseTones(currentProvVal);
                        const matchedProv = provincesList.find(p => {
                            const cleanP = removeVietnameseTones(p.name);
                            return cleanP.includes(cleanProv) || cleanProv.includes(cleanP);
                        });
                        if (matchedProv) {
                            selectedProvinceCode = matchedProv.code;
                            const currentDistVal = document.getElementById('select-district')?.value;
                            const currentWardVal = document.getElementById('select-ward')?.value;
                            if (currentDistVal || currentWardVal) {
                                window.autoSelectLocation = { district: currentDistVal, ward: currentWardVal };
                            }
                            fetchDistricts(matchedProv.code);
                            return;
                        }
                    }

                    // Mặc định chọn TP. Hồ Chí Minh nếu chưa có tỉnh nào được chọn
                    const defaultProv = provincesList.find(p => p.name.includes('Hồ Chí Minh')) || provincesList[0];
                    if (defaultProv) {
                        selectFormProvince(defaultProv.name, defaultProv.code);
                    }
                } else {
                    throw new Error('API returned error');
                }
            } catch (err) {
                console.error('Lỗi tải danh sách Tỉnh/Thành:', err);
                const provText = document.getElementById('form-province-text');
                if (provText && (!provText.innerText || provText.innerText === 'Đang tải danh sách...')) {
                    provText.innerText = 'Chọn Tỉnh / Thành Phố';
                }
            }
        }

        async function fetchDistricts(provinceCode) {
            if (!provinceCode) {
                resetDistrict();
                resetWard();
                return;
            }
            try {
                const res = await fetch(`${API_BASE}/2/${provinceCode}.htm`);
                const json = await res.json();
                if(json.error === 0) {
                    // 1. Cập nhật mảng dữ liệu Quận/Huyện mới
                    currentDistrictsList = json.data.map(d => ({ code: d.id, name: d.full_name || d.name }));
                    
                    // 2. Reset lại hiển thị của ô chọn Quận/Huyện
                    document.getElementById('form-district-text').innerText = 'Chọn Quận / Huyện';
                    document.getElementById('select-district').value = '';
                    
                    // 3. QUAN TRỌNG: Vẽ lại danh sách HTML mới
                    renderFormDistricts(); 
                    
                    if (window.autoSelectLocation && window.autoSelectLocation.district) {
                        const targetDist = removeVietnameseTones(window.autoSelectLocation.district);
                        const dist = currentDistrictsList.find(d => {
                            const cleanD = removeVietnameseTones(d.name);
                            return cleanD.includes(targetDist) || targetDist.includes(cleanD);
                        });
                        if (dist) {
                            selectFormDistrict(dist.name, dist.code);
                        }
                    }
                    
                    // 4. Xóa trắng danh sách Phường/Xã cũ
                    resetWard(); 
                } else {
                    throw new Error('API returned error');
                }
            } catch (err) {
                console.error('Lỗi tải danh sách Quận/Huyện:', err);
            }
        }

        async function fetchWards(districtCode) {
            if (!districtCode) {
                resetWard();
                return;
            }
            try {
                const res = await fetch(`${API_BASE}/3/${districtCode}.htm`);
                const json = await res.json();
                if(json.error === 0) {
                    // 1. Cập nhật mảng dữ liệu Phường/Xã mới
                    currentWardsList = json.data.map(w => ({ code: w.id, name: w.full_name || w.name }));
                    
                    // 2. Reset lại hiển thị của ô chọn Phường/Xã
                    document.getElementById('form-ward-text').innerText = 'Chọn Phường / Xã';
                    document.getElementById('select-ward').value = '';
                    
                    // 3. QUAN TRỌNG: Vẽ lại danh sách HTML mới
                    renderFormWards(); 
                    
                    if (window.autoSelectLocation && window.autoSelectLocation.ward) {
                        const targetWard = removeVietnameseTones(window.autoSelectLocation.ward);
                        const ward = currentWardsList.find(w => {
                            const cleanW = removeVietnameseTones(w.name);
                            return cleanW.includes(targetWard) || targetWard.includes(cleanW);
                        });
                        if (ward) {
                            selectFormWard(ward.name, ward.code);
                        }
                        window.autoSelectLocation = null;
                    }
                } else {
                    throw new Error('API returned error');
                }
            } catch (err) {
                console.error('Lỗi tải danh sách Phường/Xã:', err);
            }
        }

        function resetDistrict() {
            selectedDistrictCode = null;
            document.getElementById('select-district').value = '';
            document.getElementById('form-district-text').innerText = 'Chọn Quận / Huyện';
            currentDistrictsList = [];
            renderFormDistricts();
        }

        function resetWard() {
            document.getElementById('select-ward').value = '';
            document.getElementById('form-ward-text').innerText = 'Chọn Phường / Xã';
            currentWardsList = [];
            renderFormWards();
        }

        // ======================= TỈNH / THÀNH PHỐ =======================
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
                    if (hiddenInput) hiddenInput.value = customInput.value.trim();
                }
            } else {
                if (selectWrapper) selectWrapper.classList.remove('hidden');
                if (customWrapper) customWrapper.classList.add('hidden');
                if (toggleText) toggleText.innerText = 'Tự nhập khác';
                const provinceText = document.getElementById('form-province-text')?.innerText || '';
                if (hiddenInput) hiddenInput.value = provinceText;
            }
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
            const currentVal = document.getElementById('select-province')?.value || '';
            if (!container) return;

            const cleanKeyword = removeVietnameseTones(filterKeyword);
            const filtered = provincesList.filter(p => removeVietnameseTones(p.name).includes(cleanKeyword));

            if (filtered.length === 0) {
                container.innerHTML = `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy tỉnh / thành phố phù hợp</div>`;
                return;
            }

            let html = '';
            filtered.forEach(prov => {
                const isSelected = currentVal === prov.name;
                html += `
                    <div onclick="selectFormProvince('${prov.name}', '${prov.code}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-800'}">
                        <span>${prov.name}</span>
                        ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        function selectFormProvince(name, code) {
            const hiddenInput = document.getElementById('select-province');
            const textSpan = document.getElementById('form-province-text');
            if (hiddenInput) hiddenInput.value = name;
            if (textSpan) textSpan.innerText = name;

            selectedProvinceCode = code;

            const panel = document.getElementById('form-province-panel');
            const arrow = document.getElementById('form-province-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';

            fetchDistricts(code);
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
            }
        }

        // ======================= QUẬN / HUYỆN =======================
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
                    if (hiddenInput) hiddenInput.value = customInput.value.trim();
                }
            } else {
                if (selectWrapper) selectWrapper.classList.remove('hidden');
                if (customWrapper) customWrapper.classList.add('hidden');
                if (toggleText) toggleText.innerText = 'Tự nhập khác';
                const districtText = document.getElementById('form-district-text')?.innerText || '';
                if (hiddenInput) hiddenInput.value = districtText;
            }
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
            const currentVal = document.getElementById('select-district')?.value || '';
            if (!container) return;

            const cleanKeyword = removeVietnameseTones(filterKeyword);
            const filtered = currentDistrictsList.filter(d => removeVietnameseTones(d.name).includes(cleanKeyword));

            if (filtered.length === 0) {
                container.innerHTML = `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy quận / huyện phù hợp</div>`;
                return;
            }

            let html = '';
            filtered.forEach(dist => {
                const isSelected = currentVal === dist.name;
                html += `
                    <div onclick="selectFormDistrict('${dist.name}', '${dist.code}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-800'}">
                        <span>${dist.name}</span>
                        ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        function selectFormDistrict(name, code) {
            const hiddenInput = document.getElementById('select-district');
            const textSpan = document.getElementById('form-district-text');
            if (hiddenInput) hiddenInput.value = name;
            if (textSpan) textSpan.innerText = name;

            selectedDistrictCode = code;

            const panel = document.getElementById('form-district-panel');
            const arrow = document.getElementById('form-district-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';

            fetchWards(code);
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
            }
        }

        // ======================= PHƯỜNG / XÃ =======================
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
                    if (hiddenInput) hiddenInput.value = customInput.value.trim();
                }
            } else {
                if (selectWrapper) selectWrapper.classList.remove('hidden');
                if (customWrapper) customWrapper.classList.add('hidden');
                if (toggleText) toggleText.innerText = 'Tự nhập khác';
                const wardText = document.getElementById('form-ward-text')?.innerText || '';
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
            const currentVal = document.getElementById('select-ward')?.value || '';
            if (!container) return;

            const cleanKeyword = removeVietnameseTones(filterKeyword);
            const filtered = currentWardsList.filter(w => removeVietnameseTones(w.name).includes(cleanKeyword));

            if (filtered.length === 0) {
                container.innerHTML = `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy phường / xã phù hợp</div>`;
                return;
            }

            let html = '';
            filtered.forEach(ward => {
                const isSelected = currentVal === ward.name;
                html += `
                    <div onclick="selectFormWard('${ward.name}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-amber-50/80 text-amber-600 font-bold' : 'text-slate-800'}">
                        <span>${ward.name}</span>
                        ${isSelected ? '<i class="fa-solid fa-check text-amber-600 text-xs"></i>' : ''}
                    </div>
                `;
            });
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

        function filterFormWards() {
            const input = document.getElementById('form-ward-search');
            const clearBtn = document.getElementById('form-clear-ward-search-btn');
            const val = input ? input.value : '';

            if (clearBtn) {
                if (val.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            renderFormWards(val);
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

        // ======================= CÁC HÀM TIỆN ÍCH FORM =======================
        function normalizeAddr(str) {
            if (!str) return '';
            return removeVietnameseTones(str)
                .toLowerCase()
                .replace(/^(thanh pho|tp\.?|tinh|quan|huyen|thi xa|tx\.?|phuong|xa|thi tran|tt\.?)\s+/g, '')
                .replace(/[\,\.\-\/]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function checkAddressMatch(inputVal, buildingVal) {
            if (!buildingVal) return true;
            if (!inputVal) return false;
            const nInput = normalizeAddr(inputVal);
            const nBld = normalizeAddr(buildingVal);
            if (!nBld) return true;
            if (nInput === nBld) return true;
            if (nInput.includes(nBld) || nBld.includes(nInput)) return true;
            return false;
        }

        function syncAddressFromBuilding() {
            const bSelect = document.getElementById('select-building-id');
            if (bSelect) {
                onBuildingSelected(bSelect);
                const warnBox = document.getElementById('address-mismatch-warning');
                if (warnBox) warnBox.classList.add('hidden');
            }
        }

        function goToStepDetails() {
            const bSelect = document.getElementById('select-building-id');
            const buildingId = bSelect ? bSelect.value : '';
            const isSubAccount = @json($isSubAccount);

            if (isSubAccount && !buildingId) {
                alert('Vui lòng chọn Tòa nhà được phân công quản lý trước khi tiếp tục!');
                bSelect?.focus();
                return;
            }

            document.getElementById('step-location').classList.add('hidden');
            document.getElementById('step-details').classList.remove('hidden');
        }

        function goToStepLocation() {
            document.getElementById('step-details').classList.add('hidden');
            document.getElementById('step-location').classList.remove('hidden');
        }

        function stepCount(type, delta) {
            const el = document.getElementById(type + '-count');
            let val = parseInt(el.innerText) + delta;
            if (val < 0) val = 0;
            el.innerText = val;
        }

        function togglePropertyType(type) {
            const roomBox = document.getElementById('room-counter-box');
            const isLand = (type === 'dat_nen' || type === 'dat_phan_lo');
            if (roomBox) {
                if (isLand) {
                    roomBox.classList.add('hidden');
                } else {
                    roomBox.classList.remove('hidden');
                }
            }
            
            // Logic tự động đổi chu kỳ giá và Label
            const unitSelect = document.getElementById('post-price-unit');
            const priceLabel = document.getElementById('price-label-text');
            if (unitSelect && priceLabel) {
                if (isLand) {
                    unitSelect.value = 'tong';
                    priceLabel.innerHTML = 'Giá bán mong muốn <span class="text-rose-500">*</span>';
                } else {
                    unitSelect.value = 'thang';
                    priceLabel.innerHTML = 'Giá thuê mong muốn <span class="text-rose-500">*</span>';
                }
            }
        }

        let currentBuildingAmenities = [];

        function renderBuildingServicePrices(selectedOption) {
            const box = document.getElementById('building-service-prices-box');
            const list = document.getElementById('building-service-prices-list');
            if (!box || !list) return;

            if (!selectedOption || !selectedOption.value) {
                box.classList.add('hidden');
                list.innerHTML = '';
                return;
            }

            // 1. Kiểm tra loại hình tòa nhà (data-type): ẩn nếu là dat_nen, dat_phan_lo, cum_nha_lien_ke
            const bType = (selectedOption.getAttribute('data-type') || '').trim().toLowerCase();
            const excludedTypes = ['dat_nen', 'dat_phan_lo', 'cum_nha_lien_ke'];
            if (excludedTypes.includes(bType)) {
                box.classList.add('hidden');
                list.innerHTML = '';
                return;
            }

            // 2. Lấy dữ liệu giá dịch vụ từ data-attributes
            const elecPrice = parseFloat(selectedOption.getAttribute('data-elec')) || 0;
            const waterPrice = parseFloat(selectedOption.getAttribute('data-water')) || 0;
            const waterType = (selectedOption.getAttribute('data-water-type') || 'm3').toLowerCase();
            const serviceFee = parseFloat(selectedOption.getAttribute('data-service')) || 0;
            const internetFee = parseFloat(selectedOption.getAttribute('data-internet')) || 0;
            const parkingFee = parseFloat(selectedOption.getAttribute('data-parking')) || 0;

            const formatter = new Intl.NumberFormat('vi-VN');
            const waterUnit = (waterType === 'person' || waterType === 'nguoi') ? 'đ/người' : 'đ/m³';

            const serviceItems = [
                { label: 'Điện', emoji: '⚡', val: elecPrice, unit: 'đ/kWh' },
                { label: 'Nước', emoji: '💧', val: waterPrice, unit: waterUnit },
                { label: 'Dịch vụ / Vệ sinh', emoji: '🧹', val: serviceFee, unit: 'đ/phòng' },
                { label: 'Wifi / Internet', emoji: '📶', val: internetFee, unit: 'đ/phòng' },
                { label: 'Gửi xe', emoji: '🛵', val: parkingFee, unit: 'đ/xe' },
            ];

            // Chỉ hiển thị các khoản phí có giá trị > 0
            const activeServices = serviceItems.filter(item => item.val > 0);

            // Nếu tất cả các mức giá đều trống hoặc bằng 0, ẩn khối
            if (activeServices.length === 0) {
                box.classList.add('hidden');
                list.innerHTML = '';
                return;
            }

            // Render danh sách pill/card dạng Read-only
            list.innerHTML = activeServices.map(item => `
                <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-white border border-amber-200/80 shadow-2xs">
                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="text-sm shrink-0 leading-none">${item.emoji}</span>
                        <span class="whitespace-nowrap">${item.label}:</span>
                    </span>
                    <span class="text-xs font-bold text-amber-950 bg-amber-100/80 px-2.5 py-1 rounded-lg border border-amber-200/70 whitespace-nowrap ml-2 font-mono shrink-0">
                        ${formatter.format(item.val)} <span class="font-sans font-semibold text-[10px] text-amber-800">${item.unit}</span>
                    </span>
                </div>
            `).join('');

            box.classList.remove('hidden');
        }

        function renderBuildingAmenities(amenitiesList) {
            const box = document.getElementById('building-amenities-box');
            const container = document.getElementById('building-amenities-tags');
            if (!box || !container) return;

            currentBuildingAmenities = Array.isArray(amenitiesList) ? amenitiesList : [];

            if (currentBuildingAmenities.length === 0) {
                box.classList.add('hidden');
                container.innerHTML = '';
                return;
            }

            box.classList.remove('hidden');
            container.innerHTML = currentBuildingAmenities.map(amenity => `
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-100/80 text-blue-800 text-xs font-bold border border-blue-200/90 shadow-2xs select-none">
                    <i class="fa-solid fa-circle-check text-blue-600 text-[11px]"></i>
                    <span>${amenity}</span>
                    <i class="fa-solid fa-lock text-blue-400 text-[9px] ml-0.5" title="Kế thừa từ tòa nhà (Mặc định có sẵn)"></i>
                </span>
            `).join('');
        }

        function addAmenityTag(text) {
            const input = document.getElementById('amenities-input');
            if (!input) return;
            let currentVal = input.value.trim();
            if (currentVal.includes(text)) return;
            if (currentBuildingAmenities.includes(text)) {
                alert(`"${text}" đã có sẵn trong danh sách tiện ích dùng chung kế thừa từ tòa nhà!`);
                return;
            }
            if (currentVal.length > 0) {
                input.value = currentVal + ', ' + text;
            } else {
                input.value = text;
            }
        }

        let uploadedFilesArray = [];
        let existingImagesArray = [];

        function handleImageUpload(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            files.forEach(file => {
                if ((uploadedFilesArray.length + existingImagesArray.length) < 10 && file.type.startsWith('image/')) {
                    uploadedFilesArray.push(file);
                }
            });

            renderImagePreviews();
        }

        function removeUploadedImage(index) {
            uploadedFilesArray.splice(index, 1);
            renderImagePreviews();
        }

        function removeExistingImage(index) {
            existingImagesArray.splice(index, 1);
            renderImagePreviews();
        }

        function getFullImageUrl(rawImg) {
            if (!rawImg) return '';
            if (rawImg.startsWith('http') || rawImg.startsWith('data:image')) return rawImg;
            return rawImg.startsWith('/') ? rawImg : '/storage/' + rawImg;
        }

        function renderImagePreviews() {
            const container = document.getElementById('image-preview-container');
            const badge = document.getElementById('image-count-badge');
            if (!container) return;

            container.innerHTML = '';
            const totalImages = uploadedFilesArray.length + existingImagesArray.length;

            if (totalImages > 0) {
                container.classList.remove('hidden');
                if (badge) {
                    badge.classList.remove('hidden');
                    badge.innerText = `Đã chọn: ${totalImages}/10 ảnh`;
                }
            } else {
                container.classList.add('hidden');
                if (badge) badge.classList.add('hidden');
                return;
            }

            // 1. Render ảnh cũ
            existingImagesArray.forEach((imgUrl, idx) => {
                const fullUrl = getFullImageUrl(imgUrl);
                const card = document.createElement('div');
                card.className = 'relative group aspect-square rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-100';

                card.innerHTML = `
                    <img src="${fullUrl}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeExistingImage(${idx})"
                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/70 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition-colors shadow-sm"
                        title="Xóa ảnh này">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="absolute bottom-0 inset-x-0 bg-slate-900/60 backdrop-blur-xs p-1 text-[9px] text-white font-medium truncate text-center">
                        Ảnh đã lưu
                    </div>
                `;
                container.appendChild(card);
            });

            // 2. Render ảnh mới
            uploadedFilesArray.forEach((file, idx) => {
                const fileUrl = URL.createObjectURL(file);
                const card = document.createElement('div');
                card.className = 'relative group aspect-square rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-100';

                card.innerHTML = `
                    <img src="${fileUrl}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeUploadedImage(${idx})"
                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/70 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition-colors shadow-sm"
                        title="Xóa ảnh mới này">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="absolute bottom-0 inset-x-0 bg-slate-900/60 backdrop-blur-xs p-1 text-[9px] text-white font-medium truncate text-center text-emerald-400">
                        ${file.name}
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function submitPost() {
            const titleInput = document.getElementById('post-title');
            const title = titleInput ? titleInput.value.trim() : '';

            if (!title) {
                alert('Vui lòng nhập tiêu đề bài đăng!');
                if (titleInput) titleInput.focus();
                return;
            }

            const priceInput = document.getElementById('post-price');
            const areaInput = document.getElementById('post-area');
            
            if (!priceInput || !priceInput.value.trim()) {
                alert('Vui lòng nhập giá cho thuê!');
                return;
            }
            const isSubAccount = @json($isSubAccount);
            const buildingId = document.getElementById('select-building-id')?.value || '';
            if (isSubAccount && !buildingId) {
                alert('Vui lòng chọn Tòa nhà được phân công quản lý trước khi đăng bài!');
                document.getElementById('select-building-id')?.focus();
                return;
            }

            const formData = new FormData();
            formData.append('title', title);

            if (buildingId) {
                formData.append('building_id', buildingId);
            }
            
            // Lấy giá thực (không có dấu chấm)
            let price = priceInput.value.replace(/\./g, '').trim();
            formData.append('price', price);
            
            const priceUnit = document.getElementById('post-price-unit')?.value || 'thang';
            formData.append('price_unit', priceUnit);
            
            formData.append('area', areaInput.value.trim());

            // Số tầng / Vị trí tầng của phòng / căn hộ
            const floorInput = document.getElementById('post-floor');
            if (floorInput && floorInput.value !== '') {
                const floorVal = floorInput.value.trim();
                formData.append('floor', floorVal);
                formData.append('floor_number', floorVal);
            }

            // Số phòng ngủ & phòng tắm (nếu có)
            const bedCount = document.getElementById('bedroom-count')?.innerText?.trim();
            if (bedCount) formData.append('bedroom', bedCount);
            const bathCount = document.getElementById('bathroom-count')?.innerText?.trim();
            if (bathCount) formData.append('bathroom', bathCount);

            // Địa chỉ kế thừa trực tiếp từ Tòa nhà đã chọn
            const bSelectEl = document.getElementById('select-building-id');
            const selectedOpt = bSelectEl ? bSelectEl.options[bSelectEl.selectedIndex] : null;
            if (selectedOpt && selectedOpt.value) {
                const bAddr = selectedOpt.getAttribute('data-address') || '';
                formData.append('address', bAddr);
            }
            
            // Get property type từ hidden input
            const propertyType = document.getElementById('hidden_property_type')?.value || 'nha_o';
            formData.append('property_type', propertyType);

            formData.append('description', document.getElementById('post-description')?.value.trim() || '');
            
            // account_role
            const accountRole = document.querySelector('input[name="account_role"]')?.value || 'canhan';
            formData.append('account_role', accountRole);

            // Edit logic: send post_id
            const editIdInput = document.getElementById('edit-post-id');
            if (editIdInput && editIdInput.value) {
                formData.append('post_id', editIdInput.value);
            }

            // Amenities: Kết hợp Tiện ích Kế thừa từ Tòa nhà VÀ Tiện ích riêng của phòng
            const roomAmenitiesStr = document.getElementById('amenities-input')?.value.trim() || '';
            const roomAmenitiesArr = roomAmenitiesStr.split(',').map(s => s.trim()).filter(Boolean);
            const combinedAmenities = Array.from(new Set([...(currentBuildingAmenities || []), ...roomAmenitiesArr]));
            combinedAmenities.forEach((am, idx) => {
                formData.append(`amenities[${idx}]`, am);
            });

            // Images
            if (typeof uploadedFilesArray !== 'undefined' && uploadedFilesArray.length > 0) {
                uploadedFilesArray.forEach((file) => {
                    formData.append('images[]', file);
                });
            }

            if (typeof existingImagesArray !== 'undefined' && existingImagesArray.length > 0) {
                existingImagesArray.forEach((url) => {
                    formData.append('existing_images[]', url);
                });
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Hiệu ứng Loading nút bấm
            const btn = document.querySelector('button[onclick="submitPost()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;

            // Đẩy API
            fetch('/api/posts', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                if (data.success) {
                    sessionStorage.removeItem('edit_post_data');
                    alert(data.message || 'Đã gửi bài đăng thành công! Bài viết đang chờ Kiểm duyệt viên phê duyệt.');
                    
                    let targetUrl = '/Overview?tab=pending';
                    if (data.redirect_url) {
                        try {
                            if (data.redirect_url.startsWith('http://') || data.redirect_url.startsWith('https://')) {
                                const parsed = new URL(data.redirect_url);
                                targetUrl = parsed.pathname + parsed.search + parsed.hash;
                            } else {
                                targetUrl = data.redirect_url;
                            }
                        } catch (e) {
                            targetUrl = '/Overview?tab=pending';
                        }
                    }
                    window.location.href = targetUrl;
                } else {
                    alert(data.error || data.message || 'Có lỗi xảy ra khi đăng bài!');
                }
            })
            .catch(err => {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Không thể kết nối đến server. Vui lòng thử lại sau.');
            });
        }

        function formatPriceInput(input) {
            let val = input.value.replace(/\D/g, '');
            input.value = val;
            
            const helper = document.getElementById('price-helper-text');
            if (!helper) return;

            if (val === '') {
                helper.innerText = '';
                return;
            }
            
            let num = parseInt(val, 10);
            if (num > 999000000000) {
                helper.innerText = `Lưu ý: Số tiền tối đa là 999.000.000.000 VNĐ.`;
                helper.className = 'text-[11px] mt-1.5 text-rose-500 font-bold h-4';
            } else if (num < 100000) {
                helper.innerText = `Lưu ý: Số tiền tối thiểu là 100.000 VNĐ. (Đang nhập: ${new Intl.NumberFormat('vi-VN').format(num)} VNĐ)`;
                helper.className = 'text-[11px] mt-1.5 text-rose-500 font-bold h-4';
            } else {
                helper.innerText = `Số tiền hợp lệ: ${new Intl.NumberFormat('vi-VN').format(num)} VNĐ`;
                helper.className = 'text-[11px] mt-1.5 text-emerald-600 font-bold h-4';
            }
        }

        function validatePriceInput(input) {
            let val = input.value.replace(/\D/g, '');
            if (val === '') {
                const helper = document.getElementById('price-helper-text');
                if (helper) helper.innerText = '';
                return;
            }
            
            let num = parseInt(val, 10);
            if (num > 999000000000) num = 999000000000;
            if (num < 100000) num = 100000;
            
            input.value = num;
            formatPriceInput(input);
        }

        function getPropertyTypeLabel(rawType) {
            const map = {
                'can_ho': 'Chung cư mini / Căn hộ dịch vụ',
                'nha_tro': 'Nhà trọ / Khu trọ',
                'ky_tuc_xa': 'Ký túc xá / Sleepbox',
                'dat_phan_lo': 'Đất phân lô',
                'dat_nen': 'Đất nền',
                'cum_nha_lien_ke': 'Cụm nhà liền kề / Dự án',
                'nha_rieng': 'Nhà ở / Nhà riêng lẻ',
                'nha_o': 'Nhà ở',
                'phong_tro': 'Phòng trọ'
            };
            return map[rawType] || 'Căn hộ / Nhà ở';
        }

        function applyPropertyTypeMapping(rawType) {
            let mappedType = 'nha_o';
            const isLand = (rawType === 'dat_phan_lo' || rawType === 'dat_nen');
            if (isLand) {
                mappedType = 'dat_nen';
            } else if (rawType === 'nha_tro' || rawType === 'ky_tuc_xa' || rawType === 'phong_tro') {
                mappedType = 'phong_tro';
            } else {
                mappedType = 'nha_o';
            }

            const hiddenInput = document.getElementById('hidden_property_type');
            if (hiddenInput) {
                hiddenInput.value = mappedType;
            }

            togglePropertyType(mappedType);
            return mappedType;
        }

        function onBuildingSelected(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const previewCard = document.getElementById('building-address-preview');
            const previewName = document.getElementById('preview-building-name');
            const previewAddr = document.getElementById('preview-building-address');
            const previewBadge = document.getElementById('preview-building-type-badge');

            const s2Box = document.getElementById('step2-building-summary');
            const s2Name = document.getElementById('step2-building-name');
            const s2Addr = document.getElementById('step2-building-address');
            const s2Type = document.getElementById('step2-building-type-badge');

            const floorCol = document.getElementById('floor-col');
            const floorInput = document.getElementById('post-floor');
            const floorHint = document.getElementById('floor-hint');
            const floorTitle = document.getElementById('floor-label-title');
            const floorLockBadge = document.getElementById('floor-lock-badge');
            const floorHelper = document.getElementById('floor-helper-note');
            const areaCol = document.getElementById('area-col');

            const s1FloorsBadge = document.getElementById('preview-building-floors-badge');
            const s1FloorsText = document.getElementById('preview-building-floors-text');
            const s2FloorsBadge = document.getElementById('step2-building-floors-badge');
            const s2FloorsText = document.getElementById('step2-building-floors-text');

            if (!selectedOption || !selectedOption.value) {
                if (previewCard) {
                    previewCard.classList.add('d-none');
                    previewCard.classList.add('hidden');
                }
                if (s2Box) s2Box.classList.add('hidden');
                if (s1FloorsBadge) s1FloorsBadge.classList.add('hidden');
                if (s2FloorsBadge) s2FloorsBadge.classList.add('hidden');

                renderBuildingAmenities([]);
                renderBuildingServicePrices(null);
                const defaultType = selectedOption?.getAttribute('data-type') || 'nha_o';
                applyPropertyTypeMapping(defaultType);

                // Khôi phục mặc định ô nhập tầng khi chưa chọn tòa nhà
                if (floorCol) {
                    floorCol.classList.remove('hidden');
                    floorCol.className = 'sm:col-span-4 transition-all';
                }
                if (areaCol) {
                    areaCol.className = 'sm:col-span-3 transition-all';
                }
                if (floorTitle) floorTitle.innerText = 'Vị trí tầng';
                if (floorHint) {
                    floorHint.innerText = '(Tùy chọn)';
                    floorHint.className = 'text-slate-400 font-normal text-[10px] shrink-0 whitespace-nowrap leading-none';
                }
                if (floorLockBadge) floorLockBadge.classList.add('hidden');
                if (floorHelper) {
                    floorHelper.classList.add('hidden');
                    floorHelper.innerHTML = '';
                }
                if (floorInput) {
                    floorInput.readOnly = false;
                    floorInput.removeAttribute('max');
                    floorInput.placeholder = 'VD: 3';
                    floorInput.value = '';
                    floorInput.className = 'w-full pl-3.5 pr-14 py-2.5 h-11 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all';
                }
                return;
            }

            const addr = (selectedOption.getAttribute('data-address') || '').trim();
            const bName = selectedOption.getAttribute('data-name') || selectedOption.innerText.trim();
            const bType = (selectedOption.getAttribute('data-type') || 'nha_o').toLowerCase();
            const bBusinessType = (selectedOption.getAttribute('data-business-type') || '').toLowerCase();
            const readableType = getPropertyTypeLabel(bType);

            const floorsAttr = selectedOption.getAttribute('data-floors');
            const totalFloors = parseInt(floorsAttr, 10) || 0;

            const isLienKe = (bType === 'cum_nha_lien_ke') || 
                             bType.includes('liền kề') || 
                             bType.includes('dự án') || 
                             bBusinessType.includes('liền kề') || 
                             bBusinessType.includes('dự án');

            // 1. Tự động mapping Loại hình BĐS & Ẩn/Hiện Số phòng ngủ, Số phòng tắm
            applyPropertyTypeMapping(bType);

            // 2. Cập nhật Preview Card ở Bước 1
            if (previewCard) {
                if (previewName) previewName.innerText = bName;
                if (previewAddr) previewAddr.innerText = addr || 'Địa chỉ theo hồ sơ tòa nhà';
                if (previewBadge) previewBadge.innerText = readableType;
                if (s1FloorsBadge && s1FloorsText) {
                    if (isLienKe) {
                        s1FloorsText.innerText = totalFloors > 0 ? `Quy mô: ${totalFloors} tầng` : 'Cụm liền kề';
                        s1FloorsBadge.classList.remove('hidden');
                    } else if (totalFloors > 0) {
                        s1FloorsText.innerText = `${totalFloors} tầng`;
                        s1FloorsBadge.classList.remove('hidden');
                    } else {
                        s1FloorsBadge.classList.add('hidden');
                    }
                }
                previewCard.classList.remove('d-none');
                previewCard.classList.remove('hidden');
            }

            // 3. Cập nhật thẻ tóm tắt ở Bước 2
            if (s2Box && s2Name && s2Addr) {
                s2Name.innerText = bName;
                s2Addr.innerText = addr || 'Địa chỉ theo hồ sơ tòa nhà';
                if (s2Type) s2Type.innerText = readableType;
                if (s2FloorsBadge && s2FloorsText) {
                    if (isLienKe) {
                        s2FloorsText.innerText = totalFloors > 0 ? `Quy mô: ${totalFloors} tầng` : 'Cụm liền kề';
                        s2FloorsBadge.classList.remove('hidden');
                    } else if (totalFloors > 0) {
                        s2FloorsText.innerText = `${totalFloors} tầng`;
                        s2FloorsBadge.classList.remove('hidden');
                    } else {
                        s2FloorsBadge.classList.add('hidden');
                    }
                }
                s2Box.classList.remove('hidden');
            }

            // 4. Kế thừa tiện ích dùng chung từ Tòa Nhà
            let bAmenities = [];
            try {
                const rawAm = selectedOption.getAttribute('data-amenities');
                if (rawAm) {
                    bAmenities = JSON.parse(rawAm);
                }
            } catch (e) {
                console.error('Lỗi parse data-amenities:', e);
            }
            renderBuildingAmenities(bAmenities);

            // 5. Kế thừa bảng giá dịch vụ dùng chung từ Tòa Nhà
            renderBuildingServicePrices(selectedOption);

            // 6. Xử lý logic số tầng của Lớp Vỏ / Từng căn nhà bên trong
            const isLandType = (bType === 'dat_nen' || bType === 'dat_phan_lo');

            if (isLandType) {
                // Tự động ẩn ô nhập tầng cho đất nền
                if (floorCol) floorCol.classList.add('hidden');
                if (areaCol) {
                    areaCol.className = 'sm:col-span-7 transition-all';
                }
                if (floorInput) {
                    floorInput.value = '';
                    floorInput.readOnly = false;
                }
                if (floorLockBadge) floorLockBadge.classList.add('hidden');
                if (floorHelper) {
                    floorHelper.classList.add('hidden');
                    floorHelper.innerHTML = '';
                }
            } else if (isLienKe) {
                // RIÊNG ĐỐI VỚI CỤM NHÀ LIỀN KỀ / DỰ ÁN:
                // Từng căn nhà bên trong có thể có số tầng khác nhau, nhân sự (con) được toàn quyền chỉnh số tầng
                if (floorCol) {
                    floorCol.classList.remove('hidden');
                    floorCol.className = 'sm:col-span-4 transition-all';
                }
                if (areaCol) {
                    areaCol.className = 'sm:col-span-3 transition-all';
                }
                if (floorTitle) floorTitle.innerText = 'Số tầng của nhà';
                if (floorHint) {
                    floorHint.innerHTML = '<span class="text-sky-600 font-bold text-[10px] shrink-0 whitespace-nowrap leading-none">(Tùy chỉnh)</span>';
                    floorHint.className = 'shrink-0 whitespace-nowrap';
                }
                if (floorInput) {
                    floorInput.readOnly = false;
                    floorInput.removeAttribute('max');
                    floorInput.className = 'w-full pl-3.5 pr-14 py-2.5 h-11 rounded-2xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-sky-500 focus:outline-none transition-all';
                    floorInput.placeholder = 'VD: 3, 4, 5...';
                    // Nếu đang trống thì gợi ý số tầng mặc định của cụm (nếu có)
                    if (!floorInput.value || floorInput.value === '') {
                        floorInput.value = totalFloors > 0 ? totalFloors : '';
                    }
                }
                if (floorLockBadge) {
                    floorLockBadge.classList.remove('hidden');
                    floorLockBadge.innerHTML = '<span class="font-bold text-slate-400 text-[11px]">tầng</span>';
                }
                if (floorHelper) {
                    floorHelper.classList.remove('hidden');
                    floorHelper.innerHTML = '<span class="text-sky-600 font-semibold inline-flex items-center gap-1"><i class="fa-solid fa-pen text-[10px]"></i> Tùy chỉnh số tầng riêng cho từng nhà trong cụm liền kề</span>';
                }
            } else if (totalFloors > 0) {
                // TÒA NHÀ CÓ SỐ TẦNG (Chung cư mini, KTX...): HIỂN THỊ CỐ ĐỊNH VÀ KHÔNG CHO SỬA
                if (floorCol) {
                    floorCol.classList.remove('hidden');
                    floorCol.className = 'sm:col-span-4 transition-all';
                }
                if (areaCol) {
                    areaCol.className = 'sm:col-span-3 transition-all';
                }
                if (floorTitle) floorTitle.innerText = 'Số tầng';
                if (floorHint) {
                    floorHint.innerHTML = '<span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/90 px-1.5 py-0.5 rounded-md whitespace-nowrap shrink-0 leading-none"><i class="fa-solid fa-lock text-[8px]"></i> Cố định</span>';
                    floorHint.className = 'shrink-0 whitespace-nowrap';
                }
                if (floorInput) {
                    floorInput.value = totalFloors;
                    floorInput.readOnly = true;
                    floorInput.className = 'w-full pl-3.5 pr-14 py-2.5 h-11 rounded-2xl bg-slate-100/90 border border-slate-200 text-xs font-extrabold text-slate-800 cursor-not-allowed select-none focus:outline-none transition-all';
                }
                if (floorLockBadge) {
                    floorLockBadge.classList.remove('hidden');
                    floorLockBadge.innerHTML = '<span class="font-bold text-slate-500 text-[11px]">tầng</span><i class="fa-solid fa-lock text-[10px] text-amber-500"></i>';
                }
                if (floorHelper) {
                    floorHelper.classList.remove('hidden');
                    floorHelper.innerHTML = '<span class="text-emerald-600 font-semibold inline-flex items-center gap-1"><i class="fa-solid fa-circle-check text-[10px]"></i> Cố định theo tòa nhà</span>';
                }
            } else {
                // Tòa nhà chưa có số tầng: Cho phép nhập tự do
                if (floorCol) {
                    floorCol.classList.remove('hidden');
                    floorCol.className = 'sm:col-span-4 transition-all';
                }
                if (areaCol) {
                    areaCol.className = 'sm:col-span-3 transition-all';
                }
                if (floorTitle) floorTitle.innerText = 'Vị trí tầng';
                if (floorHint) {
                    floorHint.innerText = '(Tùy chọn)';
                    floorHint.className = 'text-slate-400 font-normal text-[10px] shrink-0 whitespace-nowrap leading-none';
                }
                if (floorInput) {
                    floorInput.readOnly = false;
                    floorInput.className = 'w-full pl-3.5 pr-14 py-2.5 h-11 rounded-2xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all';
                    floorInput.placeholder = 'VD: 3';
                    floorInput.removeAttribute('max');
                }
                if (floorLockBadge) {
                    floorLockBadge.classList.add('hidden');
                    floorLockBadge.innerHTML = '<span class="font-bold text-slate-500 text-[11px]">tầng</span><i class="fa-solid fa-lock text-[10px] text-amber-500"></i>';
                }
                if (floorHelper) {
                    floorHelper.classList.add('hidden');
                    floorHelper.innerHTML = '';
                }
            }
        }

        // ======================= SỰ KIỆN KHỞI TẠO & ĐÓNG MENU =======================
        document.addEventListener('DOMContentLoaded', function () {
            const bSelect = document.getElementById('select-building-id');
            if (bSelect) {
                bSelect.addEventListener('change', function () {
                    onBuildingSelected(this);
                });

                if (bSelect.value) {
                    onBuildingSelected(bSelect);
                } else {
                    const activeOption = bSelect.options[bSelect.selectedIndex];
                    const initType = activeOption?.getAttribute('data-type') || 'nha_o';
                    applyPropertyTypeMapping(initType);
                }
            }
            
            const editDataStr = sessionStorage.getItem('edit_post_data');
            if (editDataStr) {
                try {
                    const postData = JSON.parse(editDataStr);
                    document.getElementById('post-title').value = postData.title || '';
                    if (postData.price) {
                        const priceInput = document.getElementById('post-price');
                        priceInput.value = postData.price;
                        formatPriceInput(priceInput);
                    }
                    
                    const unitSelect = document.getElementById('post-price-unit');
                    if (unitSelect && postData.price_unit) unitSelect.value = postData.price_unit;
                    
                    if(postData.area) document.getElementById('post-area').value = postData.area;
                    
                    window.autoSelectLocation = {
                        province: postData.province,
                        district: postData.district,
                        ward: postData.ward
                    };
                    
                    if (document.getElementById('input-address')) {
                        document.getElementById('input-address').value = postData.address || '';
                    }
                    if (document.getElementById('post-description')) {
                        document.getElementById('post-description').value = postData.description || '';
                    }
                    
                    if (postData.building_id) {
                        const bSelect = document.getElementById('select-building-id');
                        if (bSelect) {
                            bSelect.value = postData.building_id;
                            onBuildingSelected(bSelect);
                        }
                    }

                    const savedFloor2 = (postData.floor !== undefined && postData.floor !== null) ? postData.floor : postData.floor_number;
                    if(savedFloor2 !== undefined && savedFloor2 !== null && savedFloor2 !== '') {
                        const floorInput = document.getElementById('post-floor');
                        // Nếu ô này không bị khóa cố định bởi tòa nhà thì khôi phục giá trị đã lưu
                        if(floorInput && !floorInput.readOnly) {
                            floorInput.value = savedFloor2;
                        }
                    }
                    
                    if (postData.property_type) {
                        const hiddenTypeInput = document.getElementById('hidden_property_type');
                        if (hiddenTypeInput) hiddenTypeInput.value = postData.property_type;
                        if (typeof togglePropertyType === 'function') {
                            togglePropertyType(postData.property_type);
                        }
                    }

                    if (postData.amenities && Array.isArray(postData.amenities)) {
                        const input = document.getElementById('amenities-input');
                        const roomOnlyAmenities = postData.amenities.filter(am => !currentBuildingAmenities.includes(am));
                        if (input) input.value = roomOnlyAmenities.join(', ');
                    }
                    
                    if (postData.images && Array.isArray(postData.images)) {
                        existingImagesArray = postData.images;
                        renderImagePreviews();
                    }
                    
                    let hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.id = 'edit-post-id';
                    hiddenInput.value = postData.id || postData._id;
                    document.body.appendChild(hiddenInput);

                    const btn = document.querySelector('button[onclick="submitPost()"]');
                    if (btn) btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up mr-2"></i>Cập nhật thông tin';
                    
                    const titleH1 = document.querySelector('h1');
                    if (titleH1) titleH1.innerHTML = 'Cập nhật bài đăng';

                    sessionStorage.removeItem('edit_post_data');

                    if (typeof goToStepDetails === 'function') {
                        goToStepDetails();
                    }
                } catch(e) {
                    console.error('Error parsing edit_post_data', e);
                }
            }
            
            fetchProvinces();
        });

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