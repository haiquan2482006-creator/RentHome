<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - RentHome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?q=80&w=2075&auto=format&fit=crop') no-repeat center center/cover;
            background-color: #333;
        }
        .contact-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 50px 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .contact-container h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 28px;
            margin-top: 0;
        }
        .input-box {
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            margin-bottom: 25px;
            padding-bottom: 5px;
        }
        .input-box i.icon-left {
            color: #fff;
            font-size: 18px;
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        .input-box input {
            width: 100%;
            background: transparent;
            border: none;
            color: #fff;
            padding: 10px 0;
            font-size: 16px;
            outline: none;
            box-sizing: border-box;
        }
        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }
        .input-box i.toggle-password {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
            margin-right: 0;
            width: auto;
            transition: color 0.3s;
        }
        .input-box i.toggle-password:hover {
            color: #fff;
        }
        .contact-container button[type="submit"] {
            width: 100%;
            background-color: #004aad;
            color: white;
            border: none;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 5px;
        }
        .contact-container button[type="submit"]:hover {
            background-color: #003882;
        }
        .forgot-password {
            display: block;
            text-align: right;
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 25px;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        .register-link {
            display: block;
            text-align: center;
            color: white;
            margin-top: 25px;
            font-size: 15px;
        }
        .register-link a {
            color: #fff;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="contact-container">
            <h2>Đăng Nhập</h2>
            
            @if(session('success'))
                <div style="background-color: rgba(40, 167, 69, 0.8); color: white; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: rgba(220, 53, 69, 0.85); color: white; padding: 10px; border-radius: 4px; text-align: left; margin-bottom: 20px; font-size: 14px;">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="input-box">
                    <i class="fa-solid fa-user icon-left"></i>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Tên đăng nhập hoặc Email *" required>
                </div>
                
                <div class="input-box">
                    <i class="fa-solid fa-lock icon-left"></i>
                    <input type="password" id="password_input" name="password" placeholder="Mật khẩu *" required>
                    <i class="fa-solid fa-eye toggle-password" id="toggle_password_icon" onclick="togglePasswordVisibility('password_input', 'toggle_password_icon')" title="Ẩn/Hiện mật khẩu"></i>
                </div>
                
                <a href="/quen-mat-khau" class="forgot-password">Quên mật khẩu?</a>
                
                <button type="submit">ĐĂNG NHẬP</button>
                
                <div class="register-link">
                    Bạn chưa có tài khoản? <a href="/Register">Đăng ký</a>
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