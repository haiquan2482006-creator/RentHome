<!-- ================= HỆ THỐNG REAL-TIME SHORT POLLING (TỰ ĐỘNG PREPEND DỮ LIỆU MỚI) ================= -->
@once
<style id="renthome-realtime-polling-styles">
    @keyframes realtimePrependFlash {
        0% {
            opacity: 0;
            transform: translateY(-12px);
            background-color: #dbeafe !important;
        }
        40% {
            opacity: 1;
            transform: translateY(0);
            background-color: #e0f2fe !important;
        }
        100% {
            background-color: transparent;
        }
    }
    .realtime-prepended {
        animation: realtimePrependFlash 2.5s ease-out forwards !important;
    }
    .realtime-live-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: realtimeLivePulse 2s infinite;
    }
    @keyframes realtimeLivePulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

<script>
    (function () {
        if (window.__rentHomeRealtimePollingInitialized) return;
        window.__rentHomeRealtimePollingInitialized = true;

        // Lưu trữ mốc thời gian lần poll gần nhất (khởi tạo đồng bộ từ máy chủ)
        let lastServerTime = '{{ now()->subSeconds(10)->toIso8601String() }}';
        let isPolling = false;
        let pollTimer = null;
        const POLL_INTERVAL = 4000; // Chu kỳ Short Polling: 4 giây / lần

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        /**
         * Hàm thực hiện một lượt Short Polling lên máy chủ
         */
        async function executeRealtimePoll() {
            // Không poll khi tab trình duyệt đang ẩn hoặc đang có request poll chưa hoàn tất
            if (document.hidden || isPolling) return;
            isPolling = true;

            try {
                const url = `/api/realtime/poll?since=${encodeURIComponent(lastServerTime)}&_t=${Date.now()}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (res.status === 401) {
                    // Phiên đăng nhập đã hết hạn -> dừng polling
                    stopPolling();
                    return;
                }

                if (!res.ok) return;

                const data = await res.json();
                if (!data || !data.success) return;

                // Cập nhật server_time mới nhất
                if (data.server_time) {
                    lastServerTime = data.server_time;
                }

                // 1. CẬP NHẬT & PREPEND THÔNG BÁO MỚI (Áp dụng cho mọi tài khoản & trang)
                handleNewNotifications(data.new_notifications, data.unread_notifications);

                // 2. CẬP NHẬT DỮ LIỆU ĐẶC THÙ THEO VAI TRÒ (Kiểm duyệt, CSKH, Admin)
                if (data.role_data) {
                    handleRoleSpecificData(data.role_data);
                }
            } catch (err) {
                // Lỗi mạng tạm thời -> bỏ qua và chờ lượt poll kế tiếp
            } finally {
                isPolling = false;
            }
        }

        /**
         * Xử lý Prepend Thông báo mới và cập nhật Badge
         */
        function handleNewNotifications(newNotifs, unreadCount) {
            // Cập nhật số đếm thông báo chưa đọc trên tất cả các loại Badge giao diện
            updateAllNotificationBadges(unreadCount);

            if (!Array.isArray(newNotifs) || newNotifs.length === 0) return;

            newNotifs.forEach(n => {
                // Hiển thị Toast thông báo tức thời góc phải màn hình
                if (typeof window.showToast === 'function') {
                    const shortMsg = (n.message || '').length > 60 ? (n.message.substring(0, 60) + '...') : n.message;
                    window.showToast(`🔔 ${n.title}: ${shortMsg}`, 'info');
                }

                // A. Trang Thông báo của ADMIN (#notifList)
                const adminList = document.getElementById('notifList');
                if (adminList) {
                    if (!document.querySelector(`#notifList [data-id="${n.id}"]`)) {
                        // Ẩn placeholder trống
                        const emptyPlaceholder = adminList.querySelector('.empty-placeholder, #emptyState');
                        if (emptyPlaceholder) emptyPlaceholder.style.display = 'none';

                        const notifDiv = document.createElement('div');
                        notifDiv.className = 'notif-item unread realtime-prepended';
                        notifDiv.setAttribute('data-id', n.id);
                        notifDiv.innerHTML = `
                            <div class="notif-left">
                                <div class="notif-avatar notif-avatar-info">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-header">
                                        <span class="notif-title">${escapeHtml(n.title)}</span>
                                        <span class="badge badge-info">${escapeHtml(n.category || 'Mới')}</span>
                                    </div>
                                    <p class="notif-desc">${escapeHtml(n.message)}</p>
                                    <div class="notif-meta">
                                        <span><i class="fa-regular fa-clock"></i> ${n.time_ago || 'Vừa xong'}</span>
                                        <span><i class="fa-solid fa-bolt" style="color: #10b981;"></i> Trực tuyến</span>
                                    </div>
                                </div>
                            </div>
                            <div class="notif-actions">
                                <button class="action-icon-btn" title="Đánh dấu đã đọc" onclick="markRead(this, '${n.id}')">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button class="action-icon-btn delete" title="Xóa thông báo" onclick="removeNotif(this, '${n.id}')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        `;
                        adminList.prepend(notifDiv);
                    }
                }

                // B. Trang Thông báo KIỂM DUYỆT VIÊN (#notificationList)
                const modNotifList = document.getElementById('notificationList');
                if (modNotifList) {
                    if (!document.querySelector(`#notificationList [data-id="${n.id}"]`)) {
                        const emptyState = modNotifList.querySelector('.empty-state');
                        if (emptyState) emptyState.style.display = 'none';

                        const modCard = document.createElement('div');
                        modCard.className = 'notif-card unread realtime-prepended';
                        modCard.setAttribute('data-id', n.id);
                        modCard.id = `notif-${n.id}`;
                        modCard.innerHTML = `
                            <div class="notif-icon-box info">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div class="notif-content">
                                <div class="notif-top">
                                    <div class="notif-title-row">
                                        <span class="notif-badge system">${escapeHtml(n.category || 'Hệ thống')}</span>
                                        <span class="notif-title">${escapeHtml(n.title)}</span>
                                        <span class="unread-dot"></span>
                                    </div>
                                    <div class="notif-time">
                                        <i class="fa-regular fa-clock"></i> ${n.time_ago || 'Vừa xong'}
                                    </div>
                                </div>
                                <p class="notif-message">${escapeHtml(n.message)}</p>
                                <div class="notif-meta-bar">
                                    <div class="notif-sender">
                                        <i class="fa-regular fa-user"></i> Hệ thống RentHome
                                    </div>
                                    <div class="notif-actions">
                                        ${n.action_url ? `<a href="${n.action_url}" class="btn-act btn-act-primary"><i class="fa-solid fa-arrow-up-right-from-square"></i> Xem ngay</a>` : ''}
                                        <button type="button" class="btn-act btn-act-light" onclick="deleteNotif('${n.id}')" title="Xóa thông báo">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        modNotifList.prepend(modCard);
                    }
                }

                // C. Trang Thông báo CHĂM SÓC KHÁCH HÀNG (#cskh-notif-container)
                const cskhList = document.getElementById('cskh-notif-container');
                if (cskhList) {
                    if (!document.getElementById(`cskh-notif-${n.id}`)) {
                        const emptyBox = cskhList.querySelector('.empty-state-box');
                        if (emptyBox) emptyBox.style.display = 'none';

                        const cskhCard = document.createElement('div');
                        cskhCard.id = `cskh-notif-${n.id}`;
                        cskhCard.className = 'notif-card bg-white rounded-2xl p-4 sm:p-5 border border-sky-300/80 bg-sky-50/20 ring-1 ring-sky-300/40 realtime-prepended';
                        cskhCard.innerHTML = `
                            <div class="flex items-start gap-3.5 sm:gap-4">
                                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-bell text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-slate-900 text-sm sm:text-base">${escapeHtml(n.title)}</h4>
                                        <span class="text-xs text-slate-500 whitespace-nowrap"><i class="fa-regular fa-clock"></i> ${n.time_ago || 'Vừa xong'}</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">${escapeHtml(n.message)}</p>
                                </div>
                            </div>
                        `;
                        cskhList.prepend(cskhCard);
                    }
                }

                // D. Dropdown Menu Thông báo ở Header trang chủ (#home-notif-list / #home-topbar-notif-list)
                const homeNotifList = document.getElementById('home-notif-list') || document.getElementById('home-topbar-notif-list');
                if (homeNotifList) {
                    if (!homeNotifList.querySelector(`[data-notif-id="${n.id}"]`)) {
                        const emptyHome = homeNotifList.querySelector('.empty-notif-msg');
                        if (emptyHome) emptyHome.style.display = 'none';

                        const homeItem = document.createElement('a');
                        homeItem.href = n.action_url || '#';
                        homeItem.setAttribute('data-notif-id', n.id);
                        homeItem.className = 'flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-colors realtime-prepended';
                        homeItem.innerHTML = `
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-bell text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">${escapeHtml(n.title)}</p>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">${escapeHtml(n.message)}</p>
                                <span class="text-[10px] text-slate-400 mt-1 block">${n.time_ago || 'Vừa xong'}</span>
                            </div>
                        `;
                        homeNotifList.prepend(homeItem);
                    }
                }
            });
        }

        /**
         * Xử lý Prepend dữ liệu đặc thù theo nghiệp vụ (Kiểm duyệt, CSKH...)
         */
        function handleRoleSpecificData(roleData) {
            // A. KIỂM DUYỆT VIÊN: BÀI ĐĂNG PHÒNG CHỜ DUYỆT
            // 1. Cập nhật số lượng bài chờ duyệt trên KPI và Sidebar
            if (typeof roleData.pending_posts_count === 'number') {
                const cardPending = document.getElementById('cardPendingCount');
                if (cardPending) {
                    cardPending.innerText = roleData.pending_posts_count;
                }
                const sidebarPostBadge = document.getElementById('sidebarPostBadge') || document.getElementById('sidebarPendingBadge');
                if (sidebarPostBadge) {
                    sidebarPostBadge.innerText = roleData.pending_posts_count;
                    sidebarPostBadge.style.display = roleData.pending_posts_count > 0 ? '' : 'none';
                }
            }

            // 2. Tự động ẩn các bài đăng đã được Kiểm duyệt viên khác phê duyệt hoặc từ chối
            if (roleData.processed_posts && Array.isArray(roleData.processed_posts) && roleData.processed_posts.length > 0) {
                roleData.processed_posts.forEach(proc => {
                    if (typeof window.updateRowStatusUI === 'function') {
                        window.updateRowStatusUI(proc.id, proc.status);
                    } else {
                        const row = document.querySelector(`tr.post-row[data-id="${proc.id}"]`);
                        if (row) {
                            row.style.transition = 'all 0.4s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(25px)';
                            setTimeout(() => {
                                row.style.display = 'none';
                                if (typeof window.filterModerationTable === 'function') {
                                    window.filterModerationTable();
                                }
                            }, 400);
                        }
                    }
                });
            }

            // 3. Prepend vào bảng Quản lý Kiểm duyệt khi có bài đăng mới (/kiem-duyet/qlkiem-duyet)
            if (roleData.new_posts && Array.isArray(roleData.new_posts) && roleData.new_posts.length > 0) {
                if (typeof window.prependModerationRow === 'function') {
                    roleData.new_posts.forEach(p => window.prependModerationRow(p));
                } else {
                    const table = document.getElementById('moderationTable') || document.querySelector('table.moderation-table') || document.querySelector('table');
                    const tableBody = table ? table.querySelector('tbody') : null;
                    if (tableBody && (window.location.pathname.includes('/qlkiem-duyet') || document.getElementById('moderationTable'))) {
                        roleData.new_posts.forEach(p => {
                            if (!document.querySelector(`tr.post-row[data-id="${p.id}"]`)) {
                                // Ẩn mọi hàng rỗng / Empty State nếu có
                                const empty1 = document.getElementById('emptyDatabaseRow');
                                if (empty1) empty1.style.display = 'none';
                                const empty2 = document.getElementById('dynamicEmptyRow');
                                if (empty2) empty2.style.display = 'none';
                                const empty3 = document.getElementById('empty-row-initial') || tableBody.querySelector('.empty-placeholder')?.closest('tr');
                                if (empty3) empty3.style.display = 'none';

                                // Lưu payload đầy đủ để mở modal xem chi tiết
                                if (!window.postDataMap) window.postDataMap = {};
                                const payload = p.modal_payload || p.raw_payload;
                                window.postDataMap[p.id] = payload;

                                const tr = document.createElement('tr');
                                tr.className = 'post-row realtime-prepended';
                                tr.setAttribute('data-id', p.id);
                                tr.setAttribute('data-status', 'pending');
                                tr.setAttribute('data-building', (p.building_name || '').toLowerCase());
                                tr.setAttribute('data-type', (p.building_type || '').toLowerCase());
                                tr.setAttribute('data-has-viewed', '0');
                                tr.setAttribute('data-locked-by', '');
                                tr.innerHTML = `
                                    <!-- Cột 1: Thông tin Phòng (Lớp Ruột) -->
                                    <td>
                                        <div class="post-info-cell">
                                            <div class="thumb-wrapper">
                                                <img src="${p.thumb_url || 'https://placehold.co/160x120?text=No+Image'}" alt="${escapeHtml(p.title)}" onerror="this.src='https://placehold.co/160x120?text=No+Image'">
                                                <span class="img-count-tag"><i class="fa-solid fa-camera"></i> ${p.images_count || 1}</span>
                                            </div>
                                            <div>
                                                <span class="post-title">${escapeHtml(p.title)}</span>
                                                <div class="post-specs">
                                                    <span><strong>Mã:</strong> ${escapeHtml(p.room_code || ('#' + p.id.slice(-5).toUpperCase()))}</span>
                                                    <span>•</span>
                                                    <span><i class="fa-solid fa-ruler-combined"></i> ${p.area || 0} m²</span>
                                                    ${p.floor_text ? `<span>•</span><span>${escapeHtml(p.floor_text)}</span>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cột 2: Tòa nhà mẹ (Lớp Vỏ) -->
                                    <td>
                                        <div class="building-box">
                                            <div class="building-name">
                                                <i class="fa-solid fa-building"></i>
                                                <span>${escapeHtml(p.building_name)}</span>
                                            </div>
                                            <div class="building-addr">${escapeHtml(p.building_address)}</div>
                                            <span class="type-badge">${escapeHtml(p.building_type || 'Chung cư mini / Căn hộ dịch vụ')}</span>
                                        </div>
                                    </td>

                                    <!-- Cột 3: Giá -->
                                    <td>
                                        <div class="price-val">${escapeHtml(p.price_formatted)}</div>
                                    </td>

                                    <!-- Cột 4: Nhân viên đăng -->
                                    <td>
                                        <div class="staff-box">
                                            <div class="staff-avatar">${escapeHtml(p.author_initial || 'N')}</div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 13px;">${escapeHtml(p.author_name)}</div>
                                                <div style="font-size: 11.5px; color: var(--text-muted, #64748b);">${escapeHtml(p.author_phone || 'Tài khoản con')}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cột 5: Thời gian gửi -->
                                    <td style="font-size: 12.5px; color: var(--text-muted, #64748b); white-space: nowrap;">
                                        ${escapeHtml(p.created_at_formatted)}
                                    </td>

                                    <!-- Cột 6: Trạng thái -->
                                    <td>
                                        <span class="status-badge status-pending" id="statusBadge_${p.id}">
                                            <i class="fa-solid fa-hourglass-half"></i>
                                            <span>Chờ phê duyệt</span>
                                        </span>
                                    </td>

                                    <!-- Cột 7: Thao tác kiểm duyệt -->
                                    <td>
                                        <div class="action-group" id="actionGroup_${p.id}">
                                            <button type="button" class="btn-act btn-act-view"
                                                onclick='openInspectModal(window.postDataMap["${p.id}"])'
                                                title="Xem chi tiết & Đối chiếu Lớp Vỏ - Lớp Ruột">
                                                <i class="fa-solid fa-eye"></i> <span>👁️ Xem chi tiết</span>
                                            </button>
                                        </div>
                                    </td>
                                `;
                                tableBody.prepend(tr);

                                if (typeof window.filterModerationTable === 'function') {
                                    window.filterModerationTable();
                                }

                                if (typeof window.showToast === 'function') {
                                    window.showToast(`🔔 Bài đăng mới chờ duyệt: "${p.title}"`, 'success');
                                }
                            }
                        });
                    }
                }
            }

            // B. CHĂM SÓC KHÁCH HÀNG: KHIẾU NẠI MỚI (#complaintGrid)
            if (roleData.new_complaints && Array.isArray(roleData.new_complaints) && roleData.new_complaints.length > 0) {
                // 1. Cập nhật các thẻ KPI & Badge số lượng đơn
                const kpiPending = document.getElementById('kpiPending');
                if (kpiPending && typeof roleData.pending_complaints_count === 'number') {
                    kpiPending.innerText = roleData.pending_complaints_count;
                }
                const sidebarComplaintBadge = document.getElementById('sidebarComplaintBadge');
                if (sidebarComplaintBadge && typeof roleData.pending_complaints_count === 'number') {
                    sidebarComplaintBadge.innerText = roleData.pending_complaints_count;
                    sidebarComplaintBadge.style.display = roleData.pending_complaints_count > 0 ? '' : 'none';
                }
                const kpiTotalComplaints = document.getElementById('kpiTotalComplaints');
                if (kpiTotalComplaints && typeof roleData.total_complaints_count === 'number') {
                    kpiTotalComplaints.innerText = roleData.total_complaints_count;
                }

                // 2. Prepend thẻ vào danh sách khiếu nại nếu đang ở trang /cskh/khieu-nai
                const complaintGrid = document.getElementById('complaintGrid');
                if (complaintGrid && (window.location.pathname.includes('/khieu-nai') || window.location.pathname.includes('/khieu_nai'))) {
                    roleData.new_complaints.forEach(c => {
                        if (!document.querySelector(`[data-id="${c.id}"]`)) {
                            // Ẩn placeholder rỗng nếu có
                            const emptyCol = complaintGrid.querySelector('.col-span-full');
                            if (emptyCol) emptyCol.style.display = 'none';

                            // Tạo thẻ khiếu nại mới
                            const card = document.createElement('div');
                            card.className = 'complaint-card bg-white rounded-xl shadow border border-slate-200 flex flex-col overflow-hidden transition-all duration-200 hover:shadow-md hover:border-slate-300 realtime-prepended';
                            card.setAttribute('data-id', c.id);
                            card.setAttribute('data-status', 'pending');

                            // Xây dựng hình ảnh
                            let imgHtml = '';
                            if (c.first_image) {
                                imgHtml = `
                                    <div class="relative h-48 w-full bg-slate-100 overflow-hidden cursor-pointer group"
                                         onclick="typeof zoomEvidenceImage === 'function' ? zoomEvidenceImage('${c.first_image}') : null"
                                         title="Nhấn để xem ảnh phóng to">
                                        <img src="${c.first_image}" alt="Bằng chứng khiếu nại" class="h-48 w-full object-cover rounded-t-xl group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/25 transition-colors"></div>
                                        ${c.more_count > 0 ? `
                                            <span class="absolute bottom-2.5 right-2.5 bg-slate-900/80 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-md">
                                                <i class="fa-regular fa-images text-[11px]"></i> +${c.more_count} ảnh
                                            </span>
                                        ` : ''}
                                        <span class="absolute top-2.5 left-2.5 bg-slate-900/70 backdrop-blur-xs text-white text-[11px] font-mono font-bold px-2 py-0.5 rounded shadow-xs">
                                            ${escapeHtml(c.code)}
                                        </span>
                                    </div>
                                `;
                            } else {
                                imgHtml = `
                                    <div class="h-48 w-full bg-slate-50 border-b border-slate-100 rounded-t-xl flex flex-col items-center justify-center text-slate-400 gap-2 relative">
                                        <span class="absolute top-2.5 left-2.5 bg-slate-200/80 text-slate-600 text-[11px] font-mono font-bold px-2 py-0.5 rounded">
                                            ${escapeHtml(c.code)}
                                        </span>
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-300">
                                            <i class="fa-regular fa-image text-2xl"></i>
                                        </div>
                                        <span class="text-xs font-medium text-slate-400">Không có hình ảnh đính kèm</span>
                                    </div>
                                `;
                            }

                            // Header post link
                            let postHtml = '';
                            if (c.post_title || c.building_name) {
                                postHtml = `
                                    <a href="${c.post_url || 'javascript:void(0)'}"
                                       ${c.post_url ? 'target="_blank"' : ''}
                                       class="font-bold text-blue-600 hover:text-blue-800 text-sm sm:text-base flex items-center gap-1.5 group line-clamp-1 transition-colors"
                                       title="${escapeHtml(c.post_title)}">
                                        <i class="fa-solid fa-house text-blue-500 text-xs shrink-0 group-hover:scale-110 transition-transform"></i>
                                        <span class="truncate">${escapeHtml(c.post_title)}</span>
                                        ${c.post_url ? '<i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></i>' : ''}
                                    </a>
                                    ${c.building_name ? `
                                        <div class="text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-building text-[10px] text-slate-400"></i>
                                            <span>${escapeHtml(c.building_name)}</span>
                                        </div>
                                    ` : ''}
                                `;
                            } else {
                                postHtml = `
                                    <div class="font-bold text-slate-700 text-sm flex items-center gap-1.5">
                                        <i class="fa-solid fa-shield text-slate-400 text-xs"></i>
                                        <span>Khiếu nại bài đăng hệ thống</span>
                                    </div>
                                `;
                            }

                            card.innerHTML = `
                                ${imgHtml}
                                <div class="p-4 pb-2">
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <span class="status-pill status-pending" id="badge_${c.id}">
                                            <i class="fa-solid fa-clock"></i> Chờ xử lý
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                                            <i class="fa-regular fa-clock text-[11px]"></i>
                                            ${c.created_at_formatted}
                                        </span>
                                    </div>
                                    <div>${postHtml}</div>
                                </div>
                                <div class="px-4 py-2 space-y-2.5">
                                    <div class="flex items-center justify-between text-xs text-slate-600 pt-1 border-t border-slate-100">
                                        <div class="flex items-center gap-1.5 font-bold text-slate-800 truncate" title="${escapeHtml(c.sender_name)}">
                                            <i class="fa-regular fa-user text-slate-400 text-xs shrink-0"></i>
                                            <span class="truncate">${escapeHtml(c.sender_name)}</span>
                                        </div>
                                        <div class="text-slate-500 font-medium shrink-0 flex items-center gap-1">
                                            <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                                            <span>${escapeHtml(c.sender_phone)}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="reason-tag" style="margin-bottom: 0;">
                                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 10px; margin-right: 3px;"></i>
                                            ${escapeHtml(c.reason)}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-lg text-sm text-gray-700 line-clamp-3 leading-relaxed border border-slate-100" title="${escapeHtml(c.content)}">
                                        ${escapeHtml(c.content)}
                                    </div>
                                </div>
                                <div class="mt-auto border-t border-slate-100 p-4 pt-3 bg-slate-50/50">
                                    <button type="button"
                                            id="btn-claim-${c.id}"
                                            onclick="typeof claimTicket === 'function' ? claimTicket('${c.id}') : null"
                                            class="w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-all active:scale-[0.98] bg-blue-600 hover:bg-blue-700 text-white cursor-pointer">
                                        <i class="fa-solid fa-hand-pointer"></i>
                                        <span>Tiếp Nhận Xử Lý</span>
                                    </button>
                                </div>
                            `;

                            complaintGrid.prepend(card);

                            if (typeof window.showToast === 'function') {
                                window.showToast(`🔔 Khiếu nại mới từ khách thuê: "${c.code} - ${c.reason}"`, 'warning');
                            }
                        }
                    });
                }
            }
        }

        /**
         * Cập nhật đồng bộ các huy hiệu (Badge) số đếm trên Header/Sidebar toàn dự án
         */
        function updateAllNotificationBadges(count) {
            if (typeof count !== 'number') return;

            // 1. Sidebar Kiểm duyệt & CSKH
            const sidebarBadge = document.getElementById('sidebarNotifBadge');
            if (sidebarBadge) {
                sidebarBadge.innerText = count;
                sidebarBadge.style.display = count > 0 ? '' : 'none';
            }

            // 2. Header CSKH & Admin
            const headerBadge = document.getElementById('headerNotifBadge');
            if (headerBadge) {
                headerBadge.innerText = count > 0 ? `${count} mới` : '0';
                headerBadge.style.display = count > 0 ? '' : 'none';
            }

            // 3. Header Trang chủ / Cá nhân
            const homeBadge = document.getElementById('home-notif-badge');
            if (homeBadge) {
                homeBadge.innerText = count;
                homeBadge.style.display = count > 0 ? '' : 'none';
            }
            const homeTopbarBadge = document.getElementById('home-topbar-notif-badge');
            if (homeTopbarBadge) {
                homeTopbarBadge.innerText = count;
                homeTopbarBadge.style.display = count > 0 ? '' : 'none';
            }
        }

        /**
         * Bắt đầu vòng lặp Short Polling
         */
        function startPolling() {
            if (pollTimer) clearInterval(pollTimer);
            // Kích hoạt ngay 1 lần sau 1.2s khởi tạo trang
            setTimeout(executeRealtimePoll, 1200);
            pollTimer = setInterval(executeRealtimePoll, POLL_INTERVAL);
        }

        function stopPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        // Tự động tối ưu hiệu năng: Tạm dừng khi ẩn tab, tiếp tục ngay lập tức khi người dùng quay lại tab
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                executeRealtimePoll();
                startPolling();
            } else {
                stopPolling();
            }
        });

        window.addEventListener('focus', () => {
            executeRealtimePoll();
        });

        // Khởi động khi tải xong DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startPolling);
        } else {
            startPolling();
        }

        // Xuất hàm ra global để có thể gọi thủ công nếu cần
        window.triggerRealtimePoll = executeRealtimePoll;
    })();
</script>
@endonce

