<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mã xác nhận quên mật khẩu</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #004aad;
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
            color: #444444;
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .code-box {
            text-align: center;
            margin: 35px 0;
        }
        .code {
            display: inline-block;
            font-size: 38px;
            font-weight: bold;
            color: #004aad;
            background-color: #f0f8ff;
            padding: 15px 35px;
            border-radius: 8px;
            letter-spacing: 6px;
            border: 2px dashed #004aad;
        }
        .footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>RentHome</h1>
        </div>
        <div class="content">
            <p>Xin chào,</p>
            <p>Chúng tôi vừa nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với địa chỉ email này tại <strong>RentHome</strong>. Vui lòng sử dụng mã xác nhận dưới đây để tiếp tục:</p>
            
            <div class="code-box">
                <span class="code">{{ $code }}</span>
            </div>
            
            <p>Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email này. Mật khẩu và tài khoản của bạn vẫn được bảo mật an toàn.</p>
            <p>Trân trọng,<br><strong>Đội ngũ RentHome</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} RentHome. Tất cả các quyền được bảo lưu.</p>
            <p>Vui lòng không trả lời email tự động này.</p>
        </div>
    </div>
</body>
</html>
