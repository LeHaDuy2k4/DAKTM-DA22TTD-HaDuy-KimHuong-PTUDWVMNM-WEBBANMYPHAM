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
$user = null;

// 2. TRUY VẤN THÔNG TIN NGƯỜI DÙNG TỪ DATABASE
if (isset($conn)) {
    $sql = "SELECT hoTen, gioiTinh, email, dienThoai, diaChi 
            FROM nguoidung 
            WHERE tenDangnhap = '$tenDangNhap'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    :root {
        --primary-pink: #E91E63;
        --secondary-pink: #fff0f5;
        --accent-green: #4CAF50;
        --accent-orange: #FF9800; /* Màu mới cho nút đánh giá */
        --text-dark: #333;
    }

    .profile-wrapper {
        background-color: #fff8fb;
        min-height: 80vh;
        padding: 40px 0;
    }

    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .profile-header {
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 2px solid var(--secondary-pink);
        padding-bottom: 20px;
    }

    .profile-header h2 { 
        color: var(--primary-pink); 
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .profile-header p { color: #888; font-size: 0.95rem; }
    
    .info-group {
        display: flex;
        padding: 18px 0;
        border-bottom: 1px solid #f9f9f9;
        align-items: center;
    }

    .info-label {
        flex: 0 0 180px;
        font-weight: 600;
        color: #666;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-label i { color: var(--primary-pink); width: 20px; }

    .info-value {
        flex: 1;
        color: var(--text-dark);
        font-weight: 500;
    }

    /* Group nút bấm */
    .profile-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .btn-profile {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        color: white;
    }

    .btn-edit { background: var(--primary-pink); }
    .btn-edit:hover { background: #c2185b; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3); }

    .btn-orders { background: var(--accent-green); }
    .btn-orders:hover { background: #388E3C; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3); }

    /* Style nút Đánh giá mới */
    .btn-reviews { background: var(--accent-orange); }
    .btn-reviews:hover { background: #e68a00; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3); }

    @media (max-width: 600px) {
        .info-group { flex-direction: column; align-items: flex-start; gap: 5px; }
        .info-label { flex: none; }
        .btn-profile { width: 100%; justify-content: center; }
    }
</style>

<div class="profile-wrapper">
    <div class="profile-container">
        <div class="profile-header">
            <h2><i class="fa-solid fa-circle-user"></i> Hồ Sơ Của Tôi</h2>
            <p>Thông tin cá nhân được bảo mật an toàn</p>
        </div>

        <?php if ($user): ?>
            <div class="info-group">
                <div class="info-label"><i class="fa-solid fa-signature"></i> Họ và Tên:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['hoTen']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label"><i class="fa-solid fa-venus-mars"></i> Giới tính:</div>
                <div class="info-value">
                    <?php 
                        if($user['gioiTinh'] == 1) echo "Nam";
                        else if($user['gioiTinh'] == 0) echo "Nữ";
                        else echo "Chưa cập nhật";
                    ?>
                </div>
            </div>

            <div class="info-group">
                <div class="info-label"><i class="fa-solid fa-envelope"></i> Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label"><i class="fa-solid fa-phone"></i> Điện thoại:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['dienThoai']); ?></div>
            </div>

            <div class="info-group" style="border-bottom: none;">
                <div class="info-label"><i class="fa-solid fa-location-dot"></i> Địa chỉ:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['diaChi']); ?></div>
            </div>

            <div class="profile-actions">
                <a href="edit_profile.php" class="btn-profile btn-edit">
                    <i class="fa-solid fa-user-pen"></i> Sửa thông tin
                </a>
                <a href="order_history.php" class="btn-profile btn-orders">
                    <i class="fa-solid fa-receipt"></i> Đơn hàng của tôi
                </a>
                <a href="review_history.php" class="btn-profile btn-reviews">
                    <i class="fa-solid fa-star-half-stroke"></i> Lịch sử đánh giá
                </a>
            </div>
            
            <div style="text-align: center; margin-top: 25px;">
                <a href="index.php" style="color: #999; text-decoration: none; font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ
                </a>
            </div>

        <?php else: ?>
            <div style="text-align: center; padding: 20px;">
                <p style="color: red;">Rất tiếc, không tìm thấy dữ liệu tài khoản.</p>
                <a href="login.php" class="btn-profile btn-edit" style="margin-top: 10px;">Đăng nhập lại</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require("footer.php"); ?>