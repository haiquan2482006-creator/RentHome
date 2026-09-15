<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tạo Hợp Đồng Cho Thuê Mới - RentHome</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        skybrand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0b132a',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(34, 197, 94, 0.4)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.05)',
                        'sky-glow': '0 0 25px -5px rgba(2, 132, 199, 0.3)',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">

    <!-- TOP HEADER BAR -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Logo & Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                    <span class="text-[10px] font-bold text-skybrand-600 uppercase tracking-widest mt-0.5">Quản Lý Vận Hành Chủ Nhà</span>
                </div>
            </a>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ url('qlvan_hanh/Room_list') }}" 
                   class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center gap-2 border border-slate-200 transition-all">
                    <i class="fa-solid fa-arrow-left text-slate-500"></i>
                    <span>Danh Sách Phòng</span>
                </a>

                <a href="{{ url('/') }}" 
                   class="py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md transition-all">
                    <i class="fa-solid fa-house"></i>
                    <span>Trở Về Trang Chủ</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- BANNER HERO BANNER (ĐỒNG MÀU DARK NAVY NÉT NHƯ CÁC TRANG QUẢN LÝ VẬN HÀNH) -->
        <div class="bg-[#0b132a] rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-file-signature"></i> Tạo Hợp Đồng Cho Thuê Mới
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Tạo Hợp Đồng & Cấu Hình Phòng Cho Thuê</h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Điền đầy đủ thông tin bên thuê, giá cọc, cấu hình đơn giá điện nước và thiết lập chu kỳ chốt hóa đơn định kỳ cho hệ thống.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ url('qlvan_hanh/Room_list') }}" 
                       class="py-3 px-5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm flex items-center gap-2 backdrop-blur-md transition-all border border-white/20">
                        <i class="fa-solid fa-xmark"></i> Hủy Bỏ
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM TRANG TẠO HỢP ĐỒNG ĐẦY ĐỦ 15 CẤU HÌNH -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-slate-200 space-y-6">
            <form id="fullCreateContractForm" onsubmit="handleSaveForm(event)" class="space-y-6 text-xs sm:text-sm">
                
                <!-- PHẦN 1: THÔNG TIN BÊN THUÊ & PHÒNG THUÊ -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-user-tag text-emerald-600 text-base"></i> 1. Thông Tin Khách Thuê & Phòng Cho Thuê
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Người thuê -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Bên thuê (Người thuê) <span class="text-rose-500">*</span></label>
                            <select id="fTenant" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                                <option value="">-- Chọn từ danh sách khách đã gửi form liên hệ --</option>
                                <option value="Nguyễn Văn A (SĐT: 0912 345 678)" selected>Nguyễn Văn A (SĐT: 0912 345 678)</option>
                                <option value="Trần Thị B (SĐT: 0908 123 456)">Trần Thị B (SĐT: 0908 123 456)</option>
                                <option value="Lê Hoàng C (SĐT: 0938 111 222)">Lê Hoàng C (SĐT: 0938 111 222)</option>
                                <option value="Phạm Văn D (SĐT: 0977 888 999)">Phạm Văn D (SĐT: 0977 888 999)</option>
                            </select>
                            <span class="text-[11px] text-slate-500 mt-1 block">* Chọn từ danh sách user đã gửi form liên hệ tư vấn</span>
                        </div>

                        <!-- 2. Phòng thuê -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Phòng cho thuê <span class="text-rose-500">*</span></label>
                            <select id="fRoom" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                                <option value="NH-201" selected>Phòng 201 – Nguyên Hồng (NH-201)</option>
                                <option value="NH-302">Phòng 302 – Nguyên Hồng (NH-302)</option>
                                <option value="VH-1208">Căn 12.08 – Vinhomes Central Park (VH-1208)</option>
                                <option value="MT-1504">Căn T3-1504 – Masteri Thảo Điền (MT-1504)</option>
                            </select>
                            <span class="text-[11px] text-emerald-700 font-medium mt-1 block">* Hệ thống tự động chuyển phòng này sang trạng thái "Đã cho thuê"</span>
                        </div>

                        <!-- 3. Tiền phòng -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Tiền phòng hàng tháng (VNĐ/tháng) <span class="text-rose-500">*</span></label>
                            <input type="text" id="fRentPrice" value="3.000.000 VNĐ/tháng" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                        </div>

                        <!-- 4. Tiền cọc -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Tiền cọc giữ phòng (VNĐ) <span class="text-rose-500">*</span></label>
                            <input type="text" id="fDeposit" value="1.000.000 VNĐ" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-emerald-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                        </div>
                    </div>
                </div>

                <!-- PHẦN 2: CẤU HÌNH ĐIỆN & NƯỚC (NƠI LƯU CẤU HÌNH ĐỂ THÁNG SAU TỰ TÍNH TIỀN) -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-bolt-lightning text-amber-500 text-base"></i> 2. Cấu Hình Đơn Giá Điện & Nước Thỏa Thuận
                    </h3>

                    <!-- CẤU HÌNH ĐIỆN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Giá Điện (VNĐ/số) <span class="text-rose-500">*</span></label>
                            <input type="text" id="fElecPrice" value="3.500 VNĐ/số" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Lưu cấu hình để tháng sau hệ thống tự động nhân tiền điện</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Số điện đầu ban đầu <span class="text-rose-500">*</span></label>
                            <input type="number" id="fElecStart" value="2000" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                        </div>
                    </div>

                    <!-- CẤU HÌNH NƯỚC (NÚT CHỌN TOGGLE: TÍNH KHỐI / TÍNH NGƯỜI) -->
                    <div class="pt-3 space-y-3 border-t border-slate-200">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="font-extrabold text-slate-900 text-xs sm:text-sm flex items-center gap-2">
                                <i class="fa-solid fa-droplet text-skybrand-600"></i> Giá Nước Thỏa Thuận (Chọn hình thức tính):
                            </label>
                            <div class="inline-flex rounded-xl bg-slate-200 p-1 font-bold text-xs shadow-inner">
                                <button type="button" id="btnModeKhoi" onclick="switchWaterForm('khoi')" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition-all">
                                    <i class="fa-solid fa-cube"></i> Tính Theo Khối
                                </button>
                                <button type="button" id="btnModeNguoi" onclick="switchWaterForm('nguoi')" class="px-4 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 transition-all">
                                    <i class="fa-solid fa-users"></i> Tính Theo Người
                                </button>
                            </div>
                        </div>

                        <!-- SUB-FORM 1: TÍNH THEO KHỐI -->
                        <div id="subWaterKhoi" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-2xl border border-slate-200">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số tiền (VNĐ/khối)</label>
                                <input type="text" id="fWaterPriceKhoi" value="25.000 VNĐ/khối" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số nước ban đầu</label>
                                <input type="number" id="fWaterStart" value="10" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white">
                            </div>
                        </div>

                        <!-- SUB-FORM 2: TÍNH THEO NGƯỜI -->
                        <div id="subWaterNguoi" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-2xl border border-slate-200">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số tiền (VNĐ/người)</label>
                                <input type="text" id="fWaterPriceNguoi" value="100.000 VNĐ/người" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số người ở đăng ký</label>
                                <input type="number" id="fWaterPeopleCount" value="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHẦN 3: PHÍ DỊCH VỤ CỐ ĐỊNH -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-wifi text-purple-600 text-base"></i> 3. Phí Dịch Vụ Cố Định Hàng Tháng
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Internet / Wifi (VNĐ/tháng)</label>
                            <input type="text" id="fInternetPrice" value="10.000 VNĐ" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Rác sinh hoạt (VNĐ/phòng)</label>
                            <input type="text" id="fGarbagePrice" value="10.000 VNĐ / phòng" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                        </div>
                    </div>
                </div>

                <!-- PHẦN 4: CHU KỲ, HẠN CHỐT & PHÍ PHẠT QUÁ HẠN -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-calendar-check text-skybrand-600 text-base"></i> 4. Chu Kỳ Thanh Toán, Ngày Chốt Hóa Đơn & Phí Phạt
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Chu kỳ thanh toán</label>
                            <select id="fCycle" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                                <option value="1 tháng" selected>1 tháng (Hàng tháng)</option>
                                <option value="3 tháng">3 tháng (Theo quý)</option>
                                <option value="6 tháng">6 tháng (Nửa năm)</option>
                                <option value="1 năm">1 năm (Theo năm)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Ngày chốt Hóa đơn</label>
                            <input type="text" id="fBillDate" value="VD: Mùng 5" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Đến ngày này hệ thống nhắc Chủ nhà ghi điện nước</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Thời hạn thanh toán</label>
                            <input type="text" id="fPayDays" value="5 ngày" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Ngày chốt + 5 ngày = Hạn chót đóng tiền</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Phí phạt quá hạn</label>
                            <input type="text" id="fPenalty" value="20.000 VNĐ / ngày" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-rose-600 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Tự lấy số ngày trễ × con số này để cộng vào hóa đơn</span>
                        </div>
                    </div>
                </div>

                <!-- PHẦN 5: THỜI HẠN HỢP ĐỒNG & TỰ ĐỘNG TÍNH NGÀY KẾT THÚC -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-clock text-rose-500 text-base"></i> 5. Thời Hạn Hợp Đồng & Tự Động Tính Ngày Kết Thúc
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Ngày bắt đầu tính tiền <span class="text-rose-500">*</span></label>
                            <input type="date" id="fStartDate" value="2026-10-15" onchange="calcEndDate()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Ngày khách chính thức dọn vào ở</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Thời hạn hợp đồng (Số tháng) <span class="text-rose-500">*</span></label>
                            <input type="number" id="fDurationMonths" value="8" min="1" max="60" oninput="calcEndDate()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Nhập số tháng hợp đồng (VD: 8)</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Ngày kết thúc (Tự động tính)</label>
                            <input type="text" id="fEndDateDisplay" value="15/06/2027" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 text-xs sm:text-sm font-extrabold text-emerald-700 bg-emerald-50/90 shadow-xs">
                            <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">* Kết quả tự động hiển thị ngày kết thúc</span>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT ACTIONS -->
                <div class="pt-4 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200">
                    <a href="{{ url('qlvan_hanh/Room_list') }}" class="py-3 px-6 rounded-2xl border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i> Hủy Bỏ Quay Lại
                    </a>

                    <button type="submit" class="py-3.5 px-8 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center gap-2 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-floppy-disk text-base"></i>
                        <span>LƯU HỢP ĐỒNG (Đẩy vào CSDL tbl_hop_dong)</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Toast Notification Container -->
    <div id="toast" class="fixed bottom-5 right-5 z-50 hidden bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700 text-xs font-semibold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
        <span id="toastMessage">Thao tác thành công!</span>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Switch water calculation mode
        function switchWaterForm(mode) {
            const btnKhoi = document.getElementById('btnModeKhoi');
            const btnNguoi = document.getElementById('btnModeNguoi');
            const subKhoi = document.getElementById('subWaterKhoi');
            const subNguoi = document.getElementById('subWaterNguoi');

            if (mode === 'khoi') {
                btnKhoi.className = "px-4 py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition-all";
                btnNguoi.className = "px-4 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 transition-all";
                subKhoi.classList.remove('hidden');
                subNguoi.classList.add('hidden');
            } else {
                btnNguoi.className = "px-4 py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition-all";
                btnKhoi.className = "px-4 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 transition-all";
                subNguoi.classList.remove('hidden');
                subKhoi.classList.add('hidden');
            }
        }

        // Calculate end date based on start date + duration months
        function calcEndDate() {
            const startDateVal = document.getElementById('fStartDate').value;
            const monthsVal = parseInt(document.getElementById('fDurationMonths').value) || 0;
            const endDateDisplay = document.getElementById('fEndDateDisplay');

            if (!startDateVal || monthsVal <= 0) {
                endDateDisplay.value = "--/--/----";
                return;
            }

            const startDate = new Date(startDateVal);
            if (isNaN(startDate.getTime())) return;

            startDate.setMonth(startDate.getMonth() + monthsVal);

            const day = String(startDate.getDate()).padStart(2, '0');
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const year = startDate.getFullYear();

            endDateDisplay.value = `${day}/${month}/${year}`;
        }

        // Show Toast Notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Handle Save Form Submission
        function handleSaveForm(e) {
            e.preventDefault();
            const roomCode = document.getElementById('fRoom').value;
            showToast(`Đã đẩy toàn bộ dữ liệu hợp đồng mới vào CSDL tbl_hop_dong thành công! Phòng ${roomCode} đã chuyển sang 'Đã cho thuê'. Đang chuyển về danh sách phòng...`);
            
            setTimeout(() => {
                window.location.href = "{{ url('qlvan_hanh/Room_list') }}";
            }, 1800);
        }

        // Initial Calculation
        window.addEventListener('DOMContentLoaded', () => {
            calcEndDate();
        });
    </script>
</body>

</html>
