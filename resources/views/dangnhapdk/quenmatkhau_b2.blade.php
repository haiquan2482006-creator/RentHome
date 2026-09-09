<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu - RentHome</title>
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
        .contact-container input {
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
        .contact-container input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }
        .contact-container button {
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
        .contact-container button:hover {
            background-color: #003882;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="contact-container">
            <h2>Đặt Lại Mật Khẩu</h2>
            <!-- Hiển thị thông báo lỗi nếu có -->
            @if($errors->any())
                <div style="color: #ff4d4d; margin-bottom: 15px; text-align: center; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/dat-lai-mat-khau" method="POST">
                @csrf
                <input type="password" name="new_password" placeholder="Nhập mật khẩu mới *" required>
                
                <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu *" required>
                
                <button type="submit">XÁC NHẬN</button>
            </form>
        </div>
    </div>
</body>
</html>
