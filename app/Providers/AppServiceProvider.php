<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $defaultSettings = [
            'banner_title' => 'Tìm Nhà Cho Thuê Nhanh Chóng & Dễ Dàng',
            'banner_subtitle' => 'Hàng ngàn căn hộ, phòng trọ chất lượng cao được xác thực mỗi ngày tại RentHome',
            'banner_cta' => 'Khám phá ngay',
            'banner_url' => '',
            'brand_name' => 'RentHome',
            'brand_slogan' => 'Thuê nhà ước mơ',
            'logo_url' => '',
            'theme_color' => '#16a34a',
            'footer_phone' => '0903990706',
            'footer_email' => 'huydepgai@gmail.com',
            'footer_address' => 'Tòa nhà Landmark 81, Bình Thạnh, TP.HCM',
            'footer_copyright' => '© 2026 RentHome Inc. Tất cả quyền được bảo lưu.',
            'amenities' => [
                'Wifi tốc độ cao',
                'Điều hòa nhiệt độ',
                'Máy giặt chung',
                'Chỗ để xe miễn phí',
                'An ninh 24/7',
                'Giờ giấc tự do',
                'Tủ lạnh riêng'
            ]
        ];

        $settingsFile = storage_path('app/theme_settings.json');
        if (file_exists($settingsFile)) {
            $saved = json_decode(file_get_contents($settingsFile), true);
            $themeSettings = is_array($saved) ? array_merge($defaultSettings, $saved) : $defaultSettings;
        } else {
            $themeSettings = $defaultSettings;
        }

        \Illuminate\Support\Facades\View::share('themeSettings', $themeSettings);
    }
}
