<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Tòa nhà: {{ $building->name }} - RentHome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="antialiased text-slate-800 pb-20">

    @php
        $imgUrl = $building->display_image;
        if (empty($imgUrl) || $imgUrl === asset('images/default-building.jpg')) {
            if (!empty($building->imageModel) && !empty($building->imageModel->base64_data)) {
                $imgUrl = "data:{$building->imageModel->mime_type};base64,{$building->imageModel->base64_data}";
            } elseif (!empty($building->image)) {
                $imgUrl = \Illuminate\Support\Str::startsWith($building->image, 'http') ? $building->image : asset('storage/' . $building->image);
            } else {
                $imgUrl = 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80';
            }
        }
        
        $addrParts = array_filter([$building->address_detail, $building->ward, $building->district, $building->province]);
        $fullAddress = implode(', ', $addrParts);

        $currentUser = Auth::user();
        $isAdmin = $currentUser && ($currentUser->isAdmin() || $currentUser->role === 'admin' || $currentUser->isEnterprise());

        $rooms = $rooms ?? ($posts ?? collect());
        if (!isset($activeContractPostIds) || !isset($activeContractMap)) {
            $roomItems = $rooms instanceof \Illuminate\Pagination\LengthAwarePaginator ? $rooms->items() : $rooms;
            $roomIds = collect($roomItems)
                ->map(fn($r) => (string)($r->_id ?? $r->id))
                ->filter()
                ->toArray();
            $activeContractPostIds = [];
            $activeContractMap = [];
            if (!empty($roomIds)) {
                $contracts = \App\Models\Contract::where('status', '!=', 'cancelled')
                    ->where(function($q) use ($roomIds) {
                        $q->whereIn('post_id', $roomIds)
                          ->orWhereIn('room_id', $roomIds);
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
    @endphp

    <!-- Hero Banner -->
    <div class="relative w-full h-[400px]">
        <!-- Background Image -->
        <img src="{{ $imgUrl }}" alt="{{ $building->name }}" class="w-full h-full object-cover">
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

        <!-- Back Button -->
        <div class="absolute top-6 left-6 z-10">
            <a href="{{ url('/qltoa_nha') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium backdrop-blur-md transition-all border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách tòa nhà
            </a>
        </div>

        <!-- Banner Content -->
        <div class="absolute bottom-16 left-0 w-full px-8 md:px-16">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 font-bold text-xs border border-blue-500/30 backdrop-blur-sm">
                        <i class="fa-solid fa-building mr-1"></i> {{ $building->type ?? 'Tòa nhà' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 font-bold text-xs border border-purple-500/30 backdrop-blur-sm">
                        <i class="fa-solid fa-door-open mr-1"></i> {{ $building->total_rooms ?? 0 }} Phòng
                    </span>
                    @if($building->status == 'active')
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-xs border border-emerald-500/30 backdrop-blur-sm">
                            <i class="fa-solid fa-check-circle mr-1"></i> Đang hoạt động
                        </span>
                    @endif
                </div>
                
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3 tracking-tight">{{ $building->name }}</h1>
                
                <p class="text-slate-200 text-sm md:text-base flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-rose-400"></i>
                    {{ $fullAddress ?: 'Chưa cập nhật địa chỉ' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content (Floating Card) -->
    <div class="relative -mt-10 px-4 md:px-12 z-20 max-w-7xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            
            @if(session('success'))
                <div class="mx-6 md:mx-8 mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <!-- Header Body -->
            <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Danh sách phòng</h2>
                    <p class="text-sm text-slate-500 mt-1">Quản lý và cập nhật trạng thái các phòng trong tòa nhà.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" placeholder="Tìm mã phòng, tên phòng..." class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-64 transition-all">
                    </div>
                    <a href="{{ url('dangbai?building_id=' . ($building->id ?? $building->_id)) }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/30 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Thêm phòng mới
                    </a>
                </div>
            </div>

            <!-- Table Body -->
            <div class="p-6 md:p-8">
                <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th class="p-4 w-24">Mã Phòng</th>
                                <th class="p-4">Thông Tin Phòng</th>
                                <th class="p-4 w-24">Diện Tích</th>
                                <th class="p-4 w-32">Giá Thuê</th>
                                <th class="p-4 w-40">Trạng Thái</th>
                                <th class="p-4 text-right whitespace-nowrap min-w-[140px]">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rooms as $room)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                   @php
                                        $imgUrl = $room->display_image ?: 'https://placehold.co/150?text=No+Image';
                                        $rId = (string)($room->id ?? $room->_id);
                                        $contract = $activeContractMap[$rId] ?? null;
                                        $tenantName = $room->tenant_name ?? ($contract ? $contract->tenant_name : null);
                                        $isRented = ($room->is_rented === true 
                                            || $room->is_rented === 1 
                                            || $room->is_rented === '1'
                                            || in_array($room->status, ['đã cho thuê', 'rented', 'dang_thue', 'Đang thuê', 'da_cho_thue'])
                                            || in_array($rId, $activeContractPostIds ?? []));
                                    @endphp
                                    <!-- Mã phòng hoặc ID bài đăng -->
                                    <td class="p-4 font-bold text-slate-800">#{{ substr($rId, -5) }}</td>
                                    
                                    <!-- Thông Tin Phòng -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $imgUrl }}" alt="Thumbnail" class="w-12 h-12 md:w-14 md:h-14 object-cover rounded-lg border border-slate-200 shrink-0 shadow-sm" onerror="this.src='https://placehold.co/150?text=Lỗi+Ảnh'">
                                            <div class="flex flex-col">
                                                <h4 class="font-bold text-slate-900 text-sm line-clamp-1" title="{{ $room->title }}">{{ $room->title ?? 'Phòng cho thuê' }}</h4>
                                                <p class="text-[11px] text-slate-500 mt-1 font-medium">{{ $room->property_type ?? 'Phòng' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Diện tích -->
                                    <td class="p-4 text-slate-600 font-medium">{{ $room->area ?? 0 }} m²</td>
                                    
                                    <!-- Giá tiền (Format chuẩn Việt Nam) -->
                                    <td class="p-4 font-bold text-rose-600">{{ number_format($room->price ?? 0, 0, ',', '.') }} đ</td>
                                    
                                    <!-- Trạng thái phòng -->
                                    <td class="p-4">
                                        @if($isRented)
                                            <div class="inline-flex flex-col items-start gap-1">
                                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs inline-flex items-center gap-1.5 border border-amber-200 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 inline-block animate-pulse"></span> Đang thuê
                                                </span>
                                                @if(!empty($tenantName))
                                                    <span class="text-[11px] text-slate-500 font-medium flex items-center gap-1 pl-1">
                                                        <i class="fa-solid fa-user text-[9px] text-amber-600"></i> {{ $tenantName }}
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif($room->status === 'approved' || $room->status === 'active' || empty($room->status))
                                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs inline-flex items-center gap-1.5 border border-emerald-200 shadow-2xs">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Đang hiển thị
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-xs inline-flex items-center gap-1.5 border border-slate-200 shadow-2xs">
                                                <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div> Đã ẩn / Khóa
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Nút hành động -->
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ url('/baidang/' . $rId) }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors" title="Xem trên web">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                            </a>
                                            <a href="{{ url('dangbai?edit=' . $rId) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors" title="Chỉnh sửa">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>
                                            <button onclick="unlinkRoom('{{ $rId }}')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors cursor-pointer" title="Gỡ khỏi tòa nhà">
                                                <i class="fa-solid fa-link-slash text-xs"></i>
                                            </button>
                                            <button onclick="deleteRoom('{{ $rId }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors cursor-pointer" title="Xóa vĩnh viễn">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <!-- Hiển thị khi tòa nhà chưa có phòng nào -->
                                <tr>
                                    <td colspan="6" class="p-10 text-center">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-solid fa-door-closed text-2xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium">Chưa có phòng nào trong tòa nhà này.</p>
                                        <p class="text-xs text-slate-400 mt-1">Hãy bấm "Thêm phòng mới" để bắt đầu.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (Placeholder) -->
                <div class="mt-10 flex items-center justify-between">
                    <p class="text-sm text-slate-500">
                        Hiển thị <span class="font-bold text-slate-800">{{ collect($rooms)->count() }}</span> 
                        trên <span class="font-bold text-slate-800">{{ $building->total_rooms ?? 0 }}</span> phòng
                    </p>
                    
                    <!-- Hiển thị các nút phân trang mặc định của Laravel nếu có -->
                    <div class="flex items-center gap-1">
                        @if(method_exists($rooms, 'links'))
                            {{ $rooms->links('pagination::tailwind') }}
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
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
                if(data.success) {
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
            if (!confirm('Hành động này sẽ XÓA VĨNH VIỄN bài đăng. Bạn có chắc chắn?')) return;
            fetch(`/api/posts/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
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
                if(data.success) {
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

        // Tìm kiếm mã phòng / tên phòng nhanh trên bảng
        document.getElementById('searchInput')?.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return;
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

    </script>
</body>
</html>
