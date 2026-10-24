<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NewsModerationController;
use Carbon\Carbon;

class FetchRssNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch-rss {--force : Bỏ qua giới hạn hạn mức 10 tin/ngày}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động quét và kéo tin tức BĐS mới nhất từ RSS các đầu báo lớn về danh sách chờ duyệt (Tối đa 10 tin/ngày)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeStr = Carbon::now()->format('d/m/Y H:i:s');
        $force = $this->option('force');
        $this->info("[{$timeStr}] Bắt đầu tiến trình tự động kéo tin RSS Bất Động Sản (Giới hạn tối đa 10 tin/ngày)...");

        $result = NewsModerationController::performRssFetch(10, $force);

        $newCount = $result['new_count'] ?? 0;
        $message = $result['message'] ?? '';
        $errors = $result['errors'] ?? [];

        $this->info($message);

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->warn("- {$err}");
            }
        }

        $this->info("Hoàn tất tác vụ quét tin RSS!");
        return Command::SUCCESS;
    }
}
