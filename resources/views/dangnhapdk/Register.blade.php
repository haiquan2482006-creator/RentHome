<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - RentHome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #0b1329;
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Full Screen Background Layout */
        .form-wrapper {
            position: relative;
            display: flex;
            justify-content: flex-end; /* Align right side like screenshot */
            align-items: center;
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(90deg, rgba(11, 19, 41, 0.45) 0%, rgba(15, 23, 42, 0.75) 100%), 
                        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop') no-repeat center center / cover;
            padding: 40px 8%;
        }

        @media (max-width: 900px) {
            .form-wrapper {
                justify-content: center;
                padding: 24px 16px;
            }
        }

        /* Translucent Glass Card like Reference Image */
        .register-container {
            position: relative;
            z-index: 2;
            background: rgba(30, 58, 95, 0.58);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 44px 38px;
            border-radius: 20px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            color: #ffffff;
        }
        
        /* Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 26px;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            margin-bottom: 12px;
        }
        .logo-badge img {
            height: 34px;
            width: 34px;
            object-fit: contain;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            margin-bottom: 6px;
        }
        .brand-logo span {
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .brand-logo span b {
            color: #38bdf8;
        }

        .form-title {
            color: #ffffff;
            font-size: 1.45rem;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.01em;
        }
        .form-subtitle {
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 500;
        }

        /* Account Type Switcher Card */
        .account-type-selector {
            margin-bottom: 22px;
            background: rgba(255, 255, 255, 0.1);
            padding: 6px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .account-type-selector > .input-label {
            margin-left: 6px;
            margin-top: 4px;
            margin-bottom: 8px;
            color: #e2e8f0;
        }
        .account-type-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .account-type-card {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 16px;
            border-radius: 10px;
            border: 1.5px solid transparent;
            background-color: transparent;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 700;
            color: #cbd5e1;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }
        .account-type-card input[type="radio"] {
            display: none;
        }
        .account-type-card:hover {
            color: #ffffff;
        }
        .account-type-card.active {
            background: rgba(255, 255, 255, 0.22);
            border-color: #38bdf8;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        /* Input Controls */
        .input-group {
            margin-bottom: 18px;
        }
        .input-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .input-label .required-star {
            color: #f87171;
            margin-left: 3px;
        }

        .input-box {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 12px;
            padding: 4px 16px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-box:hover {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.18);
        }
        .input-box:focus-within {
            background: rgba(255, 255, 255, 0.22);
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.25);
            transform: translateY(-1px);
        }
        .input-box i.icon-left {
            color: #cbd5e1;
            font-size: 16px;
            margin-right: 14px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
            transition: color 0.25s ease;
        }
        .input-box:focus-within i.icon-left {
            color: #38bdf8;
        }
        .input-box input {
            width: 100%;
            background: transparent;
            border: none;
            color: #ffffff;
            padding: 11px 0;
            font-size: 0.95rem;
            font-weight: 600;
            outline: none;
        }
        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.65);
            font-weight: 400;
        }
        .input-box i.toggle-password {
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
            padding: 4px;
            transition: all 0.2s;
        }
        .input-box i.toggle-password:hover {
            color: #38bdf8;
            transform: scale(1.1);
        }

        /* File Upload styling */
        .file-box {
            background: rgba(255, 255, 255, 0.1);
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 22px;
            transition: all 0.25s ease;
        }
        .file-box:hover {
            border-color: #38bdf8;
            background: rgba(255, 255, 255, 0.16);
        }
        .file-box label {
            color: #e2e8f0;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .file-box input[type="file"] {
            font-size: 0.85rem;
            color: #cbd5e1;
            width: 100%;
            cursor: pointer;
        }

        /* Submit Button like Reference Image */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #0252ad 0%, #0284c7 100%);
            color: #ffffff;
            border: none;
            padding: 15px;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(2, 82, 173, 0.45);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            margin-top: 8px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #02438e 0%, #0369a1 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(2, 82, 173, 0.6);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .login-link {
            display: block;
            text-align: center;
            color: #cbd5e1;
            margin-top: 24px;
            font-size: 0.88rem;
            font-weight: 500;
        }
        .login-link a {
            color: #38bdf8;
            font-weight: 800;
            text-decoration: none;
            margin-left: 4px;
            transition: color 0.2s;
        }
        .login-link a:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-size: 0.86rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        /* Footer Home Link */
        .home-link-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        .home-link-footer a {
            color: #cbd5e1;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.25s ease;
        }
        .home-link-footer a:hover {
            color: #ffffff;
            background-color: rgba(56, 189, 248, 0.3);
            border-color: #38bdf8;
            transform: translateX(-3px);
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="register-container">
            <div class="brand-header">
                <a href="/" class="brand-logo">
                    <div class="logo-badge">
                        <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo">
                    </div>
                </a>
                <div>
                    <a href="/" class="brand-logo">
                        <span>Rent<b>Home</b></span>
                    </a>
                </div>
                <h2 class="form-title">Đăng Ký Tài Khoản</h2>
                <p class="form-subtitle">Tham gia RentHome để khám phá & quản lý bất động sản</p>
            </div>

            @if($errors->any())
                <div class="alert-box">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 18px; flex-shrink: 0;"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="/register" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="account-type-selector">
                    <div class="input-label">Chọn đối tượng sử dụng</div>
                    <div class="account-type-grid">
                        <label class="account-type-card {{ old('account_type', 'canhan') == 'canhan' ? 'active' : '' }}" id="label_canhan">
                            <input type="radio" id="ca_nhan" name="account_type" value="canhan" {{ old('account_type', 'canhan') == 'canhan' ? 'checked' : '' }} onchange="toggleForm()">
                            <i class="fa-solid fa-user"></i> Cá nhân
                        </label>
                        <label class="account-type-card {{ old('account_type') == 'doanhnghiep' ? 'active' : '' }}" id="label_doanhnghiep">
                            <input type="radio" id="doanh_nghiep" name="account_type" value="doanhnghiep" {{ old('account_type') == 'doanhnghiep' ? 'checked' : '' }} onchange="toggleForm()">
                            <i class="fa-solid fa-building"></i> Doanh nghiệp
                        </label>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Địa chỉ Email <span class="required-star">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-envelope icon-left"></i>
                        <input type="email" id="email_input" name="email" value="{{ old('email') }}" placeholder="Nhập địa chỉ Gmail của bạn *" required>
                    </div>
                </div>

                <!-- Phần dành riêng cho Cá nhân -->
                <div id="form_canhan">
                    <div class="input-group">
                        <label class="input-label">Tên tài khoản / Họ tên</label>
                        <div class="input-box">
                            <i class="fa-solid fa-id-card icon-left"></i>
                            <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="Nhập họ và tên của bạn *">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Số điện thoại <span class="required-star">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-phone icon-left"></i>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại liên hệ *" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Mật khẩu <span class="required-star">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock icon-left"></i>
                        <input type="password" id="register_password" name="password" placeholder="Tạo mật khẩu *" required>
                        <i class="fa-solid fa-eye toggle-password" id="toggle_reg_pass" onclick="togglePasswordVisibility('register_password', 'toggle_reg_pass')" title="Ẩn/Hiện mật khẩu"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Xác nhận mật khẩu <span class="required-star">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-shield-halved icon-left"></i>
                        <input type="password" id="register_password_confirm" name="password_confirmation" placeholder="Nhập lại mật khẩu *" required>
                        <i class="fa-solid fa-eye toggle-password" id="toggle_reg_pass_confirm" onclick="togglePasswordVisibility('register_password_confirm', 'toggle_reg_pass_confirm')" title="Ẩn/Hiện mật khẩu"></i>
                    </div>
                </div>

                <!-- Phần dành riêng cho Doanh nghiệp (Mặc định ẩn) -->
                <div id="form_doanhnghiep" style="display: none;">
                    <div class="input-group">
                        <label class="input-label">Tên doanh nghiệp <span class="required-star">*</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-building icon-left"></i>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Nhập tên công ty / doanh nghiệp *">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Địa chỉ doanh nghiệp <span class="required-star">*</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-location-dot icon-left"></i>
                            <input type="text" name="company_address" value="{{ old('company_address') }}" placeholder="Nhập địa chỉ đăng ký kinh doanh *">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Mã số thuế <span class="required-star">*</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-file-invoice-dollar icon-left"></i>
                            <input type="text" name="tax_code" value="{{ old('tax_code') }}" placeholder="Nhập mã số thuế doanh nghiệp *">
                        </div>
                    </div>
                    
                    <div class="file-box">
                        <label><i class="fa-solid fa-file-contract" style="color: #38bdf8;"></i> Giấy phép kinh doanh (nếu có)</label>
                        <input type="file" name="business_license" accept="image/*,.pdf">
                    </div>
                </div>

                <button type="submit" class="btn-submit">ĐĂNG KÝ TÀI KHOẢN</button>

                <div class="login-link">
                    Bạn đã có tài khoản? <a href="/login">Đăng nhập ngay</a>
                </div>

                <div class="home-link-footer">
                    <a href="/"><i class="fa-solid fa-house"></i> Trở về trang chủ RentHome</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleForm() {
            const isEnterprise = document.getElementById('doanh_nghiep').checked;
            const formCaNhan = document.getElementById('form_canhan');
            const formDoanhNghiep = document.getElementById('form_doanhnghiep');
            const emailInput = document.getElementById('email_input');

            const labelCaNhan = document.getElementById('label_canhan');
            const labelDoanhNghiep = document.getElementById('label_doanhnghiep');

            if (isEnterprise) {
                formCaNhan.style.display = 'none';
                formDoanhNghiep.style.display = 'block';
                labelCaNhan.classList.remove('active');
                labelDoanhNghiep.classList.add('active');
                if (emailInput) {
                    emailInput.placeholder = 'Nhập email doanh nghiệp *';
                }
            } else {
                formCaNhan.style.display = 'block';
                formDoanhNghiep.style.display = 'none';
                labelCaNhan.classList.add('active');
                labelDoanhNghiep.classList.remove('active');
                if (emailInput) {
                    emailInput.placeholder = 'Nhập địa chỉ Gmail của bạn *';
                }
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleForm();
        });
    </script>
</body>
</html>