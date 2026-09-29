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
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = trim($request->input('message'));
        $history = session()->get('ai_chat_history', []);

        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $result = $this->aiService->ask($userMessage, $history, $currentUser);

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
        ]);
    }

    /**
     * Làm mới lịch sử chat
     */
    public function resetChat(): JsonResponse
    {
        session()->forget('ai_chat_history');
        return response()->json([
            'status' => 'success',
            'message' => 'Cuộc trò chuyện đã được làm mới.',
        ]);
    }
}
