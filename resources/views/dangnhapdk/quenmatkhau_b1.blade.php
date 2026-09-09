<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - RentHome</title>
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
        
        .input-group {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 25px;
        }
        
        .input-group input {
            margin-bottom: 0;
        }
        
        .input-group button.btn-send-code {
            width: auto;
            white-space: nowrap;
            padding: 10px 20px;
            margin-top: 0;
            background-color: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.5);
            color: white;
            font-size: 14px;
        }
        
        .input-group button.btn-send-code:hover {
            background-color: rgba(255,255,255,0.3);
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
        <div class="contact-container">
            <h2>Quên Mật Khẩu</h2>
            <!-- Hiển thị thông báo lỗi nếu có -->
            @if($errors->any())
                <div style="color: #ff4d4d; margin-bottom: 15px; text-align: center; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/verify-reset-code" method="POST">
                @csrf
                
                <div class="input-group">
                    <input type="email" id="email_input" name="email" placeholder="Gmail *" required>
                    <button type="button" class="btn-send-code" onclick="sendCode()">Gửi mã</button>
                </div>
                
                <input type="text" name="code" placeholder="Mã xác nhận *" required>
                
                <button type="submit">TIẾP TỤC</button>
                
                <div class="login-link">
                    <a href="/login">Quay lại Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function sendCode() {
            const email = document.getElementById('email_input').value;
            if (!email) {
                alert('Vui lòng nhập địa chỉ email trước khi gửi mã!');
                return;
            }

            alert('Đang gửi mã xác nhận, vui lòng chờ...');
            
            fetch('/send-reset-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Mã xác nhận đã được gửi đến email của bạn thành công!');
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối đến máy chủ.');
            });
        }
    </script>
</body>
</html>
