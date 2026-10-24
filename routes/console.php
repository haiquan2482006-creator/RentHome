<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('billing:check-due')->dailyAt('08:00');
\Illuminate\Support\Facades\Schedule::command('billing:generate-monthly')->dailyAt('00:05');

// Tự động quét và kéo tin tức RSS Bất động sản mới định kỳ
\Illuminate\Support\Facades\Schedule::command('news:fetch-rss')->everyTwoHours();
\Illuminate\Support\Facades\Schedule::command('news:fetch-rss')->dailyAt('07:30');

