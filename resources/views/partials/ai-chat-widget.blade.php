<!-- RentHome AI Assistant Chat Widget -->
<div id="renthome-ai-widget" style="position: fixed !important; bottom: 28px !important; right: 28px !important; z-index: 999999 !important; font-family: 'Plus Jakarta Sans', system-ui, sans-serif;">
    <!-- Nút mở chat tròn nổi (FAB) -->
    <button id="ai-toggle-btn" 
            onclick="toggleRentHomeChat()" 
            style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.5); cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; transition: transform 0.3s ease, box-shadow 0.3s ease; outline: none;"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
            title="Trò chuyện cùng Chuyên viên Tư vấn AI RentHome">
        <span style="position: absolute; top: 0px; right: 0px; width: 14px; height: 14px; background-color: #34d399; border: 2.5px solid #ffffff; border-radius: 50%; box-shadow: 0 0 10px #10b981;"></span>
        <!-- Icon Chat -->
        <svg id="ai-icon-chat" style="width: 30px; height: 30px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <!-- Icon Đóng (ẩn mặc định) -->
        <svg id="ai-icon-close" style="width: 30px; height: 30px; display: none; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Khung cửa sổ Chat -->
    <div id="ai-chat-window"
         style="display: none; width: 390px; max-width: 90vw; height: 570px; max-height: 84vh; background: #ffffff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; flex-direction: column; margin-bottom: 16px; border: 1px solid #e2e8f0; animation: aiSlideUp 0.3s ease-out;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: #ffffff; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 22px; border: 1px solid rgba(255,255,255,0.3);">
                    🤖
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 15px; letter-spacing: -0.3px;">Chuyên viên Tư vấn RentHome</div>
                    <div style="font-size: 11px; opacity: 0.9; display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                        <span style="display: inline-block; width: 7px; height: 7px; background-color: #34d399; border-radius: 50%;"></span> Trực tuyến 24/7 • Hỗ trợ tự động
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <button onclick="resetRentHomeChat()" title="Làm mới cuộc trò chuyện" style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <svg style="width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
                <button onclick="toggleRentHomeChat()" title="Đóng cửa sổ" style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Khung nội dung tin nhắn -->
        <div id="ai-messages-container" style="flex: 1; padding: 16px; overflow-y: auto; background-color: #f8fafc; font-size: 13.5px; display: flex; flex-direction: column; gap: 14px;">
            <!-- Tin nhắn chào mừng ban đầu -->
            <div style="display: flex; align-items: flex-start; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #d1fae5; color: #047857; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; flex-shrink: 0;">
                    AI
                </div>
                <div style="background: #ffffff; padding: 12px 14px; border-radius: 16px; border-top-left-radius: 2px; border: 1px solid #e2e8f0; color: #1e293b; max-width: 85%; line-height: 1.55; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    Dạ em chào Anh/Chị! Em là chuyên viên tư vấn của <b>RentHome</b>. 🌸
                    <br><br>
                    Em có thể tự động hỗ trợ Anh/Chị:
                    <ul style="margin: 6px 0 0 0; padding-left: 18px; color: #475569; font-size: 12.5px; list-style-type: disc;">
                        <li><b>Tra cứu phòng trọ:</b> Theo quận, tầm giá, tiện ích.</li>
                        <li><b>Hướng dẫn hệ thống:</b> Đăng tin, tạo tòa nhà, quản lý phòng.</li>
                        <li><b>Tiếp nhận khiếu nại:</b> Báo cáo chủ trọ, tin giả, sai giá.</li>
                    </ul>
                    <br>
                    Anh/Chị cần em hỗ trợ việc gì hôm nay ạ?
                </div>
            </div>

            <!-- Gợi ý câu hỏi nhanh -->
            <div id="ai-quick-suggestions" style="display: flex; flex-wrap: wrap; gap: 6px; padding-top: 4px;">
                <button onclick="sendQuickPrompt('Tìm giúp em phòng trọ quanh Cầu Giấy dưới 4 triệu')" style="font-size: 11.5px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 6px 12px; border-radius: 20px; cursor: pointer;">
                    🔍 Tìm phòng Cầu Giấy < 4tr
                </button>
                <button onclick="sendQuickPrompt('Hướng dẫn em các bước đăng tin cho thuê phòng')" style="font-size: 11.5px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 20px; cursor: pointer;">
                    📝 Cách đăng tin
                </button>
                <button onclick="sendQuickPrompt('Làm sao để tạo và quản lý tòa nhà mới?')" style="font-size: 11.5px; background: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; padding: 6px 12px; border-radius: 20px; cursor: pointer;">
                    🏢 Tạo tòa nhà
                </button>
                <button onclick="sendQuickPrompt('Em muốn gửi khiếu nại về bài đăng lừa đảo')" style="font-size: 11.5px; background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; padding: 6px 12px; border-radius: 20px; cursor: pointer;">
                    ⚠️ Gửi khiếu nại
                </button>
            </div>
        </div>

        <!-- Trạng thái Đang tìm kiếm -->
        <div id="ai-typing-indicator" style="display: none; padding: 10px 16px; background: #f8fafc; font-size: 12px; color: #059669; align-items: center; gap: 8px; border-top: 1px solid #f1f5f9;">
            <span>⏳ Dạ em đang tra cứu dữ liệu hệ thống, Anh/Chị đợi em một xíu nhé...</span>
        </div>

        <!-- Ô nhập câu hỏi -->
        <form id="ai-chat-form" onsubmit="handleSendAiMessage(event)" style="padding: 12px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; gap: 8px;">
            <input type="text" 
                   id="ai-user-input" 
                   placeholder="Nhập câu hỏi hoặc yêu cầu tư vấn..." 
                   autocomplete="off"
                   style="flex: 1; padding: 10px 14px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; outline: none;">
            <button type="submit" 
                    id="ai-send-btn" 
                    style="padding: 10px 16px; background: #059669; border: none; border-radius: 12px; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>
