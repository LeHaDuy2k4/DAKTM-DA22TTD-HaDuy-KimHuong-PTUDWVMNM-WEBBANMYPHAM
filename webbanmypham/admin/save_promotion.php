<?php
session_start();
// Yêu cầu file kết nối database
require("../config.php");

// Kiểm tra nếu dữ liệu được gửi qua phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. LẤY VÀ LÀM SẠCH DỮ LIỆU ĐẦU VÀO
    $action_type = $_POST['action_type']; // 'add' hoặc 'edit'
    $tenKM = $conn->real_escape_string($_POST['tenKM']);
    $phantramgiam = (float)$_POST['phantramgiam'];
    $ngayBD = $_POST['ngayBD'];
    $ngayKT = $_POST['ngayKT'];

    // 2. KIỂM TRÁ LOGIC NGÀY THÁNG
    if (strtotime($ngayKT) < strtotime($ngayBD)) {
        header("Location: promotion_add.php?error=" . urlencode("Ngày kết thúc không được nhỏ hơn ngày bắt đầu."));
        exit();
    }

    // 3. XỬ LÝ THEO LOẠI HÀNH ĐỘNG
    if ($action_type === 'add') {
        // --- THÊM MỚI KHUYẾN MÃI ---
        // Bảng khuyenmai gồm: tenKM, phantramgiam, ngayBD, ngayKT
        $sql = "INSERT INTO khuyenmai (tenKM, phantramgiam, ngayBD, ngayKT) 
                VALUES ('$tenKM', $phantramgiam, '$ngayBD', '$ngayKT')";
        
        $msg = "Thêm chương trình khuyến mãi thành công!";
    } 
    elseif ($action_type === 'edit' && isset($_POST['maKM'])) {
        // --- CẬP NHẬT KHUYẾN MÃI ---
        $maKM = (int)$_POST['maKM'];
        $sql = "UPDATE khuyenmai SET 
                tenKM = '$tenKM', 
                phantramgiam = $phantramgiam, 
                ngayBD = '$ngayBD', 
                ngayKT = '$ngayKT' 
                WHERE maKM = $maKM";
        
        $msg = "Cập nhật chương trình khuyến mãi thành công!";
    }

    // 4. THỰC THI CÂU LỆNH SQL
    if ($conn->query($sql) === TRUE) {
        // Thành công: Chuyển hướng về trang danh sách kèm thông báo
        header("Location: promotions.php?success=" . urlencode($msg));
        exit();
    } else {
        // Thất bại: Hiển thị lỗi
        echo "Lỗi hệ thống: " . $conn->error;
    }

} else {
    // Nếu truy cập trực tiếp file này mà không qua FORM
    header("Location: promotions.php");
    exit();
}

$conn->close();
?>