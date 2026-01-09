<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("config.php");
require("header.php");

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php");
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];
$success_msg = "";
$error_msg = "";
$is_updated = false; // Biến cờ để kiểm soát chuyển hướng

// 2. XỬ LÝ CẬP NHẬT KHI NHẤN LƯU
if (isset($_POST['btn_update'])) {
    $hoTen = $conn->real_escape_string($_POST['hoTen']);
    $gioiTinh = (int)$_POST['gioiTinh'];
    $email = $conn->real_escape_string($_POST['email']);
    $dienThoai = $conn->real_escape_string($_POST['dienThoai']);
    $diaChi = $conn->real_escape_string($_POST['diaChi']);

    // Cập nhật dữ liệu vào bảng nguoidung
    $sql_update = "UPDATE nguoidung SET 
                    hoTen = '$hoTen', 
                    gioiTinh = $gioiTinh, 
                    email = '$email', 
                    dienThoai = '$dienThoai', 
                    diaChi = '$diaChi' 
                  WHERE tenDangnhap = '$tenDangNhap'";

    if ($conn->query($sql_update)) {
        $success_msg = "Cập nhật thành công! Đang quay lại trang cá nhân...";
        $is_updated = true;
    } else {
        $error_msg = "Lỗi cập nhật: " . $conn->error;
    }
}

// 3. TRUY VẤN DỮ LIỆU HIỆN TẠI ĐỂ ĐỔ VÀO FORM
$sql = "SELECT * FROM nguoidung WHERE tenDangnhap = '$tenDangNhap'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<style>
    .edit-profile-container {
        max-width: 700px;
        margin: 40px auto;
        background: #fff;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    }
    .edit-title { 
        color: #E91E63; 
        font-weight: 700; 
        margin-bottom: 25px; 
        border-bottom: 2px solid #fff0f5; 
        padding-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group { margin-bottom: 22px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        outline: none;
        font-family: inherit;
        transition: border-color 0.3s;
    }
    .form-group input:focus { border-color: #E91E63; }
    
    .radio-group { display: flex; gap: 30px; padding: 5px 0; }
    .radio-item { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .radio-item input { width: auto; cursor: pointer; accent-color: #E91E63; }

    .btn-save {
        background: #E91E63;
        color: white;
        border: none;
        padding: 14px 30px;
        border-radius: 30px;
        font-weight: 700;
        cursor: pointer;
        width: 100%;
        transition: 0.3s;
        font-size: 1rem;
        margin-top: 10px;
    }
    .btn-save:hover { background: #c2185b; box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3); }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; text-align: center; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="container">
    <div class="edit-profile-container">
        <h2 class="edit-title"><i class="fa-solid fa-user-pen"></i> Chỉnh Sửa Hồ Sơ</h2>

        <?php if($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fa-solid fa-signature"></i> Họ và Tên</label>
                <input type="text" name="hoTen" value="<?php echo htmlspecialchars($user['hoTen']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-venus-mars"></i> Giới tính</label>
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="gioiTinh" value="1" <?php echo ($user['gioiTinh'] == 1) ? 'checked' : ''; ?>> Nam
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="gioiTinh" value="0" <?php echo ($user['gioiTinh'] == 0) ? 'checked' : ''; ?>> Nữ
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-phone"></i> Số điện thoại</label>
                <input type="text" name="dienThoai" value="<?php echo htmlspecialchars($user['dienThoai']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-location-dot"></i> Địa chỉ</label>
                <textarea name="diaChi" rows="3" required><?php echo htmlspecialchars($user['diaChi']); ?></textarea>
            </div>

            <button type="submit" name="btn_update" class="btn-save">LƯU THAY ĐỔI</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="user_profile.php" style="color: #888; text-decoration: none; font-size: 0.9rem;"> 
                    <i class="fa-solid fa-circle-arrow-left"></i> Quay lại trang cá nhân
                </a>
            </div>
        </form>
    </div>
</div>

<?php if ($is_updated): ?>
    <script>
        setTimeout(function() {
            window.location.href = 'user_profile.php';
        }, 2000); // Chờ 2 giây để khách hàng thấy thông báo rồi mới chuyển hướng
    </script>
<?php endif; ?>

<?php require("footer.php"); ?>