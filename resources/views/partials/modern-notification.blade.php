<!-- ================= BỘ THÔNG BÁO HIỆN ĐẠI & GỌN GÀNG (RENTHOME NOTIFICATION) ================= -->
@once
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style id="renthome-modern-notification-styles">
    /* Tùy chỉnh cửa sổ xác nhận / thông báo (Modal) hiện đại, nhỏ gọn */
    .swal2-container {
        z-index: 9999999 !important;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .swal2-popup:not(.swal2-toast) {
        border-radius: 1.25rem !important;
        padding: 1.35rem 1.5rem !important;
        max-width: 420px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid #f1f5f9 !important;
    }
    .swal2-popup:not(.swal2-toast) .swal2-title {
        font-size: 1.125rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        padding: 0 !important;
        margin: 0.75rem 0 0.35rem 0 !important;
        line-height: 1.3 !important;
    }
    .swal2-popup:not(.swal2-toast) .swal2-html-container {
        font-size: 0.875rem !important;
        color: #475569 !important;
        line-height: 1.5 !important;
        margin: 0.35rem 0 0 0 !important;
        padding: 0 !important;
    }
    .swal2-actions {
        gap: 0.625rem !important;
        margin-top: 1.25rem !important;
        width: 100% !important;
        justify-content: center !important;
    }
    .swal2-confirm, .swal2-cancel {
        border-radius: 0.75rem !important;
        font-weight: 700 !important;
        font-size: 0.84rem !important;
        padding: 0.6rem 1.15rem !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }
    .swal2-confirm:hover, .swal2-cancel:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }
    .swal2-popup:not(.swal2-toast) .swal2-icon {
        width: 3.5rem !important;
        height: 3.5rem !important;
        margin: 0.25rem auto 0.25rem auto !important;
        border-width: 3px !important;
    }

    /* Tùy chỉnh Toast nổi góc phải cực kỳ gọn gàng, tinh tế */
    .swal2-popup.swal2-toast {
        border-radius: 0.75rem !important;
        padding: 0.5rem 0.85rem !important;
        max-width: 340px !important;
        width: auto !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.08) !important;
        border: 1px solid #e2e8f0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        background: #ffffff !important;
    }
    .swal2-popup.swal2-toast .swal2-title {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        margin: 0 !important;
        line-height: 1.35 !important;
        text-align: left !important;
    }
    .swal2-popup.swal2-toast .swal2-icon {
        width: 1.25rem !important;
        height: 1.25rem !important;
        min-width: 1.25rem !important;
        margin: 0 !important;
    }
    .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content {
        font-size: 0.75rem !important;
    }
</style>

<script>
    (function() {
        if (window.__rentHomeNotificationInitialized) return;
        window.__rentHomeNotificationInitialized = true;

        function escapeNotificationHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Tạo Toast mặc định nhỏ gọn
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        window.RentHomeToast = Toast;
        window.Toast = Toast;

        // Hàm hiển thị Toast thông báo nhanh
        window.showToast = function(message, type = 'success') {
            if (!message) return;
            const validTypes = ['success', 'error', 'warning', 'info', 'question'];
            const icon = validTypes.includes(type) ? type : 'info';
            Toast.fire({
                icon: icon,
                title: String(message)
            });
        };

        // Hàm hiển thị hộp thoại xác nhận hiện đại (trả về Promise<boolean>)
        window.showConfirmDialog = async function(options = {}) {
            const {
                title = 'Xác nhận hành động?',
                text = '',
                html = '',
                icon = 'question',
                confirmText = 'Đồng ý',
                cancelText = 'Hủy bỏ',
                confirmColor = '#10b981',
                cancelColor = '#64748b',
                danger = false
            } = options;

            const finalConfirmColor = danger ? '#ef4444' : confirmColor;

            const result = await Swal.fire({
                title: title,
                text: html ? undefined : text,
                html: html || undefined,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: finalConfirmColor,
                cancelButtonColor: cancelColor,
                confirmButtonText: `<i class="fa-solid fa-check mr-1.5"></i> ${confirmText}`,
                cancelButtonText: `<i class="fa-solid fa-xmark mr-1.5"></i> ${cancelText}`,
                reverseButtons: true,
                focusCancel: danger
            });

            return result.isConfirmed;
        };

        // Hàm chặn submit form và hỏi xác nhận hiện đại
        window.confirmForm = function(form, message, title = 'Xác nhận thực hiện') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Xác nhận',
                cancelButtonText: '<i class="fa-solid fa-xmark mr-1.5"></i> Hủy',
                reverseButtons: true
            }).then((res) => {
                if (res.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        };

        // Ghi đè window.alert mặc định của trình duyệt ("localhost:8000 cho biết")
        // Tự động chuyển đổi thành Toast hoặc Modal nhỏ gọn hiện đại mà không chặn luồng JS
        window.alert = function(msg) {
            if (msg === undefined || msg === null) return;
            const str = String(msg).trim();
            if (!str) return;

            const lower = str.toLowerCase();
            let icon = 'info';

            if (
                lower.includes('thành công') || 
                lower.includes('success') || 
                lower.includes('🎉') || 
                lower.includes('cảm ơn') || 
                lower.includes('đã gửi') || 
                lower.includes('đã xóa') || 
                lower.includes('đã hủy') || 
                lower.includes('đã lưu') || 
                lower.includes('đã sao chép') || 
                lower.includes('đã duyệt') ||
                lower.includes('hoàn tất')
            ) {
                icon = 'success';
            } else if (
                lower.includes('lỗi') || 
                lower.includes('error') || 
                lower.includes('thất bại') || 
                lower.includes('không thể') || 
                lower.includes('không tìm thấy') ||
                lower.includes('hành động này không thể')
            ) {
                icon = 'error';
            } else if (
                lower.includes('vui lòng') || 
                lower.includes('cảnh báo') || 
                lower.includes('warning') || 
                lower.includes('chưa') || 
                lower.includes('không được phép') ||
                lower.includes('tối thiểu')
            ) {
                icon = 'warning';
            }

            // Nếu thông báo xuống dòng nhiều dòng (như tin nhắn Zalo, biên bản quyết toán) hoặc quá dài: hiển thị Modal gọn gàng
            if (str.includes('\n') || str.length > 110) {
                Swal.fire({
                    title: 'Thông báo',
                    html: `<div style="text-align: left; font-size: 13.5px; line-height: 1.5; color: #334155; white-space: pre-line; word-break: break-word; background: #f8fafc; padding: 12px 14px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 8px;">${escapeNotificationHtml(str)}</div>`,
                    icon: icon,
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Đã hiểu'
                });
            } else {
                // Hiển thị Toast nổi góc trên nhỏ gọn
                Toast.fire({
                    icon: icon,
                    title: str
                });
            }
        };
    })();
</script>
@endonce

