@props(['count' => 4])

@php
    $repeatCount = isset($count) && is_numeric($count) && (int)$count > 0 ? (int)$count : 4;
@endphp

@for ($i = 0; $i < $repeatCount; $i++)
<div class="w-[310px] sm:w-[350px] md:w-[370px] shrink-0 bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-card animate-pulse flex flex-col justify-between">
    <!-- Image Frame Placeholder -->
    <div class="relative aspect-[4/3] w-full bg-slate-200 overflow-hidden flex items-center justify-center">
        <i class="fa-regular fa-image text-slate-300 text-3xl"></i>
        <!-- Tag Nổi Bật Placeholder -->
        <div class="absolute top-4 left-4 bg-amber-200/80 h-6 w-24 rounded-full"></div>
        <!-- Price Placeholder -->
        <div class="absolute bottom-4 left-4 bg-slate-300/80 h-7 w-32 rounded-xl"></div>
    </div>

    <!-- Content Placeholder -->
    <div class="p-6 flex-1 flex flex-col justify-between">
        <div>
            <!-- Badge BQL kiểm duyệt -->
            <div class="h-3.5 w-44 bg-slate-200 rounded-full mb-3"></div>
            <!-- Title -->
            <div class="bg-slate-300 h-5 w-4/5 rounded mb-2"></div>
            <div class="bg-slate-200 h-4 w-3/5 rounded mb-3"></div>
            <!-- Address -->
            <div class="bg-slate-200 h-3 w-1/2 rounded"></div>
        </div>

        <div>
            <!-- Specs Row (PN, WC, m2) -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <div class="bg-slate-200 h-3 w-12 rounded"></div>
                <div class="bg-slate-200 h-3 w-12 rounded"></div>
                <div class="bg-slate-200 h-3 w-12 rounded"></div>
            </div>

            <!-- Card Action (Ưu tiên hiển thị & Nút Xem chi tiết) -->
            <div class="mt-5 flex items-center justify-between">
                <div class="bg-slate-200 h-3.5 w-24 rounded"></div>
                <div class="bg-slate-300 h-8 w-24 rounded-xl"></div>
            </div>
        </div>
    </div>
</div>
@endfor

