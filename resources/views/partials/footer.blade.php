@php
    $themeSettings = $themeSettings ?? (file_exists(storage_path('app/theme_settings.json')) 
        ? json_decode(file_get_contents(storage_path('app/theme_settings.json')), true) 
        : []);
    $fBrandName = $themeSettings['brand_name'] ?? 'RentHome';
    $fColor = $themeSettings['theme_color'] ?? '#16a34a';
    if (str_ends_with(strtolower($fBrandName), 'home')) {
        $fp1 = substr($fBrandName, 0, strlen($fBrandName) - 4);
        $fp2 = 'Home';
    } elseif (strlen($fBrandName) > 4) {
        $fmid = floor(strlen($fBrandName) / 2);
        $fp1 = substr($fBrandName, 0, $fmid);
        $fp2 = substr($fBrandName, $fmid);
    } else {
        $fp1 = $fBrandName;
        $fp2 = '';
    }
@endphp

<!-- Footer -->
<footer id="contact" class="bg-slate-900 text-slate-400 pt-16 pb-8 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
            <!-- Brand Info -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ !empty($themeSettings['logo_url']) ? $themeSettings['logo_url'] : asset('img/logo.png') }}"
                        alt="RentHome Logo" class="w-9 h-9 object-contain rounded-xl shadow-sm">
                    <span class="text-xl font-extrabold text-white">{{ $fp1 }}<span
                            style="color: {{ $fColor }}">{{ $fp2 }}</span></span>
                </div>
                <p class="text-xs leading-relaxed text-slate-400">
                    {{ $themeSettings['brand_slogan'] ?? 'Thuê nhà ước mơ' }}
                </p>
                <div class="flex gap-3 pt-2">
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs transition-colors hover:opacity-80"
                        style="background-color: {{ $fColor }}"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs transition-colors hover:opacity-80"
                        style="background-color: {{ $fColor }}"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs transition-colors hover:opacity-80"
                        style="background-color: {{ $fColor }}"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Danh Mục Hot</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#" class="hover:text-white transition-colors">Cho thuê căn hộ TP.HCM</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Cho thuê nhà nguyên căn Hà Nội</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Phòng trọ giá rẻ sinh viên</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Biệt thự Villa nghỉ dưỡng</a></li>
                </ul>
            </div>

            <!-- Support Links -->
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Hỗ Trợ Khách Hàng</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#" class="hover:text-white transition-colors">Trung tâm trợ giúp</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Quy định dịch vụ</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Giải quyết tranh chấp</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Liên Hệ</h4>
                <ul class="space-y-3 text-xs">
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-phone" style="color: {{ $fColor }}"></i> Hotline:
                        {{ $themeSettings['footer_phone'] ?? '0903990706' }}
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-envelope" style="color: {{ $fColor }}"></i> Email:
                        {{ $themeSettings['footer_email'] ?? 'huydepgai@gmail.com' }}
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fa-solid fa-location-dot mt-0.5" style="color: {{ $fColor }}"></i>
                        {{ $themeSettings['footer_address'] ?? 'Tòa nhà Landmark 81, Bình Thạnh, TP.HCM' }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>{{ $themeSettings['footer_copyright'] ?? '© 2026 RentHome Inc. Tất cả quyền được bảo lưu.' }}</p>
            <p>Thiết kế bởi Hoàng Hải - Laravel Framework 12</p>
        </div>
    </div>
</footer>
