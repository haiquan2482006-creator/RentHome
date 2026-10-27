<!-- ==========================================
     COMPONENT: MODAL KHÓA / MỞ KHÓA TÀI KHOẢN DÙNG CHUNG
     Dùng chung cho Quản lý Khách thuê & Quản lý Nhân viên
     ========================================== -->
@once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce
<style>
    /* Modal Lock User Styles */
    .modal-lock-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .modal-lock-backdrop.active,
    .modal-lock-backdrop.show {
        opacity: 1;
    }
    .modal-lock-dialog {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        transform: translateY(16px) scale(0.97);
        transition: transform 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .modal-lock-backdrop.active .modal-lock-dialog,
    .modal-lock-backdrop.show .modal-lock-dialog {
        transform: translateY(0) scale(1);
    }
    .modal-lock-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .modal-lock-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e293b;
    }
    .modal-lock-header-title i {
        color: #d97706;
        font-size: 1.2rem;
    }
    .modal-lock-close {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.15s;
    }
    .modal-lock-close:hover {
        color: #ef4444;
        background: #fee2e2;
    }
    .modal-lock-body {
        padding: 20px;
        background: #ffffff;
    }
    .modal-lock-alert {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        border-left: 4px solid #f59e0b;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 18px;
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.5;
    }
    .modal-lock-alert strong {
        color: #0f172a;
    }
    .modal-lock-alert .warning-sub {
        display: block;
        margin-top: 4px;
        color: #dc2626;
        font-weight: 700;
    }
    .form-group-lock {
        margin-bottom: 16px;
    }
    .form-group-lock label {
        display: block;
        font-size: 0.825rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-group-lock label span.req {
        color: #dc2626;
    }
    .input-lock, .select-lock {
        width: 100%;
        height: 42px;
        padding: 0 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #1e293b;
        background-color: #ffffff;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .input-lock:focus, .select-lock:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
    }
    .modal-lock-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 20px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }
    .btn-lock-cancel {
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-lock-cancel:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .btn-lock-confirm {
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 700;
        color: #ffffff;
        background: #d97706;
        border: 1px solid #b45309;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }
    .btn-lock-confirm:hover {
        background: #b45309;
    }
    .btn-lock-confirm:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<!-- MODAL HTML: Khóa tài khoản này? -->
<div id="lockUserModal" class="modal-lock-backdrop" onclick="if(event.target === this) closeLockUserModal()">
    <div class="modal-lock-dialog">
        <div class="modal-lock-header">
            <div class="modal-lock-header-title">
                <i class="fa-solid fa-lock"></i>
                <span>Khóa tài khoản này?</span>
            </div>
            <button type="button" class="modal-lock-close" onclick="closeLockUserModal()" title="Đóng">&times;</button>
        </div>

        <form id="lockUserModalForm" method="POST" action="" onsubmit="submitLockUserForm(event)">
            @csrf
            <div class="modal-lock-body">
                <div class="modal-lock-alert">
                    Bạn có chắc chắn muốn khóa tài khoản <strong id="lockModalUserName">Người dùng</strong>?
                    <span class="warning-sub">Người dùng sẽ không thể đăng nhập vào hệ thống.</span>
                </div>

                <div class="form-group-lock">
                    <label for="lockModalReason">
                        Lý do khóa tài khoản: <span class="req">*</span>
                    </label>
                    <input type="text" 
                           id="lockModalReason" 
                           name="lock_reason" 
                           class="input-lock" 
                           placeholder="Nhập lý do (ví dụ: Vi phạm quy định làm việc, spam, tiêu cực...)" 
                           required>
                </div>

                <div class="form-group-lock">
                    <label for="lockModalDuration">
                        Thời gian khóa: <span class="req">*</span>
                    </label>
                    <select id="lockModalDuration" name="lock_duration" class="select-lock" required>
                        <option value="1">Khóa 1 ngày</option>
                        <option value="3">Khóa 3 ngày</option>
                        <option value="7" selected>Khóa 7 ngày</option>
                        <option value="30">Khóa 30 ngày (1 tháng)</option>
                        <option value="indefinite">Khóa đến khi mở lại (Thủ công)</option>
                    </select>
                </div>
            </div>

            <div class="modal-lock-footer">
                <button type="button" class="btn-lock-cancel" onclick="closeLockUserModal()">Hủy bỏ</button>
                <button type="submit" id="btnConfirmLockUserSubmit" class="btn-lock-confirm">
                    <i class="fa-solid fa-lock"></i> Xác nhận khóa tài khoản
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentLockUserId = null;

    /**
     * Mở popup / Modal Khóa hoặc Mở khóa tài khoản
     * 
     * @param {string} userId - ID của tài khoản
     * @param {string} userName - Tên hiển thị của tài khoản
     * @param {boolean} isCurrentlyLocked - Trạng thái khóa hiện tại
     */
    function openLockUserModal(userId, userName, isCurrentlyLocked) {
        if (isCurrentlyLocked) {
            // 1. Trường hợp MỞ KHÓA TÀI KHOẢN
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Mở khóa tài khoản?',
                    html: `Bạn có chắc chắn muốn mở khóa cho tài khoản <strong>"${userName}"</strong>?<br>Người dùng sẽ có thể đăng nhập và sử dụng dịch vụ trở lại.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-lock-open"></i> Đồng ý mở khóa',
                    cancelButtonText: 'Hủy thao tác',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeUnlockUser(userId);
                    }
                });
            } else {
                if (confirm(`Bạn có chắc chắn muốn mở khóa cho tài khoản "${userName}"?`)) {
                    executeUnlockUser(userId);
                }
            }
            return;
        }

        // 2. Trường hợp KHÓA TÀI KHOẢN: Hiển thị Modal HTML với đầy đủ lý do & thời hạn
        currentLockUserId = userId;
        const modal = document.getElementById('lockUserModal');
        const form = document.getElementById('lockUserModalForm');
        const nameSpan = document.getElementById('lockModalUserName');
        const reasonInput = document.getElementById('lockModalReason');
        const durationSelect = document.getElementById('lockModalDuration');

        if (form) {
            // Form action trỏ đúng route /admin/users/{id}/lock
            form.action = `/admin/users/${userId}/lock`;
        }
        if (nameSpan) {
            nameSpan.textContent = userName || 'Nhân viên';
        }
        if (reasonInput) {
            reasonInput.value = '';
        }
        if (durationSelect) {
            durationSelect.value = '7';
        }

        if (modal) {
            modal.style.display = 'flex';
            // Reflow để animation mượt mà
            void modal.offsetWidth;
            modal.classList.add('active');
            if (reasonInput) {
                setTimeout(() => reasonInput.focus(), 100);
            }
        }
    }

    /**
     * Đóng Modal Khóa tài khoản
     */
    function closeLockUserModal() {
        const modal = document.getElementById('lockUserModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }
        currentLockUserId = null;
    }

    /**
     * Mở khóa tài khoản qua AJAX
     */
    async function executeUnlockUser(userId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng chờ trong giây lát...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        try {
            const res = await fetch(`/admin/users/${userId}/lock`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            });

            const data = await res.json();
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message || 'Đã mở khóa tài khoản thành công!',
                        timer: 1800,
                        showConfirmButton: false
                    });
                } else if (typeof showToast === 'function') {
                    showToast(data.message || 'Đã mở khóa tài khoản thành công!', 'success');
                }
                setTimeout(() => location.reload(), 800);
            } else {
                const err = data.message || 'Có lỗi xảy ra khi mở khóa tài khoản!';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Thất bại', text: err });
                } else {
                    alert(err);
                }
            }
        } catch (e) {
            console.error('Lỗi khi mở khóa:', e);
            alert('Lỗi kết nối máy chủ!');
        }
    }

    /**
     * Submit Form Khóa tài khoản (Gửi AJAX đến route /admin/users/{id}/lock)
     */
    async function submitLockUserForm(event) {
        event.preventDefault();
        const form = event.target;
        const reasonInput = document.getElementById('lockModalReason');
        const durationSelect = document.getElementById('lockModalDuration');
        const submitBtn = document.getElementById('btnConfirmLockUserSubmit');

        const reason = reasonInput ? reasonInput.value.trim() : '';
        const duration = durationSelect ? durationSelect.value : '7';

        if (!reason) {
            alert('Vui lòng nhập lý do khóa tài khoản.');
            if (reasonInput) reasonInput.focus();
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    lock_reason: reason,
                    lock_duration: duration
                })
            });

            const data = await res.json();
            if (data.success) {
                closeLockUserModal();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message || 'Đã khóa tài khoản thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (typeof showToast === 'function') {
                    showToast(data.message || 'Đã khóa tài khoản thành công!', 'success');
                }
                setTimeout(() => location.reload(), 800);
            } else {
                const err = data.message || 'Không thể khóa tài khoản!';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Thao tác thất bại', text: err });
                } else {
                    alert(err);
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Xác nhận khóa tài khoản';
                }
            }
        } catch (err) {
            console.error('Lỗi khi khóa tài khoản:', err);
            alert('Lỗi kết nối máy chủ khi khóa tài khoản!');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Xác nhận khóa tài khoản';
            }
        }
    }

    // Đóng modal khi nhấn Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLockUserModal();
        }
    });

    // Global Aliases cho các hàm
    window.openLockModal = openLockUserModal;
    window.openLockUserModal = openLockUserModal;
    window.confirmToggleLockUser = openLockUserModal;
    window.confirmToggleLock = openLockUserModal;
    window.closeLockUserModal = closeLockUserModal;
    window.submitLockUserForm = submitLockUserForm;
</script>
