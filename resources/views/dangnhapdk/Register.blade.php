<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - RentHome</title>
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
            padding: 40px 0;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 550px;
            box-sizing: border-box;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .register-container h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 28px;
            margin-top: 0;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container input[type="tel"],
        .register-container input[type="file"] {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            color: #fff;
            padding: 10px 0;
            margin-bottom: 25px;
            font-size: 16px;
            outline: none;
            box-sizing: border-box;
        }
        .register-container input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }
        .register-container input[type="file"] {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 0;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            color: white;
            font-size: 16px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
            gap: 8px;
        }
        .register-container button {
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
            margin-top: 10px;
        }
        .register-container button:hover {
            background-color: #003882;
        }
        .login-link {
            display: block;
            text-align: center;
            color: white;
            margin-top: 25px;
            font-size: 15px;
        }
        .login-link a {
            color: #fff;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="register-container">
            <h2>Đăng Ký</h2>

            @if($errors->any())
                <div style="background-color: rgba(220, 53, 69, 0.85); color: white; padding: 10px 15px; border-radius: 4px; text-align: left; margin-bottom: 20px; font-size: 14px;">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="/register" method="POST" enctype="multipart/form-data">
                @csrf
                
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Gmail *" required>
                
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Tên đăng nhập *" required>
                
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại *" required>
                
                <input type="password" name="password" placeholder="Mật khẩu *" required>
                
                <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu *" required>

                <div class="radio-group">
                    <label>
                        <input type="radio" id="ca_nhan" name="account_type" value="canhan" {{ old('account_type', 'canhan') == 'canhan' ? 'checked' : '' }} onchange="toggleForm()">
                        Cá nhân
                    </label>
                    <label>
                        <input type="radio" id="doanh_nghiep" name="account_type" value="doanhnghiep" {{ old('account_type') == 'doanhnghiep' ? 'checked' : '' }} onchange="toggleForm()">
                        Doanh nghiệp
                    </label>
                </div>

                <!-- Phần dành riêng cho Cá nhân -->
                <div id="form_canhan">
                    <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="Tên tài khoản *">
                </div>

                <!-- Phần dành riêng cho Doanh nghiệp (Mặc định ẩn) -->
                <div id="form_doanhnghiep" style="display: none;">
                    <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Tên doanh nghiệp *">
                    
                    <input type="text" name="company_address" value="{{ old('company_address') }}" placeholder="Địa chỉ doanh nghiệp *">
                    
                    <input type="text" name="tax_code" value="{{ old('tax_code') }}" placeholder="Mã số thuế *">
                    
                    <input type="email" name="company_email" value="{{ old('company_email') }}" placeholder="Gmail doanh nghiệp *">
                    
                    <div style="margin-bottom: 25px;">
                        <label style="color: rgba(255, 255, 255, 0.8); font-size: 14px; display: block; margin-bottom: 5px;">Giấy phép kinh doanh</label>
                        <input type="file" name="business_license" accept="image/*,.pdf" style="margin-bottom: 0;">
                    </div>
                </div>

                <button type="submit">ĐĂNG KÝ</button>

                <div class="login-link">
                    <a href="/login">Đã có tài khoản</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleForm() {
            const isEnterprise = document.getElementById('doanh_nghiep').checked;
            const formCaNhan = document.getElementById('form_canhan');
            const formDoanhNghiep = document.getElementById('form_doanhnghiep');

            if (isEnterprise) {
                formCaNhan.style.display = 'none';
                formDoanhNghiep.style.display = 'block';
            } else {
                formCaNhan.style.display = 'block';
                formDoanhNghiep.style.display = 'none';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleForm();
        });
    </script>
</body>
</html>