</div>

<script>
    const AI_CHAT_ENDPOINT = "{{ url('/ai/chat') }}";
    const AI_RESET_ENDPOINT = "{{ url('/ai/reset') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    function toggleRentHomeChat() {
        const win = document.getElementById('ai-chat-window');
        const iconChat = document.getElementById('ai-icon-chat');
        const iconClose = document.getElementById('ai-icon-close');
        
        if (win.style.display === 'none' || !win.style.display) {
            win.style.display = 'flex';
            iconChat.style.display = 'none';
            iconClose.style.display = 'block';
            setTimeout(() => document.getElementById('ai-user-input').focus(), 100);
        } else {
            win.style.display = 'none';
            iconChat.style.display = 'block';
            iconClose.style.display = 'none';
        }
    }

    function sendQuickPrompt(promptText) {
        document.getElementById('ai-user-input').value = promptText;
        handleSendAiMessage(new Event('submit'));
    }

    async function handleSendAiMessage(e) {
        if (e) e.preventDefault();
        const input = document.getElementById('ai-user-input');
        const text = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';
        input.disabled = true;
        document.getElementById('ai-send-btn').disabled = true;

        const typing = document.getElementById('ai-typing-indicator');
        typing.style.display = 'flex';
        scrollChatToBottom();

        try {
            const res = await fetch(AI_CHAT_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            typing.style.display = 'none';

            if (data.status === 'success') {
                appendMessage('ai', data.reply, data.cards);
            } else {
                appendMessage('ai', 'Dạ rất tiếc em đang gặp trục trặc khi kết nối hệ thống. Anh/Chị vui lòng thử lại sau giây lát nhé ạ!');
            }
        } catch (err) {
            console.error(err);
            typing.style.display = 'none';
            appendMessage('ai', 'Dạ hệ thống mạng đang có chút chập chờn, Anh/Chị thử nhắn lại giúp em nha!');
        } finally {
            input.disabled = false;
            document.getElementById('ai-send-btn').disabled = false;
            input.focus();
            scrollChatToBottom();
        }
    }

    function appendMessage(role, text, cards = []) {
        const container = document.getElementById('ai-messages-container');
        const msgWrapper = document.createElement('div');
        msgWrapper.style.display = 'flex';
        msgWrapper.style.alignItems = 'flex-start';
        msgWrapper.style.gap = '10px';
        if (role === 'user') {
            msgWrapper.style.justifyContent = 'flex-end';
        }

        let formattedText = text ? text.replace(/\n/g, '<br>') : '';

        if (role === 'user') {
            msgWrapper.innerHTML = `
                <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 10px 14px; border-radius: 16px; border-top-right-radius: 2px; max-width: 85%; font-size: 13.5px; line-height: 1.5; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    ${formattedText}
                </div>
            `;
        } else {
            let cardsHtml = '';
            if (cards && cards.length > 0) {
                cardsHtml = '<div style="margin-top: 10px; display: flex; flex-direction: column; gap: 8px;">';
                cards.forEach(card => {
                    cardsHtml += `
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; display: flex; gap: 8px; padding: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <img src="${card.image}" alt="${card.title}" style="width: 80px; height: 70px; object-fit: cover; border-radius: 6px;">
                            <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="font-weight: 600; font-size: 12px; color: #0f172a; line-height: 1.2;">${card.title}</div>
                                    <div style="color: #e11d48; font-weight: bold; font-size: 11px; margin-top: 2px;">${card.price} <span style="color: #64748b; font-weight: normal;">(${card.area})</span></div>
                                    <div style="color: #64748b; font-size: 10px; margin-top: 2px;">📍 ${card.address}</div>
                                </div>
                                <a href="${card.url}" target="_blank" style="font-size: 11px; color: #059669; font-weight: 600; text-decoration: none; margin-top: 4px;">
                                    Xem chi tiết phòng &rarr;
                                </a>
                            </div>
                        </div>
                    `;
                });
                cardsHtml += '</div>';
            }

            msgWrapper.innerHTML = `
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #d1fae5; color: #047857; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; flex-shrink: 0;">
                    AI
                </div>
                <div style="background: white; padding: 12px 14px; border-radius: 16px; border-top-left-radius: 2px; border: 1px solid #e2e8f0; color: #1e293b; max-width: 85%; font-size: 13.5px; line-height: 1.55; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div>${formattedText}</div>
                    ${cardsHtml}
                </div>
            `;
        }

        container.appendChild(msgWrapper);
        scrollChatToBottom();
    }

    async function resetRentHomeChat() {
        if (!confirm('Anh/Chị có chắc muốn làm mới phiên hội thoại không ạ?')) return;
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
            container.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #d1fae5; color: #047857; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; flex-shrink: 0;">AI</div>
                    <div style="background: white; padding: 12px 14px; border-radius: 16px; border-top-left-radius: 2px; border: 1px solid #e2e8f0; color: #1e293b; max-width: 85%; font-size: 13.5px; line-height: 1.55;">
                        Dạ em đã làm mới phiên tư vấn rồi ạ! Anh/Chị đang quan tâm đến phòng trọ khu vực nào hoặc cần em hỗ trợ thao tác gì ạ?
                    </div>
                </div>
            `;
        } catch (err) {
            console.error(err);
        }
    }

    function scrollChatToBottom() {
        const container = document.getElementById('ai-messages-container');
        container.scrollTop = container.scrollHeight;
    }
</script>