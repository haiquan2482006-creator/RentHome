@props(['count' => 1])

@php
    $repeatCount = isset($count) && is_numeric($count) && (int)$count > 0 ? (int)$count : 1;
@endphp

@for ($i = 0; $i < $repeatCount; $i++)
<div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm animate-pulse flex flex-col justify-between">
    <!-- 1. Khối Hình ảnh (Tỷ lệ 4:3 tương đương thumbnail nhà của RentHome) -->
    <div class="relative aspect-[4/3] w-full bg-slate-200 overflow-hidden flex items-center justify-center">
        <i class="fa-regular fa-image text-slate-300 text-3xl"></i>
        <!-- Huy hiệu giá hoặc tag nổi bật giả lập ở góc ảnh -->
        <div class="absolute bottom-4 left-4 bg-slate-300/80 h-6 w-24 rounded-xl"></div>
    </div>

    <!-- 2. Khối Text (Thông tin bài đăng bên dưới ảnh) -->
    <div class="p-6 flex-1 flex flex-col justify-between">
        <div>
            <!-- Label nhỏ mô phỏng phân loại (Nhà ở / Căn hộ / Phòng trọ) -->
            <div class="h-4 w-20 bg-slate-200 rounded-full"></div>

            <!-- Khối dài mô phỏng Tiêu đề bài đăng -->
            <div class="bg-slate-300 h-4 w-3/4 rounded mt-4"></div>

            <!-- Khối ngắn mô phỏng Địa chỉ -->
            <div class="bg-slate-200 h-3 w-1/2 rounded mt-2"></div>

            <!-- Khối mô phỏng Lượt xem & Lượt yêu thích -->
            <div class="flex items-center gap-3 mt-3">
                <div class="bg-slate-200 h-3 w-12 rounded"></div>
                <div class="bg-slate-200 h-3 w-12 rounded"></div>
            </div>
        </div>

        <!-- Khối góc dưới cùng: Giá tiền giả lập và nút hành động -->
        <div class="flex items-center justify-between pt-4 mt-5 border-t border-slate-100">
            <!-- Giá tiền ở góc dưới cùng -->
            <div class="bg-slate-300 h-5 w-1/3 rounded"></div>

            <!-- Nút bấm chi tiết giả lập -->
            <div class="bg-slate-200 h-8 w-20 rounded-xl"></div>
        </div>
    </div>
</div>
@endfor
