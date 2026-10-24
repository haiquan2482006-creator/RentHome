<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Vận Hành & Quản Lý Phòng: {{ $building->name }} - RentHome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7fb;
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
    </style>
</head>
<body class="antialiased text-slate-800 pb-20">

    @php
        // 1. XỬ LÝ DANH SÁCH NHIỀU ẢNH CỦA TÒA NHÀ (LỚP VỎ) TỪ DATABASE
        $buildingImages = [];

        if (!empty($building->all_images) && is_iterable($building->all_images)) {
            foreach ($building->all_images as $imgItem) {
                $u = is_array($imgItem) ? ($imgItem['url'] ?? '') : ($imgItem->url ?? '');
                if ($u) $buildingImages[] = $u;
            }
        }

        if (empty($buildingImages) && !empty($building->images) && is_iterable($building->images)) {
            foreach ($building->images as $rawImg) {
                $rawStr = is_array($rawImg) ? ($rawImg['id'] ?? ($rawImg['url'] ?? '')) : (string)$rawImg;
                if (!$rawStr) continue;
                if (\Illuminate\Support\Str::startsWith($rawStr, ['http', 'data:', '/images/', '/storage/'])) {
                    $buildingImages[] = $rawStr;
                } elseif (preg_match('/^[a-f\d]{24}$/i', $rawStr)) {
                    // Nếu là ObjectId của Collection images trong MongoDB
                    $buildingImages[] = url('/images/' . $rawStr);
                } else {
                    $buildingImages[] = asset('storage/' . ltrim($rawStr, '/'));
                }
            }
        }

        // Ảnh đại diện chính của Tòa nhà
        $coverImgUrl = $building->display_image ?? '';
        if (empty($coverImgUrl) || $coverImgUrl === asset('images/default-building.jpg')) {
            if (!empty($buildingImages)) {
                $coverImgUrl = $buildingImages[0];
            } elseif (!empty($building->imageModel) && !empty($building->imageModel->base64_data)) {
                $coverImgUrl = "data:{$building->imageModel->mime_type};base64,{$building->imageModel->base64_data}";
            } elseif (!empty($building->image)) {
                $rawImg = (string)$building->image;
                if (\Illuminate\Support\Str::startsWith($rawImg, ['http', 'data:', '/images/'])) {
                    $coverImgUrl = $rawImg;
                } elseif (preg_match('/^[a-f\d]{24}$/i', $rawImg)) {
                    $coverImgUrl = url('/images/' . $rawImg);
                } elseif (\Illuminate\Support\Str::startsWith($rawImg, ['/storage/', 'storage/'])) {
                    $coverImgUrl = asset(ltrim($rawImg, '/'));
                } else {
                    $coverImgUrl = asset('storage/' . ltrim($rawImg, '/'));
                }
            } else {
                $coverImgUrl = 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80';
            }
        }

        if (empty($buildingImages)) {
            $buildingImages[] = $coverImgUrl;
        } else {
            $buildingImages = array_values(array_unique($buildingImages));
        }

        // 2. XỬ LÝ ĐỊA CHỈ & PHÂN LOẠI BẤT ĐỘNG SẢN (LỚP VỎ)
        $addrParts = array_filter([$building->address_detail, $building->ward, $building->district, $building->province]);
        $fullAddress = $building->full_address ?? implode(', ', $addrParts);

        $bTypeRaw = strtolower($building->type ?? '');
        $bTypeLabel = $building->business_type ?: ($building->type ?: 'Chung cư mini / Căn hộ dịch vụ');
        $bTypeLower = mb_strtolower($bTypeLabel);

        $isLand = ($bTypeRaw === 'dat_phan_lo') || str_contains($bTypeLower, 'phân lô') || str_contains($bTypeLower, 'đất nền');
        $isLienKe = ($bTypeRaw === 'cum_nha_lien_ke') || str_contains($bTypeLower, 'liền kề') || str_contains($bTypeLower, 'dự án');
        $isHouse = !$isLienKe && (($bTypeRaw === 'nha_rieng') || str_contains($bTypeLower, 'nhà riêng') || str_contains($bTypeLower, 'nhà ở') || str_contains($bTypeLower, 'nguyên căn'));
        $isMatBang = ($bTypeRaw === 'dat_nen') || str_contains($bTypeLower, 'mặt bằng') || str_contains($bTypeLower, 'văn phòng');

        // Ẩn Nhóm 2 (Bảng giá dịch vụ dùng chung) đối với Cụm liền kề, Đất phân lô, Nhà riêng lẻ
        $hideSharedServicePricing = ($isLand || $isLienKe || $isHouse);

        // Đơn vị gọi tên Lớp Ruột (phòng / căn / lô / mặt bằng)
        $unitName = $isLienKe ? 'căn' : ($isLand ? 'lô' : ($isMatBang ? 'mặt bằng' : 'phòng'));
        $unitNameCap = ucfirst($unitName);

        // 3. PHÂN QUYỀN & ĐIỀU HƯỚNG QUAY LẠI
        $currentUser = Auth::user();
        $isSub = $currentUser && (
            (method_exists($currentUser, 'isSubAccount') && $currentUser->isSubAccount()) ||
            in_array($currentUser->role ?? '', ['sub_account', 'staff', 'doanhnghiep']) ||
            !empty($currentUser->parent_id)
        );
        $isAdmin = $currentUser && (
            (method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin()) ||
            ($currentUser->role ?? '') === 'admin'
        );
        $backUrl = ($isSub || request('ref') === 'buildings') ? url('/Overview?tab=buildings') : url('/qltoa_nha');

        // 4. XỬ LÝ DANH SÁCH PHÒNG (LỚP RUỘT) & HỢP ĐỒNG ĐANG THUÊ
        $rooms = $rooms ?? ($posts ?? collect());
        $roomItems = $rooms instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($rooms->items()) : collect($rooms);

        if (!isset($activeContractPostIds) || !isset($activeContractMap)) {
            $roomIds = $roomItems->map(fn($r) => (string)($r->_id ?? $r->id))->filter()->toArray();
            $activeContractPostIds = [];
            $activeContractMap = [];
            if (!empty($roomIds) && class_exists(\App\Models\Contract::class)) {
                $contracts = \App\Models\Contract::where('status', '!=', 'cancelled')
                    ->where(function($q) use ($roomIds) {
                        $q->whereIn('post_id', $roomIds)->orWhereIn('room_id', $roomIds);
                    })
                    ->get(['post_id', 'room_id', 'tenant_name']);
                foreach ($contracts as $c) {
                    if (!empty($c->post_id)) {
                        $activeContractPostIds[] = (string)$c->post_id;
                        $activeContractMap[(string)$c->post_id] = $c;
                    }
                    if (!empty($c->room_id)) {
                        $activeContractPostIds[] = (string)$c->room_id;
                        $activeContractMap[(string)$c->room_id] = $c;
                    }
                }
                $activeContractPostIds = array_unique($activeContractPostIds);
            }
        }

        // 5. TÍNH TOÁN CHỈ SỐ MINI DASHBOARD (LẤP ĐẦY, TIẾN ĐỘ, TÀI CHÍNH)
        $totalCapacity = max((int)($building->total_rooms ?? 1), 1);
        $createdCount  = $rooms instanceof \Illuminate\Pagination\LengthAwarePaginator ? $rooms->total() : $roomItems->count();
        $missingCount  = max($totalCapacity - $createdCount, 0);
        $progressPct   = min(round(($createdCount / $totalCapacity) * 100, 1), 100);

        $rentedCount    = 0;
        $availableCount = 0;
        $approvedPosts  = 0;
        $pendingPosts   = 0;
        $rejectedPosts  = 0;
        $actualRevenue  = 0;
        $maxRevenue     = 0;

        foreach ($roomItems as $r) {
            $rid = (string)($r->id ?? $r->_id);
            $rStatus = strtolower($r->status ?? 'approved');
            $rPrice = (float)($r->price ?? 0);
            $maxRevenue += $rPrice;

            $checkRented = (
                $r->is_rented === true || $r->is_rented === 1 || $r->is_rented === '1' ||
                in_array($rStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue']) ||
                in_array($rid, $activeContractPostIds ?? [])
            );

            if ($checkRented) {
                $rentedCount++;
                $actualRevenue += $rPrice;
            } else {
                $availableCount++;
            }

            if (in_array($rStatus, ['pending', 'cho_duyet', 'chờ duyệt', 'waiting'])) {
                $pendingPosts++;
            } elseif (in_array($rStatus, ['rejected', 'tu_choi', 'từ chối', 'hidden', 'inactive', 'locked'])) {
                $rejectedPosts++;
            } else {
                $approvedPosts++;
            }
        }

        $amenitiesList = is_array($building->amenities) ? $building->amenities : [];
    @endphp

    <!-- ================= HERO BANNER & BỘ SƯU TẬP ẢNH LỚP VỎ ================= -->
    <div class="relative w-full bg-slate-900 overflow-hidden">
        <!-- Ảnh nền mờ phía sau -->
        <div class="relative h-[340px] md:h-[380px] w-full">
            <img id="heroMainImage" src="{{ $coverImgUrl }}" alt="{{ $building->name }}" class="w-full h-full object-cover opacity-85 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/65 to-slate-900/30"></div>

            <!-- Thanh điều hướng phía trên -->
            <div class="absolute top-6 left-0 right-0 px-6 md:px-12 z-20">
                <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                    <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-sm font-semibold backdrop-blur-md transition-all border border-white/20 shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách cơ sở
                    </a>

                    <button type="button" onclick="openLightbox(0)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900/70 hover:bg-slate-900 text-white text-sm font-semibold backdrop-blur-md transition-all border border-white/20 cursor-pointer">
                        <i class="fa-solid fa-images text-sky-400"></i>
                        Xem ảnh Tòa nhà ({{ count($buildingImages) }} ảnh)
                    </button>
                </div>
            </div>

            <!-- Thông tin định danh Tòa Nhà (Lớp Vỏ) & Dải Thumbnail nhiều ảnh -->
            <div class="absolute bottom-14 left-0 right-0 px-6 md:px-12 z-20">
                <div class="max-w-7xl mx-auto flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5 mb-3">
                            <span class="px-3 py-1 rounded-lg bg-sky-500/25 text-sky-200 font-bold text-xs border border-sky-400/30 backdrop-blur-sm">
                                <i class="fa-solid fa-tag mr-1"></i> {{ $bTypeLabel }}
                            </span>
                            @if(!$isLand && !$isLienKe)
                                <span class="px-3 py-1 rounded-lg bg-indigo-500/25 text-indigo-200 font-bold text-xs border border-indigo-400/30 backdrop-blur-sm">
                                    <i class="fa-solid fa-layer-group mr-1"></i> {{ $building->total_floors ?? 1 }} Tầng
                                </span>
                            @endif
                            <span class="px-3 py-1 rounded-lg bg-purple-500/25 text-purple-200 font-bold text-xs border border-purple-400/30 backdrop-blur-sm">
                                <i class="fa-solid fa-door-open mr-1"></i> Quy mô: {{ $totalCapacity }} {{ $unitName }}
                            </span>
                            <span class="px-3 py-1 rounded-lg bg-emerald-500/25 text-emerald-200 font-bold text-xs border border-emerald-400/30 backdrop-blur-sm">
                                <i class="fa-solid fa-circle-check mr-1"></i> Cơ sở đang vận hành
                            </span>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-2">
                            {{ $building->name }}
                        </h1>

                        <p class="text-slate-200 text-sm md:text-base flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-rose-400"></i>
                            <span>{{ $fullAddress ?: 'Chưa cập nhật địa chỉ chi tiết' }}</span>
                        </p>
                    </div>

                    <!-- Dải Thumbnail các ảnh mặt tiền / tiện ích chung của Tòa nhà (Lớp Vỏ) -->
                    @if(count($buildingImages) > 0)
                        <div class="flex items-center gap-2.5 bg-slate-900/60 p-2 rounded-2xl border border-white/15 backdrop-blur-md shrink-0">
                            @foreach(array_slice($buildingImages, 0, 5) as $idx => $bImg)
                                <div onclick="openLightbox({{ $idx }})" class="relative w-16 h-12 md:w-20 md:h-14 rounded-xl overflow-hidden border-2 {{ $idx === 0 ? 'border-sky-400' : 'border-white/20 hover:border-white' }} cursor-pointer group transition-all">
                                    <img src="{{ $bImg }}" alt="Ảnh cơ sở {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.src='https://placehold.co/200x150?text=No+Image'">
                                    @if($idx === 0)
                                        <span class="absolute bottom-0 inset-x-0 bg-sky-600/90 text-[9px] text-white font-bold text-center py-0.5">Ảnh bìa</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MAIN CONTAINER ================= -->
    <div class="relative -mt-8 px-4 md:px-12 z-30 max-w-7xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <!-- KHỐI 1: 4 THẺ THỐNG KÊ TỶ LỆ LẤP ĐẦY (OCCUPANCY CARDS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Tổng quy mô -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tổng Quy Mô Cơ Sở</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalCapacity }} <span class="text-sm font-semibold text-slate-500">{{ $unitName }}</span></h3>
                    <p class="text-xs text-slate-500 mt-1">Theo cấu hình Lớp Vỏ</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>

            <!-- Card 2: Đang trống (Sẵn sàng) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">{{ $unitNameCap }} Đang Trống</p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $availableCount }} <span class="text-sm font-semibold">{{ $unitName }}</span></h3>
                    <p class="text-xs text-slate-500 mt-1">Sẵn sàng đón khách thuê</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-door-open"></i>
                </div>
            </div>

            <!-- Card 3: Đang cho thuê -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Đang Cho Thuê</p>
                    <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $rentedCount }} <span class="text-sm font-semibold">{{ $unitName }}</span></h3>
                    <p class="text-xs text-slate-500 mt-1">Đã có hợp đồng / khách ở</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>

            <!-- Card 4: Trạng thái chờ duyệt -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Tin Chờ Kiểm Duyệt</p>
                    <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ $pendingPosts }} <span class="text-sm font-semibold">bài đăng</span></h3>
                    <p class="text-xs text-slate-500 mt-1">Đang chờ duyệt giá & ảnh</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <!-- BẢNG DANH SÁCH CHI TIẾT CÁC PHÒNG / BÀI ĐĂNG (LỚP RUỘT) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
            <!-- Thanh công cụ & Bộ lọc -->
            <div class="p-6 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-slate-50/50">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-sky-600"></i>
                        Danh Sách {{ $unitNameCap }} & Bài Đăng Trực Thuộc (Lớp Ruột)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Mọi bài đăng tạo tại đây sẽ tự động kế thừa Địa chỉ và Ảnh mặt tiền của cơ sở <strong>{{ $building->name }}</strong>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tìm kiếm từ khóa -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="searchInput" oninput="filterRoomsTable()" placeholder="Tìm mã, tên {{ $unitName }}, khách..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none w-56 transition-all">
                    </div>

                    <!-- Lọc theo tình trạng thuê -->
                    <select id="occupancyFilter" onchange="filterRoomsTable()" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-600 outline-none focus:border-sky-500 cursor-pointer">
                        <option value="">Tất cả trạng thái {{ $unitName }}</option>
                        <option value="available">Đang trống</option>
                        <option value="rented">Đang cho thuê</option>
                    </select>

                    <!-- Lọc theo trạng thái duyệt bài -->
                    <select id="moderationFilter" onchange="filterRoomsTable()" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-600 outline-none focus:border-sky-500 cursor-pointer">
                        <option value="">Tất cả trạng thái tin</option>
                        <option value="approved">Đang hiển thị (Đã duyệt)</option>
                        <option value="pending">Chờ kiểm duyệt</option>
                        <option value="rejected">Bị từ chối / Đã ẩn</option>
                    </select>

                    <!-- Nút Thêm phòng mới -->
                    <a href="{{ url('dangbai?building_id=' . ($building->id ?? $building->_id)) }}" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-md shadow-sky-500/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Thêm {{ $unitName }} mới
                    </a>
                </div>
            </div>

            <!-- Bảng dữ liệu Lớp Ruột -->
            <div class="p-6">
                <div class="overflow-x-auto rounded-2xl border border-slate-200/70 custom-scrollbar">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 border-b border-slate-200/70 text-slate-500 uppercase text-[11px] font-extrabold tracking-wider">
                            <tr>
                                <th class="p-4 w-24">Mã Số</th>
                                <th class="p-4">Thông Tin {{ $unitNameCap }} / Bài Đăng</th>
                                <th class="p-4 w-28">Diện Tích</th>
                                <th class="p-4 w-36">Giá Niêm Yết</th>
                                <th class="p-4 w-40">Tình Trạng Thuê</th>
                                <th class="p-4 w-40">Trạng Thái Tin Đăng</th>
                                <th class="p-4 text-right min-w-[150px]">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="roomsTableBody" class="divide-y divide-slate-100">
                            @forelse($rooms as $room)
                                @php
                                    $rId = (string)($room->id ?? $room->_id);

                                    // Chuẩn hóa đường dẫn ảnh của Phòng từ Collection images (MongoDB)
                                    $roomImg = $room->display_image ?? '';
                                    if (empty($roomImg)) {
                                        $firstRoomImg = is_array($room->images ?? null) && count($room->images) > 0 ? $room->images[0] : ($room->image ?? '');
                                        $firstRoomImgStr = is_array($firstRoomImg) ? ($firstRoomImg['id'] ?? ($firstRoomImg['url'] ?? '')) : (string)$firstRoomImg;
                                        if ($firstRoomImgStr) {
                                            if (\Illuminate\Support\Str::startsWith($firstRoomImgStr, ['http', 'data:', '/images/', '/storage/'])) {
                                                $roomImg = $firstRoomImgStr;
                                            } elseif (preg_match('/^[a-f\d]{24}$/i', $firstRoomImgStr)) {
                                                $roomImg = url('/images/' . $firstRoomImgStr);
                                            } else {
                                                $roomImg = asset('storage/' . ltrim($firstRoomImgStr, '/'));
                                            }
                                        } else {
                                            $roomImg = 'https://placehold.co/150?text=No+Image';
                                        }
                                    }

                                    $roomImgCount = is_countable($room->images ?? null) ? count($room->images) : 1;

                                    $contract = $activeContractMap[$rId] ?? null;
                                    $tenantName = $room->tenant_name ?? ($contract ? $contract->tenant_name : null);
                                    $rawRoomStatus = strtolower($room->status ?? 'approved');

                                    $isRented = (
                                        $room->is_rented === true || $room->is_rented === 1 || $room->is_rented === '1' ||
                                        in_array($rawRoomStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue']) ||
                                        in_array($rId, $activeContractPostIds ?? [])
                                    );

                                    $occKey = $isRented ? 'rented' : 'available';

                                    if (in_array($rawRoomStatus, ['pending', 'cho_duyet', 'chờ duyệt', 'waiting'])) {
                                        $modKey = 'pending';
                                    } elseif (in_array($rawRoomStatus, ['rejected', 'tu_choi', 'từ chối', 'hidden', 'inactive', 'locked'])) {
                                        $modKey = 'rejected';
                                    } else {
                                        $modKey = 'approved';
                                    }
                                @endphp

                                <tr class="room-row hover:bg-slate-50/90 transition-colors"
                                    data-occupancy="{{ $occKey }}"
                                    data-moderation="{{ $modKey }}">

                                    <!-- Cột 1: Mã Phòng -->
                                    <td class="p-4 font-extrabold text-slate-700">
                                        #{{ strtoupper(substr($rId, -5)) }}
                                    </td>

                                    <!-- Cột 2: Ảnh & Tiêu đề phòng -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="relative shrink-0">
                                                <img src="{{ $roomImg }}" alt="Thumbnail" class="w-14 h-12 object-cover rounded-xl border border-slate-200 shadow-2xs" onerror="this.src='https://placehold.co/150?text=No+Image'">
                                                @if($roomImgCount > 1)
                                                    <span class="absolute -bottom-1 -right-1 bg-slate-900/80 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md">
                                                        <i class="fa-solid fa-camera text-[8px]"></i> {{ $roomImgCount }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col">
                                                <h4 class="font-bold text-slate-900 text-sm line-clamp-1 max-w-xs md:max-w-sm" title="{{ $room->title }}">
                                                    {{ $room->title ?? ($unitNameCap . ' cho thuê') }}
                                                </h4>
                                                <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-1 font-medium">
                                                    <span><i class="fa-solid fa-layer-group text-sky-500 mr-1"></i>{{ $room->property_type ?? $bTypeLabel }}</span>
                                                    @if(!empty($room->floor_number))
                                                        <span>• Tầng {{ $room->floor_number }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cột 3: Diện tích -->
                                    <td class="p-4 text-slate-700 font-bold">
                                        {{ $room->area ?? 0 }} m²
                                    </td>

                                    <!-- Cột 4: Giá thuê niêm yết -->
                                    <td class="p-4">
                                        <div class="font-extrabold text-rose-600 text-sm">
                                            {{ number_format($room->price ?? 0, 0, ',', '.') }} đ
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium">/ tháng</div>
                                    </td>

                                    <!-- Cột 5: Tình trạng lấp đầy (Đang trống / Đang thuê) -->
                                    <td class="p-4">
                                        @if($isRented)
                                            <div class="inline-flex flex-col items-start gap-1">
                                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-xs inline-flex items-center gap-1.5 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span> Đang cho thuê
                                                </span>
                                                @if(!empty($tenantName))
                                                    <span class="text-[11px] text-slate-600 font-semibold flex items-center gap-1 pl-1">
                                                        <i class="fa-solid fa-user-check text-[10px] text-blue-600"></i> {{ $tenantName }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs inline-flex items-center gap-1.5 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đang trống
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Cột 6: Trạng thái Kiểm duyệt bài đăng -->
                                    <td class="p-4">
                                        @if($modKey === 'pending')
                                            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-xs inline-flex items-center gap-1.5 border border-amber-200">
                                                <i class="fa-solid fa-clock text-[10px]"></i> Chờ kiểm duyệt
                                            </span>
                                        @elseif($modKey === 'rejected')
                                            <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-xs inline-flex items-center gap-1.5 border border-rose-200">
                                                <i class="fa-solid fa-ban text-[10px]"></i> Từ chối / Đã ẩn
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-sky-50 text-sky-700 font-bold text-xs inline-flex items-center gap-1.5 border border-sky-200">
                                                <i class="fa-solid fa-globe text-[10px]"></i> Đang hiển thị
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Cột 7: Nút hành động -->
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ url('/baidang/' . $rId) }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-sky-100 text-slate-700 hover:text-sky-700 flex items-center justify-center transition-colors" title="Xem bài đăng thực tế trên web">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                            </a>
                                            <a href="{{ url('dangbai?edit=' . $rId) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors" title="Chỉnh sửa thông tin & giá phòng">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>
                                            <button onclick="unlinkRoom('{{ $rId }}')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-colors cursor-pointer" title="Gỡ khỏi tòa nhà">
                                                <i class="fa-solid fa-link-slash text-xs"></i>
                                            </button>
                                            <button onclick="deleteRoom('{{ $rId }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors cursor-pointer" title="Xóa vĩnh viễn bài đăng">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center">
                                        <div class="w-16 h-16 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-solid fa-door-open text-2xl"></i>
                                        </div>
                                        <p class="text-slate-700 font-bold text-base">Chưa có {{ $unitName }} nào được khởi tạo trong cơ sở này</p>
                                        <p class="text-xs text-slate-400 mt-1 mb-4">Nhân viên phụ trách hãy nhấn nút "Thêm {{ $unitName }} mới" để đăng thông tin Lớp Ruột.</p>
                                        <a href="{{ url('dangbai?building_id=' . ($building->id ?? $building->_id)) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-sm transition-all">
                                            <i class="fa-solid fa-plus"></i> Khởi tạo {{ $unitName }} đầu tiên
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Chân bảng & Phân trang -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 font-medium">
                        Đang hiển thị <span class="font-extrabold text-slate-800">{{ $roomItems->count() }}</span>
                        trên tổng quy mô <span class="font-extrabold text-slate-800">{{ $totalCapacity }}</span> {{ $unitName }} của cơ sở
                    </p>
                    <div class="flex items-center gap-1">
                        @if(method_exists($rooms, 'links'))
                            {{ $rooms->links('pagination::tailwind') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL LIGHTBOX XEM NHIỀU ẢNH TÒA NHÀ (LỚP VỎ) ================= -->
    <div id="buildingLightbox" class="fixed inset-0 bg-slate-950/90 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <button type="button" onclick="closeLightbox()" class="absolute top-6 right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="max-w-4xl w-full flex flex-col items-center">
            <div class="relative w-full h-[65vh] flex items-center justify-center">
                @if(count($buildingImages) > 1)
                    <button type="button" onclick="changeLightboxSlide(-1)" class="absolute left-2 w-11 h-11 rounded-full bg-slate-900/70 hover:bg-sky-600 text-white flex items-center justify-center cursor-pointer transition-all">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                @endif

                <img id="lightboxMainImg" src="{{ $coverImgUrl }}" alt="Ảnh cơ sở" class="max-h-full max-w-full object-contain rounded-2xl shadow-2xl border border-white/10">

                @if(count($buildingImages) > 1)
                    <button type="button" onclick="changeLightboxSlide(1)" class="absolute right-2 w-11 h-11 rounded-full bg-slate-900/70 hover:bg-sky-600 text-white flex items-center justify-center cursor-pointer transition-all">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                @endif
            </div>

            <div class="mt-4 text-center">
                <p class="text-white font-bold text-sm">{{ $building->name }} - Hình ảnh mặt tiền & Tiện ích chung (Lớp Vỏ)</p>
                <p id="lightboxCounter" class="text-sky-400 text-xs font-semibold mt-0.5">Ảnh 1 / {{ count($buildingImages) }}</p>
            </div>
        </div>
    </div>

    <script>
        // Danh sách ảnh Tòa nhà (Lớp Vỏ) truyền xuống JS
        const buildingGalleryImages = @json($buildingImages);
        let currentSlideIndex = 0;

        function openLightbox(index = 0) {
            if (!buildingGalleryImages || buildingGalleryImages.length === 0) return;
            currentSlideIndex = index;
            updateLightboxView();
            const lb = document.getElementById('buildingLightbox');
            lb.classList.remove('hidden');
            lb.classList.add('flex');
        }

        function closeLightbox() {
            const lb = document.getElementById('buildingLightbox');
            lb.classList.add('hidden');
            lb.classList.remove('flex');
        }

        function changeLightboxSlide(step) {
            currentSlideIndex = (currentSlideIndex + step + buildingGalleryImages.length) % buildingGalleryImages.length;
            updateLightboxView();
        }

        function updateLightboxView() {
            const imgEl = document.getElementById('lightboxMainImg');
            const counterEl = document.getElementById('lightboxCounter');
            if (imgEl) imgEl.src = buildingGalleryImages[currentSlideIndex];
            if (counterEl) counterEl.innerText = `Ảnh ${currentSlideIndex + 1} / ${buildingGalleryImages.length}`;
        }

        // Bộ lọc kép: Từ khóa + Tình trạng thuê + Trạng thái kiểm duyệt
        function filterRoomsTable() {
            const query = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            const occVal = document.getElementById('occupancyFilter')?.value || '';
            const modVal = document.getElementById('moderationFilter')?.value || '';

            const rows = document.querySelectorAll('#roomsTableBody tr.room-row');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const rowOcc = row.getAttribute('data-occupancy') || '';
                const rowMod = row.getAttribute('data-moderation') || '';

                const matchQuery = !query || text.includes(query);
                const matchOcc = !occVal || rowOcc === occVal;
                const matchMod = !modVal || rowMod === modVal;

                row.style.display = (matchQuery && matchOcc && matchMod) ? '' : 'none';
            });
        }

        // Giữ nguyên các hàm gọi API của hệ thống
        function unlinkRoom(id) {
            if (!confirm('Bạn có chắc chắn muốn gỡ bài đăng này khỏi tòa nhà?')) return;
            fetch(`/api/posts/${id}/unlink`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Đã gỡ bài đăng khỏi tòa nhà thành công!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            });
        }

        function deleteRoom(id) {
            if (!confirm('Hành động này sẽ XÓA VĨNH VIỄN bài đăng và hình ảnh liên quan. Bạn có chắc chắn?')) return;
            fetch(`/api/posts/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Đã xóa bài đăng thành công!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            });
        }

        function cancelRoom(id) {
            if (!confirm('Bạn có muốn hủy đăng bài này không?')) return;
            fetch(`/api/posts/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Đã hủy đăng bài thành công!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            });
        }
    </script>
</body>
</html>