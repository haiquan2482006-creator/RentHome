@php
    $currentUserName = '';
    if (Auth::check()) {
        $currentUserName = Auth::user()->account_name ?: (Auth::user()->username ?? '');
    }
    $currentUserSalutation = !empty($currentUserName) ? $currentUserName : 'bạn';

    $hour = (int) now('Asia/Ho_Chi_Minh')->format('H');
    if ($hour >= 5 && $hour < 11) {
        $welcomeTitle = "Xin chào <b>{$currentUserSalutation}</b>! Chúc <b>{$currentUserSalutation}</b> có một buổi sáng vui vẻ, tràn đầy năng lượng và một ngày làm việc thật hiệu quả! ☀️🌿";
    } elseif ($hour >= 11 && $hour < 14) {
        $welcomeTitle = "Xin chào <b>{$currentUserSalutation}</b>! Chúc <b>{$currentUserSalutation}</b> có một buổi trưa vui vẻ, bữa trưa ngon miệng và nghỉ ngơi thật thoải mái! 🌤️🍽️";
    } elseif ($hour >= 14 && $hour < 18) {
        $welcomeTitle = "Xin chào <b>{$currentUserSalutation}</b>! Chúc <b>{$currentUserSalutation}</b> có một buổi chiều vui vẻ, làm việc thuận lợi và tràn đầy may mắn! ⛅✨";
    } elseif ($hour >= 18 && $hour < 23) {
        $welcomeTitle = "Xin chào <b>{$currentUserSalutation}</b>! Chúc <b>{$currentUserSalutation}</b> có một buổi tối vui vẻ, ấm áp, thư thái và bình yên bên gia đình! 🌙🍵";
    } else {
        $welcomeTitle = "Xin chào <b>{$currentUserSalutation}</b>! Dù đã khuya đêm muộn nhưng RentHome vẫn luôn túc trực 24/7 đồng hành cùng <b>{$currentUserSalutation}</b>. Chúc <b>{$currentUserSalutation}</b> có giấc ngủ thật an lành và ngon giấc nhé! 🌌🛌";
    }
@endphp

