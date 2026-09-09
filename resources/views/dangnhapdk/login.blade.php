<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - RentHome</title>
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
        .contact-container {
            position: relative;
            z-index: 2;
            background: rgba(30, 58, 95, 0.58);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 44px 38px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 
                        0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            color: #ffffff;
        }
        
        /* Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
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

        /* Inputs */
        .input-group {
            margin-bottom: 20px;
        }
        .input-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 8px;
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
            padding: 12px 0;
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

        .forgot-password {
            display: inline-block;
            float: right;
            color: #38bdf8;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: -4px;
            margin-bottom: 22px;
            transition: all 0.2s;
        }
        .forgot-password:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }

        /* Primary Button like Reference Image */
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
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #02438e 0%, #0369a1 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(2, 82, 173, 0.6);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .register-link {
            display: block;
            text-align: center;
            color: #cbd5e1;
            margin-top: 24px;
            font-size: 0.88rem;
            font-weight: 500;
        }
        .register-link a {
            color: #38bdf8;
            font-weight: 800;
            text-decoration: none;
            margin-left: 4px;
            transition: color 0.2s;
        }
        .register-link a:hover {
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
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.25);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        /* Footer Link */
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
        <div class="contact-container">

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
                <h2 class="form-title">Đăng Nhập</h2>
                <p class="form-subtitle">Chào mừng trở lại! Vui lòng nhập thông tin để tiếp tục</p>
            </div>
            
            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-danger">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 18px; flex-shrink: 0;"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="input-group">
                    <label class="input-label">Email đăng nhập <span class="required-star">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-envelope icon-left"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Nhập địa chỉ email *" required>
                    </div>
                </div>
                
                <div class="input-group" style="margin-bottom: 12px;">
                    <label class="input-label">Mật khẩu <span class="required-star">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock icon-left"></i>
                        <input type="password" id="password_input" name="password" placeholder="Nhập mật khẩu *" required>
                        <i class="fa-solid fa-eye toggle-password" id="toggle_password_icon" onclick="togglePasswordVisibility('password_input', 'toggle_password_icon')" title="Ẩn/Hiện mật khẩu"></i>
                    </div>
                </div>
                
                <div style="overflow: hidden; margin-bottom: 22px;">
                    <a href="/quen-mat-khau" class="forgot-password">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
                
                <div class="register-link">
                    Bạn chưa có tài khoản? <a href="/Register">Đăng ký ngay</a>
                </div>

                <div class="home-link-footer">
                    <a href="/"><i class="fa-solid fa-house"></i> Trở về trang chủ RentHome</a>
                </div>
            </form>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>