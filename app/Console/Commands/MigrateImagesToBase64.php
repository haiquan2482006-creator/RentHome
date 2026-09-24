<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Building;
use App\Models\Post;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateImagesToBase64 extends Command
{
    protected $signature = 'images:migrate';
    protected $description = 'Chuyển đổi ảnh vật lý cũ sang Base64 lưu vào MongoDB';

    public function handle()
    {
        $this->info('Đang chuyển đổi ảnh cho Buildings...');
        $this->migrateBuildings();

        $this->info('Đang chuyển đổi ảnh cho Posts...');
        $this->migratePosts();

        $this->info('Hoàn tất!');
    }

    private function migrateBuildings()
    {
        $buildings = Building::all();
        $count = 0;

        foreach ($buildings as $building) {
            if ($building->image && !Str::startsWith($building->image, 'http') && !Str::startsWith($building->image, 'data:')) {
                $path = str_replace('/storage/', '', $building->image);
                
                if (Storage::disk('public')->exists($path)) {
                    $fileContents = Storage::disk('public')->get($path);
                    $mimeType = Storage::disk('public')->mimeType($path);
                    $base64Data = base64_encode($fileContents);

                    $newImage = Image::create([
                        'base64_data' => $base64Data,
                        'mime_type' => $mimeType
                    ]);

                    $building->image = $newImage->_id;
                    $building->save();
                    $count++;
                    $this->info("Đã chuyển đổi ảnh cho Building ID: {$building->_id}");
                }
            }
        }
        $this->info("Đã chuyển đổi thành công {$count} ảnh cho Buildings.");
    }

    private function migratePosts()
    {
        $posts = Post::all();
        $count = 0;

        foreach ($posts as $post) {
            if ($post->images && is_array($post->images)) {
                $newImageIds = [];
                $updated = false;

                foreach ($post->images as $imagePath) {
                    if ($imagePath && !Str::startsWith($imagePath, 'http') && !Str::startsWith($imagePath, 'data:')) {
                        $path = str_replace('/storage/', '', $imagePath);
                        
                        if (Storage::disk('public')->exists($path)) {
                            $fileContents = Storage::disk('public')->get($path);
                            $mimeType = Storage::disk('public')->mimeType($path);
                            $base64Data = base64_encode($fileContents);

                            $newImage = Image::create([
                                'base64_data' => $base64Data,
                                'mime_type' => $mimeType
                            ]);

                            $newImageIds[] = $newImage->_id;
                            $updated = true;
                            $count++;
                        } else {
                            // Keep the old path if file not found, or maybe just drop it? We'll keep it.
                            $newImageIds[] = $imagePath;
                        }
                    } else {
                        // Already HTTP or data:, just keep it
                        $newImageIds[] = $imagePath;
                    }
                }

                if ($updated) {
                    $post->images = $newImageIds;
                    $post->save();
                    $this->info("Đã chuyển đổi ảnh cho Post ID: {$post->_id}");
                }
            }
        }
        $this->info("Đã chuyển đổi thành công {$count} ảnh cho Posts.");
    }
}
