<?php

require("config.php");

if(isset($_POST['sbSubmit'])) {
    $username = trim($_POST['txtUsername']);
    $password = $_POST['txtPassword'];

    // Mã hóa mật khẩu bằng md5
    $password_md5 = md5($password);

    $sql = "SELECT * FROM nguoidung WHERE tenDangnhap = ? AND matKhau = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password_md5);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        session_start();
        $_SESSION['tenDangnhap'] = $row['tenDangnhap'];
        $_SESSION['emailNguoidung'] = $row['email'];
        $_SESSION['quyen'] = $row['quyen'];
        $_SESSION['trangThai'] = $row['trangThai'];

        echo "<script>alert('Đăng nhập thành công!'); window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Tên đăng nhập hoặc mật khẩu không đúng!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Trang đăng nhập</title>

    <style>
        /* Áp dụng font Times New Roman cho toàn bộ trang */
        body, h3, label, button, input, .btn-google {
            font-family: "Times New Roman", serif !important;
        }

        body {
            background: #f4f4f4;
            margin: 0; padding: 0;
        }
        .w3-container {
            max-width: 420px;
            margin: 60px auto;
            background: #fff;
            padding: 28px 26px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #d63384;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            font-size: 14px;
            color: #444;
            display: block;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 11px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #d9d9d9;
            box-sizing: border-box;
        }
        .btn-default {
            width: 100%;
            background: #d63384;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .btn-default:hover {
            opacity: 0.9;
        }

        /* Nút Google */
        .btn-google {
            width: 100%;
            background: #ffffff;
            border: 2px solid #d63384;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 14px;
            color: #d63384;
            transition: 0.25s;
        }

        .btn-google img {
            width: 22px;
            height: 22px;
        }

        .btn-google:hover {
            background: #d63384;
            color: #fff;
            border-color: #d63384;
            transform: translateY(-1px);
        }

        /* Style cho liên kết đăng ký mới */
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .register-link a {
            color: #d63384; /* Màu hồng để nhất quán với theme */
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>

    <div class="w3-container">
        <h3>Đăng nhập</h3>

        <form action="" method="post" name="f1">
            <div class="form-group">
                <label for="txtUsername">Tên đăng nhập:</label>
                <input type="text" class="form-control" id="txtUsername" name="txtUsername" required />
            </div>

            <div class="form-group">
                <label for="txtPassword">Mật khẩu:</label>
                <input type="password" class="form-control" id="txtPassword" name="txtPassword" required />
            </div>

            <button type="submit" class="btn-default" name="sbSubmit">Đăng nhập</button>

            <button type="button" class="btn-google" onclick="window.location.href='google-login.php'">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo">
                Đăng nhập với Google
            </button>
        </form>

        <div class="register-link">
            Bạn chưa có tài khoản? <a href="signup.php">Đăng ký</a>
        </div>
    </div>

</body>
</html>