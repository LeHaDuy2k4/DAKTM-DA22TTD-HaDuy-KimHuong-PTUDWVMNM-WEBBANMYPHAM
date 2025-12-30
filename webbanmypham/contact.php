<?php
session_start();
// 1. KẾT NỐI DATABASE
require("config.php"); // Đảm bảo file này chứa kết nối $conn

$message_status = ""; // Biến lưu thông báo

// 2. XỬ LÝ KHI NGƯỜI DÙNG BẤM GỬI
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_send_contact'])) {
    
    // Lấy dữ liệu từ form và làm sạch (chống hack SQL Injection cơ bản)
    $hoTen = $conn->real_escape_string($_POST['hoTen']);
    $email = $conn->real_escape_string($_POST['email']);
    $dienThoai = $conn->real_escape_string($_POST['dienThoai']);
    $noiDung = $conn->real_escape_string($_POST['noiDung']);
    
    // Lấy thời gian hiện tại
    $ngayGui = date('Y-m-d H:i:s');
    
    // Kiểm tra xem người dùng có đang đăng nhập không
    // Nếu có thì lưu tenDangNhap, nếu không thì để NULL
    $tenDangNhap = isset($_SESSION['tenDangnhap']) ? "'" . $_SESSION['tenDangnhap'] . "'" : "NULL";

    // Trạng thái mặc định là 0 (Mới/Chưa xem)
    $trangThai = 0;

    // Câu lệnh SQL Insert
    $sql = "INSERT INTO lienhe (hoTen, email, dienThoai, noiDung, ngayGui, tenDangNhap, trangThai) 
            VALUES ('$hoTen', '$email', '$dienThoai', '$noiDung', '$ngayGui', $tenDangNhap, '$trangThai')";

    if ($conn->query($sql) === TRUE) {
        $message_status = "success"; // Gửi thành công
    } else {
        $message_status = "error";   // Lỗi: $conn->error
    }
}

require("header.php"); 
?>

<style>
/* --- CSS RIÊNG CỦA TRANG CONTACT --- */
.contact-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
}

@media (max-width: 768px) {
    .contact-container { grid-template-columns: 1fr; }
}

/* Phần Thông tin bên trái */
.contact-info h2 {
    color: var(--cosmetics-accent-color, #e91e63);
    font-size: 2rem;
    margin-bottom: 20px;
}
.contact-desc { color: #666; line-height: 1.6; margin-bottom: 30px; }

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border: 1px solid #ffe6ea;
}
.info-icon {
    width: 50px; height: 50px;
    background: #fff0f5;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #e91e63;
    font-size: 1.2rem;
    margin-right: 20px;
}
.info-content h4 { margin: 0 0 5px; color: #333; }
.info-content p { margin: 0; color: #666; }

/* Phần Form bên phải */
.contact-form-card {
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(233, 30, 99, 0.1);
    border-top: 5px solid #e91e63;
}
.form-title { text-align: center; margin-bottom: 30px; color: #333; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    transition: 0.3s;
    font-family: inherit;
}
.form-control:focus { border-color: #e91e63; box-shadow: 0 0 8px rgba(233, 30, 99, 0.2); }

.btn-send {
    width: 100%;
    padding: 14px;
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3);
}
.btn-send:hover { background: #c2185b; transform: translateY(-2px); }

/* Thông báo */
.alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div style="background: #ffe1ec; padding: 40px 0; text-align: center;">
    <h1 style="color: #e91e63; margin: 0;">LIÊN HỆ VỚI CHÚNG TÔI</h1>
    <p style="color: #777; margin-top: 10px;">Chúng tôi luôn sẵn sàng lắng nghe bạn</p>
</div>

<div class="contact-container">
    
    <div class="contact-info">
        <h2>Kết nối ngay</h2>
        <p class="contact-desc">
            Nếu bạn có thắc mắc về sản phẩm, đơn hàng hoặc muốn hợp tác, đừng ngần ngại gửi tin nhắn cho chúng tôi. Đội ngũ tư vấn sẽ phản hồi trong thời gian sớm nhất.
        </p>

        <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-phone-volume"></i></div>
            <div class="info-content">
                <h4>Hotline</h4>
                <p>1900 123 456</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="info-content">
                <h4>Email</h4>
                <p>support@huonggcosmetics.com</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="info-content">
                <h4>Địa chỉ</h4>
                <p>123 Đường Sắc Đẹp, Quận 1, TP.HCM</p>
            </div>
        </div>
    </div>

    <div class="contact-form-card">
        <h3 class="form-title">Gửi tin nhắn</h3>

        <?php if ($message_status == 'success'): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i> Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn và sẽ phản hồi sớm.
            </div>
        <?php elseif ($message_status == 'error'): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i> Có lỗi xảy ra. Vui lòng thử lại sau.
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Họ và Tên</label>
                <input type="text" name="hoTen" class="form-control" placeholder="Nhập họ tên của bạn" required 
                       value="<?php echo isset($_SESSION['hoTen']) ? $_SESSION['hoTen'] : ''; ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Nhập email liên hệ" required
                       value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="dienThoai" class="form-control" placeholder="Nhập số điện thoại" required>
            </div>

            <div class="form-group">
                <label>Nội dung cần hỗ trợ</label>
                <textarea name="noiDung" class="form-control" rows="5" placeholder="Bạn cần tư vấn về vấn đề gì?..." required></textarea>
            </div>

            <button type="submit" name="btn_send_contact" class="btn-send">GỬI YÊU CẦU <i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>

</div>

<?php require("footer.php"); ?>