<!-- RentHome AI Assistant Chat Widget -->
<style>
    @keyframes aiPulseGlow {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
        70% { box-shadow: 0 0 0 14px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    @keyframes aiSlideUpFade {
        0% { opacity: 0; transform: translateY(20px) scale(0.96); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes aiTypingWave {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.35; }
        30% { transform: translateY(-6px); opacity: 1; }
    }
    @keyframes aiWavingHand {
        0%, 100% { transform: rotate(0deg); }
        20%, 60% { transform: rotate(14deg); }
        40%, 80% { transform: rotate(-14deg); }
    }
    @keyframes aiTooltipFadeIn {
        0% {
            opacity: 0;
            transform: translateY(6px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes aiTooltipFadeOut {
        0% {
            opacity: 1;
            transform: translateY(0);
        }
        100% {
            opacity: 0;
            transform: translateY(-6px);
        }
    }

    .ai-help-tooltip {
        position: absolute;
        right: 60px;
        bottom: 30px;
        white-space: nowrap;
        background: #ffffff;
        color: #0f172a;
        padding: 9px 16px;
        border-radius: 20px;
        border-bottom-right-radius: 4px;
        box-shadow: 0 10px 25px -3px rgba(0,0,0,0.15), 0 4px 6px -2px rgba(0,0,0,0.05), 0 0 0 1px rgba(16,185,129,0.25);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 999998;
        user-select: none;
        transition: box-shadow 0.25s ease;
    }
    .ai-help-tooltip:hover {
        box-shadow: 0 12px 28px -2px rgba(16,185,129,0.32), 0 0 0 1.5px #10b981;
    }
    .ai-help-tooltip.show-anim {
        display: flex !important;
        animation: aiTooltipFadeIn 0.75s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
    .ai-help-tooltip.hide-anim {
        animation: aiTooltipFadeOut 0.75s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }

    .ai-typing-dot {
        width: 7px;
        height: 7px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
    }
    .ai-typing-dot:nth-child(1) { animation: aiTypingWave 1.3s infinite ease-in-out; }
    .ai-typing-dot:nth-child(2) { animation: aiTypingWave 1.3s infinite ease-in-out 0.2s; }
    .ai-typing-dot:nth-child(3) { animation: aiTypingWave 1.3s infinite ease-in-out 0.4s; }

    #renthome-ai-widget * {
        box-sizing: border-box;
    }
    #ai-messages-container::-webkit-scrollbar {
        width: 5px;
    }
    #ai-messages-container::-webkit-scrollbar-track {
        background: transparent;
    }
    #ai-messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #ai-messages-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    #ai-send-btn {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    #ai-send-btn:hover:not(:disabled) {
        transform: scale(1.06);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
    #ai-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    #ai-user-input:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18) !important;
        background: #ffffff !important;
    }
</style>

<div id="renthome-ai-widget" style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 999999 !important; font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;">
    <!-- Tooltip thông báo hỗ trợ (cứ 5s hiện 1 lần trong 4s) -->
    <div id="ai-help-tooltip" 
         class="ai-help-tooltip" 
         onclick="openRentHomeChat()" 
         title="Bấm để trò chuyện với Trợ lý RentHome">
        <span style="font-size: 16px; display: inline-block; animation: aiWavingHand 1.6s infinite; transform-origin: 70% 70%;">👋</span>
        <span style="letter-spacing: -0.2px;">Bạn cần giúp đỡ gì?</span>
        <!-- Mũi tên nhỏ chỉ sang nút chat tròn -->
        <span style="position: absolute; right: -7px; top: 50%; transform: translateY(-50%); width: 0; height: 0; border-top: 6px solid transparent; border-bottom: 6px solid transparent; border-left: 7px solid #ffffff; filter: drop-shadow(2px 0 1px rgba(0,0,0,0.06));"></span>
    </div>

    <!-- Nút mở chat tròn nổi (Chỉ hiển thị khi khung chat ĐANG ĐÓNG) -->
    <button id="ai-toggle-btn" 
            onclick="openRentHomeChat()" 
            style="width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.45); cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease; animation: aiPulseGlow 3s infinite;"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
            title="Trò chuyện cùng Trợ lý RentHome AI">
        <!-- Chấm xanh báo online -->
        <span style="position: absolute; top: 1px; right: 1px; width: 14px; height: 14px; background-color: #34d399; border: 2.5px solid #ffffff; border-radius: 50%; box-shadow: 0 0 8px #10b981;"></span>
        <!-- Icon Chat -->
        <svg style="width: 28px; height: 28px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </button>

    <!-- Khung cửa sổ Chat (Hiện đại, bo tròn mềm mại, không có nút X thừa bên ngoài) -->
    <div id="ai-chat-window"
         style="display: none; width: 395px; max-width: calc(100vw - 32px); height: 590px; max-height: calc(100vh - 80px); background: #ffffff; border-radius: 22px; box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.06); overflow: hidden; flex-direction: column; animation: aiSlideUpFade 0.28s cubic-bezier(0.16, 1, 0.3, 1);">
        
        <!-- Header hiện đại -->
        <div style="background: linear-gradient(135deg, #059669 0%, #047857 55%, #064e3b 100%); color: #ffffff; padding: 15px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); position: relative;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 14px; background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; font-size: 22px; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    🤖
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 15.5px; letter-spacing: -0.3px; display: flex; align-items: center; gap: 6px;">
                        Trợ lý Tư vấn RentHome
                    </div>
                    <div style="font-size: 11px; opacity: 0.92; display: flex; align-items: center; gap: 6px; margin-top: 3px;">
                        <span style="display: inline-block; width: 8px; height: 8px; background-color: #34d399; border-radius: 50%; box-shadow: 0 0 6px #34d399;"></span>
                        Trực tuyến 24/7 • Hỗ trợ tức thì
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <!-- Nút Làm mới hội thoại -->
                <button onclick="resetRentHomeChat()" 
                        title="Làm mới cuộc trò chuyện" 
                        style="background: rgba(255,255,255,0.16); border: none; color: #ffffff; border-radius: 10px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.16)'">
                    <svg style="width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <!-- Nút Đóng / Thu nhỏ (Duy nhất 1 nút đóng trên header) -->
                <button onclick="closeRentHomeChat()" 
                        title="Thu nhỏ khung chat" 
                        style="background: rgba(255,255,255,0.16); border: none; color: #ffffff; border-radius: 10px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;"
                        onmouseover="this.style.background='rgba(239, 68, 68, 0.4)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.16)'">
                    <svg style="width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-width: 2.2;" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Khung nội dung tin nhắn -->
        <div id="ai-messages-container" style="flex: 1; padding: 18px 16px; overflow-y: auto; overflow-x: hidden; word-break: break-word; background-color: #f8fafc; font-size: 13.5px; display: flex; flex-direction: column; gap: 14px;">
            <!-- Tin nhắn chào mừng ban đầu -->
            <div style="display: flex; align-items: flex-start; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    AI
                </div>
                <div style="background: #ffffff; padding: 14px 16px; border-radius: 18px; border-top-left-radius: 3px; border: 1px solid #e2e8f0; color: #1e293b; max-width: 86%; line-height: 1.55; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div>{!! $welcomeTitle !!}</div>
                    <div style="margin-top: 8px; color: #475569;">Đây là trợ lý tư vấn của <b>RentHome</b>. 🌸</div>
                    <div style="margin-top: 10px; font-weight: 600; color: #334155;">Hệ thống sẵn sàng hỗ trợ {{ $currentUserSalutation }}:</div>
                    <ul style="margin: 6px 0 0 0; padding-left: 18px; color: #475569; font-size: 12.5px; list-style-type: disc;">
                        <li><b>Tra cứu phòng trọ:</b> Theo quận, tầm giá, tiện ích.</li>
                        <li><b>✨ Tự động đăng tin:</b> Gửi thông tin & ảnh phòng qua chat để AI tạo bài.</li>
                        <li><b>Hướng dẫn hệ thống:</b> Đăng tin, tạo tòa nhà, quản lý phòng.</li>
                        <li><b>Tiếp nhận khiếu nại:</b> Báo cáo chủ trọ, tin giả, sự cố.</li>
                    </ul>
                    <div style="margin-top: 10px; font-weight: 500; color: #047857;">{{ $currentUserSalutation }} cần hỗ trợ việc gì lúc này?</div>
                </div>
            </div>


            <!-- Khung 3 dấu chấm đang soạn tin nhắn của AI (Typing Wave Indicator) -->
            <div id="ai-typing-indicator" style="display: none; align-items: flex-start; gap: 10px; margin-top: 4px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    AI
                </div>
                <div style="background: #ffffff; padding: 13px 18px; border-radius: 18px; border-top-left-radius: 3px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <span class="ai-typing-dot"></span>
                    <span class="ai-typing-dot"></span>
                    <span class="ai-typing-dot"></span>
                </div>
            </div>
        </div>

        <!-- Khung xem trước ảnh phòng đính kèm -->
        <div id="ai-attached-images-preview" style="display: none; padding: 8px 14px; background: #f8fafc; border-top: 1px dashed #cbd5e1; gap: 8px; align-items: center; overflow-x: auto;">
        </div>

        <!-- Ô nhập câu hỏi & nút đính kèm ảnh -->
        <form id="ai-chat-form" onsubmit="handleSendAiMessage(event)" style="padding: 10px 14px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; gap: 8px; align-items: center;">
            <input type="file" id="ai-image-file-input" accept="image/*" multiple style="display: none;" onchange="handleAiFileSelect(event)">
            
            <button type="button" 
                    id="ai-attach-btn" 
                    onclick="document.getElementById('ai-image-file-input').click()" 
                    title="Đính kèm ảnh phòng để đăng tin"
                    style="width: 38px; height: 38px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 50%; color: #475569; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s ease;"
                    onmouseover="this.style.background='#e2e8f0'; this.style.color='#059669'"
                    onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">
                <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>

            <input type="text" 
                   id="ai-user-input" 
                   placeholder="Nhập yêu cầu tư vấn hoặc thông tin phòng..." 
                   autocomplete="off"
                   style="flex: 1; padding: 10px 14px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 24px; font-size: 13.5px; outline: none; transition: all 0.2s ease;">
            
            <button type="submit" 
                    id="ai-send-btn" 
                    title="Gửi tin nhắn"
                    style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 50%; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
                <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.2; margin-left: 2px;" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    const AI_CHAT_ENDPOINT = "{{ url('/ai/chat') }}";
    const AI_RESET_ENDPOINT = "{{ url('/ai/reset') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    let aiTooltipTimer = null;
    let aiTooltipHideTimer = null;

    function showAiHelpTooltip() {
        const tooltip = document.getElementById('ai-help-tooltip');
        const win = document.getElementById('ai-chat-window');
        if (!tooltip || !win || win.style.display === 'flex') return;

        tooltip.classList.remove('hide-anim');
        tooltip.classList.add('show-anim');

        // Tồn tại trong 4 giây rồi ẩn
        clearTimeout(aiTooltipHideTimer);
        aiTooltipHideTimer = setTimeout(() => {
            hideAiHelpTooltip();
        }, 4000);
    }

    function hideAiHelpTooltip() {
        const tooltip = document.getElementById('ai-help-tooltip');
        if (!tooltip) return;

        tooltip.classList.remove('show-anim');
        tooltip.classList.add('hide-anim');
        setTimeout(() => {
            if (tooltip.classList.contains('hide-anim')) {
                tooltip.classList.remove('hide-anim');
                tooltip.style.display = 'none';
            }
        }, 750);
    }

    function startAiTooltipLoop() {
        stopAiTooltipLoop();
        function runCycle() {
            // Cứ 5s lại hiện thông báo, tồn tại trong 4s (chu kỳ: hiện 4s + nghỉ 5s = 9s)
            aiTooltipTimer = setTimeout(() => {
                showAiHelpTooltip();
                aiTooltipTimer = setTimeout(() => {
                    runCycle();
                }, 4000 + 5000);
            }, 5000);
        }
        runCycle();
    }

    function stopAiTooltipLoop() {
        clearTimeout(aiTooltipTimer);
        clearTimeout(aiTooltipHideTimer);
        const tooltip = document.getElementById('ai-help-tooltip');
        if (tooltip) {
            tooltip.classList.remove('show-anim', 'hide-anim');
            tooltip.style.display = 'none';
        }
    }

    function openRentHomeChat() {
        const win = document.getElementById('ai-chat-window');
        const toggleBtn = document.getElementById('ai-toggle-btn');
        
        stopAiTooltipLoop();

        // Mở khung chat và ẩn hoàn toàn nút tròn bên ngoài (bỏ nút X thừa)
        win.style.display = 'flex';
        toggleBtn.style.display = 'none';
        setTimeout(() => document.getElementById('ai-user-input').focus(), 120);
        scrollChatToBottom();
    }

    function closeRentHomeChat() {
        const win = document.getElementById('ai-chat-window');
        const toggleBtn = document.getElementById('ai-toggle-btn');
        
        // Đóng khung chat và hiện lại nút tròn nổi
        win.style.display = 'none';
        toggleBtn.style.display = 'flex';

        // Tiếp tục chu kỳ hiện thông báo hỗ trợ cứ 5s
        startAiTooltipLoop();
    }

    function toggleRentHomeChat() {
        const win = document.getElementById('ai-chat-window');
        if (win.style.display === 'none' || !win.style.display) {
            openRentHomeChat();
        } else {
            closeRentHomeChat();
        }
    }

    let selectedAiImages = [];

    function handleAiFileSelect(event) {
        const files = Array.from(event.target.files);
        if (!files.length) return;
        selectedAiImages = selectedAiImages.concat(files).slice(0, 5); // Tối đa 5 ảnh
        renderImagePreviews();
        event.target.value = '';
    }

    function removeAiImage(index) {
        selectedAiImages.splice(index, 1);
        renderImagePreviews();
    }

    function renderImagePreviews() {
        const container = document.getElementById('ai-attached-images-preview');
        if (!container) return;
        if (selectedAiImages.length === 0) {
            container.style.display = 'none';
            container.innerHTML = '';
            return;
        }
        container.style.display = 'flex';
        container.innerHTML = selectedAiImages.map((file, idx) => {
            const url = URL.createObjectURL(file);
            return `
                <div style="position: relative; display: inline-block; width: 48px; height: 48px; border-radius: 8px; overflow: hidden; border: 1.5px solid #10b981; flex-shrink: 0; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
                    <img src="${url}" style="width: 100%; height: 100%; object-fit: cover;">
                    <button type="button" onclick="removeAiImage(${idx})" style="position: absolute; top: 1px; right: 1px; width: 16px; height: 16px; border-radius: 50%; background: rgba(0,0,0,0.65); color: #fff; border: none; font-size: 11px; display: flex; align-items: center; justify-content: center; cursor: pointer; line-height: 1;">&times;</button>
                </div>
            `;
        }).join('');
    }

    function sendQuickPrompt(promptText) {
        document.getElementById('ai-user-input').value = promptText;
        handleSendAiMessage(new Event('submit'));
    }

    async function handleSendAiMessage(e) {
        if (e) e.preventDefault();
        const input = document.getElementById('ai-user-input');
        const text = input.value.trim();
        if (!text && selectedAiImages.length === 0) return;

        // Hiển thị bong bóng tin nhắn của người dùng kèm ảnh (nếu có)
        let imageThumbnailsHtml = '';
        if (selectedAiImages.length > 0) {
            imageThumbnailsHtml = '<div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px;">' +
                selectedAiImages.map(f => `<img src="${URL.createObjectURL(f)}" style="width: 52px; height: 52px; border-radius: 8px; object-fit: cover; border: 1.5px solid rgba(255,255,255,0.7); box-shadow: 0 1px 4px rgba(0,0,0,0.15);">`).join('') +
                '</div>';
        }
        const userDisplayText = text ? text : (selectedAiImages.length > 0 ? `Đã gửi ${selectedAiImages.length} ảnh phòng trọ đính kèm 📸` : '');
        appendMessage('user', userDisplayText + imageThumbnailsHtml);

        const currentFiles = [...selectedAiImages];
        selectedAiImages = [];
        renderImagePreviews();

        input.value = '';
        input.disabled = true;
        document.getElementById('ai-send-btn').disabled = true;
        const attachBtn = document.getElementById('ai-attach-btn');
        if (attachBtn) attachBtn.disabled = true;

        // Hiển thị bong bóng 3 dấu chấm đang soạn tin của AI
        const typing = document.getElementById('ai-typing-indicator');
        const container = document.getElementById('ai-messages-container');
        container.appendChild(typing);
        typing.style.display = 'flex';
        scrollChatToBottom();

        try {
            const formData = new FormData();
            formData.append('message', text);
            currentFiles.forEach(f => {
                formData.append('images[]', f);
            });

            const res = await fetch(AI_CHAT_ENDPOINT, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            typing.style.display = 'none';

            if (data.status === 'success') {
                appendMessage('ai', data.reply, data.cards);
            } else {
                appendMessage('ai', data.message || 'Hệ thống đang gặp trục trặc kết nối. Vui lòng thử lại sau giây lát!');
            }
        } catch (err) {
            console.error(err);
            typing.style.display = 'none';
            appendMessage('ai', 'Kết nối mạng không ổn định, vui lòng gửi lại tin nhắn!');
        } finally {
            input.disabled = false;
            document.getElementById('ai-send-btn').disabled = false;
            if (attachBtn) attachBtn.disabled = false;
            input.focus();
            scrollChatToBottom();
        }
    }

    function renderSingleAiCard(card) {
        if (!card) return '';
        let badgeStyle = 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;';
        if (card.is_match) {
            badgeStyle = 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;';
        } else if ((card.card_type && card.card_type === 'budget') || (card.badge && (card.badge.includes('Tiết kiệm') || card.badge.includes('Tối ưu')))) {
            badgeStyle = 'background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;';
        } else if ((card.card_type && card.card_type === 'premium') || (card.badge && (card.badge.includes('Sang trọng') || card.badge.includes('Cao cấp') || card.badge.includes('Chất lượng')))) {
            badgeStyle = 'background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff;';
        }
        const badgeHtml = card.badge 
            ? `<span style="display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 6px; margin-bottom: 3px; ${badgeStyle}">${card.badge}</span>` 
            : '';

        return `
            <div onclick="window.location.href='${card.url}'" style="margin: 10px 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; gap: 10px; padding: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#10b981'; this.style.boxShadow='0 6px 14px rgba(16, 185, 129, 0.15)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.04)';">
                <img src="${card.image}" alt="${card.title}" style="width: 85px; height: 80px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        ${badgeHtml ? `<div>${badgeHtml}</div>` : ''}
                        <div style="font-weight: 600; font-size: 12.5px; color: #0f172a; line-height: 1.25;">${card.title}</div>
                        <div style="color: #e11d48; font-weight: 700; font-size: 11.5px; margin-top: 2px;">${card.price} <span style="color: #64748b; font-weight: normal; font-size: 11px;">(${card.area})</span></div>
                        <div style="color: #64748b; font-size: 10.5px; margin-top: 2px;">📍 ${card.address}</div>
                    </div>
                    <a href="${card.url}" style="font-size: 11px; color: #059669; font-weight: 600; text-decoration: none; margin-top: 4px; display: inline-flex; align-items: center; gap: 3px;">
                        Xem chi tiết phòng &rarr;
                    </a>
                </div>
            </div>
        `;
    }

    function appendMessage(role, text, cards = []) {
        const container = document.getElementById('ai-messages-container');
        const typing = document.getElementById('ai-typing-indicator');
        const msgWrapper = document.createElement('div');
        msgWrapper.style.display = 'flex';
        msgWrapper.style.alignItems = 'flex-start';
        msgWrapper.style.gap = '10px';
        if (role === 'user') {
            msgWrapper.style.justifyContent = 'flex-end';
        }

        let formattedText = text ? text
            .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
            .replace(/\*(.*?)\*/g, '<i>$1</i>')
            .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" style="color: #059669; font-weight: 700; text-decoration: underline;">$1</a>')
            .replace(/\n/g, '<br>') : '';

        if (role === 'user') {
            msgWrapper.innerHTML = `
                <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 11px 16px; border-radius: 18px; border-top-right-radius: 4px; max-width: 85%; font-size: 13.5px; line-height: 1.5; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                    ${formattedText}
                </div>
            `;
        } else {
            let embeddedCardIds = new Set();

            if (cards && cards.length > 0) {
                let budgetCard = cards.find(c => c.card_type === 'budget') || cards.find(c => c.badge && (c.badge.includes('Tiết kiệm') || c.badge.includes('Tối ưu')));
                let premiumCard = cards.find(c => c.card_type === 'premium') || cards.find(c => c.badge && (c.badge.includes('Sang trọng') || c.badge.includes('Cao cấp') || c.badge.includes('Chất lượng')));

                // 1. Kiểm tra placeholder [[CARD_BUDGET]]
                if (formattedText.includes('[[CARD_BUDGET]]')) {
                    if (budgetCard) {
                        formattedText = formattedText.replace(/(?:<br\s*\/?>\s*)*\[\[CARD_BUDGET\]\](?:<br\s*\/?>\s*)*/g, renderSingleAiCard(budgetCard));
                        embeddedCardIds.add(budgetCard.id || budgetCard.title);
                    } else {
                        formattedText = formattedText.replace(/(?:<br\s*\/?>\s*)*\[\[CARD_BUDGET\]\](?:<br\s*\/?>\s*)*/g, '');
                    }
                }

                // 2. Kiểm tra placeholder [[CARD_PREMIUM]]
                if (formattedText.includes('[[CARD_PREMIUM]]')) {
                    if (premiumCard) {
                        formattedText = formattedText.replace(/(?:<br\s*\/?>\s*)*\[\[CARD_PREMIUM\]\](?:<br\s*\/?>\s*)*/g, renderSingleAiCard(premiumCard));
                        embeddedCardIds.add(premiumCard.id || premiumCard.title);
                    } else {
                        formattedText = formattedText.replace(/(?:<br\s*\/?>\s*)*\[\[CARD_PREMIUM\]\](?:<br\s*\/?>\s*)*/g, '');
                    }
                }

                // 3. Dự phòng thông minh: Nếu chưa có placeholder nhưng nhắc tới giải pháp ngân sách & giải pháp sang trọng
                if (embeddedCardIds.size === 0 && budgetCard && premiumCard) {
                    const budgetIndex = formattedText.search(/(?:Giải pháp tối ưu ngân sách|tối ưu ngân sách)/i);
                    const premiumIndex = formattedText.search(/(?:Giải pháp sang trọng|sang trọng & cao cấp|sang trọng)/i);

                    if (budgetIndex !== -1 && premiumIndex !== -1 && budgetIndex < premiumIndex) {
                        const partBeforePremium = formattedText.substring(0, premiumIndex);
                        const partAfterPremium = formattedText.substring(premiumIndex);

                        const newPart1 = partBeforePremium.replace(/(<br\s*\/?>\s*)+$/, '') + renderSingleAiCard(budgetCard) + '<br><br>';
                        
                        let newPart2 = partAfterPremium;
                        const closingIndex = newPart2.search(/(?:xem qua các hình ảnh|Nếu ưng ý căn nào|nếu có nhu cầu)/i);
                        if (closingIndex !== -1) {
                            const beforeClosing = newPart2.substring(0, closingIndex).replace(/(<br\s*\/?>\s*)+$/, '');
                            const closingText = newPart2.substring(closingIndex);
                            newPart2 = beforeClosing + renderSingleAiCard(premiumCard) + '<br><br>' + closingText;
                        } else {
                            newPart2 = newPart2 + renderSingleAiCard(premiumCard);
                        }

                        formattedText = newPart1 + newPart2;
                        embeddedCardIds.add(budgetCard.id || budgetCard.title);
                        embeddedCardIds.add(premiumCard.id || premiumCard.title);
                    }
                }
            }

            // Render các thẻ còn lại chưa được nhúng inline (ví dụ kết quả tìm kiếm khớp chính xác)
            let remainingCards = (cards || []).filter(c => !embeddedCardIds.has(c.id || c.title));
            let cardsHtml = '';
            if (remainingCards.length > 0) {
                cardsHtml = '<div style="margin-top: 10px; display: flex; flex-direction: column; gap: 8px;">';
                remainingCards.forEach(card => {
                    cardsHtml += renderSingleAiCard(card);
                });
                cardsHtml += '</div>';
            }

            msgWrapper.innerHTML = `
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    AI
                </div>
                <div style="background: #ffffff; padding: 13px 15px; border-radius: 18px; border-top-left-radius: 3px; border: 1px solid #e2e8f0; color: #1e293b; max-width: 86%; font-size: 13.5px; line-height: 1.55; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div>${formattedText}</div>
                    ${cardsHtml}
                </div>
            `;
        }

        // Chèn tin nhắn trước typing indicator
        container.insertBefore(msgWrapper, typing);
        scrollChatToBottom();
    }

    function getTimeGreeting(salutation) {
        const hour = new Date().getHours();
        if (hour >= 5 && hour < 11) {
            return `Xin chào <b>${salutation}</b>! Chúc <b>${salutation}</b> có một buổi sáng vui vẻ, tràn đầy năng lượng và một ngày làm việc thật hiệu quả! ☀️🌿`;
        } else if (hour >= 11 && hour < 14) {
            return `Xin chào <b>${salutation}</b>! Chúc <b>${salutation}</b> có một buổi trưa vui vẻ, bữa trưa ngon miệng và nghỉ ngơi thật thoải mái! 🌤️🍽️`;
        } else if (hour >= 14 && hour < 18) {
            return `Xin chào <b>${salutation}</b>! Chúc <b>${salutation}</b> có một buổi chiều vui vẻ, làm việc thuận lợi và tràn đầy may mắn! ⛅✨`;
        } else if (hour >= 18 && hour < 23) {
            return `Xin chào <b>${salutation}</b>! Chúc <b>${salutation}</b> có một buổi tối vui vẻ, ấm áp, thư thái và bình yên bên gia đình! 🌙🍵`;
        } else {
            return `Xin chào <b>${salutation}</b>! Dù đã khuya đêm muộn nhưng RentHome vẫn luôn túc trực 24/7 đồng hành cùng <b>${salutation}</b>. Chúc <b>${salutation}</b> có giấc ngủ thật an lành và ngon giấc nhé! 🌌🛌`;
        }
    }

    async function resetRentHomeChat() {
        if (!confirm('Bạn có chắc muốn làm mới phiên hội thoại không?')) return;
        try {
            await fetch(AI_RESET_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            });
            const container = document.getElementById('ai-messages-container');
            const typing = document.getElementById('ai-typing-indicator');
            const salutation = "{{ $currentUserSalutation }}";
            const greeting = getTimeGreeting(salutation);

            container.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">AI</div>
                    <div style="background: #ffffff; padding: 14px 16px; border-radius: 18px; border-top-left-radius: 3px; border: 1px solid #e2e8f0; color: #1e293b; max-width: 86%; font-size: 13.5px; line-height: 1.55; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div>${greeting}</div>
                        <div style="margin-top: 8px;">Đã làm mới phiên tư vấn! ${salutation} đang quan tâm đến phòng trọ khu vực nào hoặc cần hỗ trợ việc gì?</div>
                    </div>
                </div>
            `;
            container.appendChild(typing);
        } catch (err) {
            console.error(err);
        }
    }

    function scrollChatToBottom() {
        const container = document.getElementById('ai-messages-container');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 50);
    }

    // Tự động kích hoạt chu kỳ thông báo "Bạn cần giúp đỡ gì?" (hiện mỗi 5s, tồn tại 4s)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startAiTooltipLoop);
    } else {
        startAiTooltipLoop();
    }
</script>