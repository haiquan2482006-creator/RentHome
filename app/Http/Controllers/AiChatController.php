<?php

namespace App\Http\Controllers;

use App\Services\RentHomeAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    protected RentHomeAiService $aiService;

    public function __construct(RentHomeAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Nhận tin nhắn chat từ người dùng và phản hồi
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $userMessage = trim((string)$request->input('message', ''));
        $history = session()->get('ai_chat_history', []);
        $currentUser = auth()->user();

        // Xử lý upload ảnh phòng từ người dùng (nếu có)
        $uploadedImageIds = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                try {
                    $base64 = base64_encode(file_get_contents($file->getRealPath()));
                    $mime = $file->getMimeType();
                    $img = \App\Models\Image::create([
                        'base64_data' => $base64,
                        'mime_type' => $mime,
                    ]);
                    $uploadedImageIds[] = (string) $img->_id;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("AI Chat image upload error: " . $e->getMessage());
                }
            }
            if (empty($userMessage)) {
                $userMessage = 'Tôi gửi kèm ' . count($uploadedImageIds) . ' ảnh phòng trọ để đăng tin cho thuê.';
            }
        }

        if (empty($userMessage)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng nhập nội dung tin nhắn hoặc tải ảnh lên!',
            ], 422);
        }

        $result = $this->aiService->ask($userMessage, $history, $currentUser, $uploadedImageIds);

        // Lưu lịch sử hội thoại vào session
        $history[] = ['role' => 'user', 'text' => $userMessage];
        $history[] = ['role' => 'assistant', 'text' => $result['reply']];
        if (count($history) > 10) {
            $history = array_slice($history, -10);
        }
        session()->put('ai_chat_history', $history);

        return response()->json([
            'status' => 'success',
            'reply' => $result['reply'],
            'cards' => $result['cards'] ?? [],
            'post_created' => $result['post_created'] ?? null,
            'has_draft' => session()->has('ai_auto_posting_draft'),
        ]);
    }

    /**
     * Làm mới lịch sử chat
     */
    public function resetChat(): JsonResponse
    {
        session()->forget('ai_chat_history');
        session()->forget('ai_auto_posting_draft');
        return response()->json([
            'status' => 'success',
            'message' => 'Cuộc trò chuyện đã được làm mới.',
        ]);
    }
}
