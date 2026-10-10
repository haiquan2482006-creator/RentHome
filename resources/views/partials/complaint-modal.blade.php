<!-- Modal Khiếu Nại / Báo Cáo Bài Đăng Đến Ban Kiểm Duyệt -->
<div id="complaintModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 overflow-y-auto" style="display: none;">
    <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 transition-all animate-fade-in">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-200 shadow-sm">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 leading-tight">Khiếu Nại Bài Đăng</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Gửi thông tin đến ban Kiểm Duyệt Viên để xem xét xử lý</p>
                </div>
            </div>
            <button type="button" onclick="closeComplaintModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Info Notice -->
        <div class="mt-4 p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-2xl text-xs text-amber-900 leading-relaxed flex items-start gap-2.5">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-600 shrink-0"></i>
            <span>Nếu phát hiện kiểm duyệt có sai sót, thông tin bài đăng sai lệch hoặc có dấu hiệu gian lận, bạn hãy gửi yêu cầu để Ban Kiểm Duyệt RentHome xử lý và phản hồi lại sớm nhất.</span>
        </div>

        <!-- Form -->
        <form id="complaintForm" onsubmit="submitComplaint(event)" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->_id ?? $post->id }}">
            <input type="hidden" name="type" value="appeal">

            <!-- Thông tin bài đăng vắn tắt -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <p class="font-bold text-slate-700 truncate"><span class="text-slate-400 font-normal">Bài đăng:</span> {{ $post->title }}</p>
                <p class="text-slate-400 mt-0.5">Mã tin: <span class="font-mono font-semibold text-slate-600">#{{ substr($post->_id ?? $post->id, -8) }}</span></p>
            </div>

            <!-- Loại khiếu nại -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Lý do / Phân loại khiếu nại <span class="text-rose-500">*</span>
                </label>
                <select name="reason_type" id="complaintReasonType" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all">
                    <option value="Kiểm duyệt có sai sót">Kiểm duyệt có sai sót / Yêu cầu duyệt lại bài</option>
                    <option value="Sai lệch giá / Diện tích / Địa chỉ">Sai lệch giá / Diện tích / Địa chỉ thực tế</option>
                    <option value="Bài đăng đã cho thuê">Phòng đã cho thuê / Không liên hệ được chủ</option>
                    <option value="Nghi ngờ giả mạo hoặc lừa đảo">Nghi ngờ thông tin giả mạo / Lừa đảo tiền cọc</option>
                    <option value="Hình ảnh không đúng thực tế">Hình ảnh sao chép / Không đúng phòng thực tế</option>
                    <option value="Khác">Lý do khác</option>
                </select>
            </div>

            @guest
            <!-- Thông tin liên hệ nếu chưa đăng nhập -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Họ tên của bạn <span class="text-rose-500">*</span></label>
                    <input type="text" name="sender_name" placeholder="Nguyễn Văn A" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-amber-500 focus:ring-1 focus:ring-amber-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
                    <input type="tel" name="sender_phone" placeholder="09xxxxxxxx" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-amber-500 focus:ring-1 focus:ring-amber-200 outline-none">
                </div>
            </div>
            @endguest

            <!-- Chi tiết nội dung khiếu nại -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Chi tiết nội dung khiếu nại <span class="text-rose-500">*</span>
                </label>
                <textarea name="content" id="complaintContent" rows="3" required placeholder="Mô tả cụ thể sai sót của khâu kiểm duyệt hoặc thông tin sai lệch cần được giải quyết..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all resize-none"></textarea>
            </div>

            <!-- Đính kèm hình ảnh bằng chứng -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Hình ảnh minh chứng <span class="text-slate-400 font-normal lowercase">(tùy chọn)</span>
                </label>
                <div class="relative flex items-center">
                    <input type="file" name="image" id="complaintImageInput" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>
            </div>

            <!-- Alert trạng thái -->
            <div id="complaintAlert" class="hidden p-3 rounded-xl text-xs font-bold"></div>

            <!-- Footer Buttons -->
            <div class="pt-2 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeComplaintModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Hủy bỏ
                </button>
                <button type="submit" id="btnSubmitComplaint" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-md shadow-amber-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Gửi đến Kiểm duyệt viên</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openComplaintModal() {
        const modal = document.getElementById('complaintModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            const alertBox = document.getElementById('complaintAlert');
            if (alertBox) {
                alertBox.classList.add('hidden');
                alertBox.innerHTML = '';
            }
        }
    }

    function closeComplaintModal() {
        const modal = document.getElementById('complaintModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
    }

    // Đóng modal khi click ra ngoài overlay
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('complaintModal');
        if (modal && e.target === modal) {
            closeComplaintModal();
        }
    });

    async function submitComplaint(e) {
        e.preventDefault();
        const form = document.getElementById('complaintForm');
        const btn = document.getElementById('btnSubmitComplaint');
        const alertBox = document.getElementById('complaintAlert');
        const originalBtnHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';

        try {
            const formData = new FormData(form);
            const response = await fetch("{{ route('complaints.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alertBox.className = 'p-3.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-2';
                alertBox.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 text-base"></i> ' + (data.message || 'Gửi khiếu nại thành công!');
                alertBox.classList.remove('hidden');
                form.reset();

                setTimeout(() => {
                    closeComplaintModal();
                    alertBox.classList.add('hidden');
                }, 2500);
            } else {
                alertBox.className = 'p-3.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 flex items-center gap-2';
                alertBox.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i> ' + (data.error || data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                alertBox.classList.remove('hidden');
            }
        } catch (err) {
            alertBox.className = 'p-3.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 flex items-center gap-2';
            alertBox.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i> Không thể kết nối đến máy chủ. Vui lòng thử lại sau.';
            alertBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    }
</script>
