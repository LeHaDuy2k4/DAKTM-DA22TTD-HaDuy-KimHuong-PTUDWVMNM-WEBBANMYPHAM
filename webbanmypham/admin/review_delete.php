<?php
session_start();
require("../config.php");

// 1. KIỂM TRA QUYỀN ADMIN
// Chỉ cho phép tài khoản có quyền admin (quyen = 1) thực hiện thao tác xóa
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: ../login.php");
    exit();
}

// 2. LẤY MÃ ĐÁNH GIÁ CẦN XÓA
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $maDG = (int)$_GET['id']; // Ép kiểu số nguyên để bảo mật

    // 3. THỰC HIỆN LỆNH XÓA TRONG DATABASE
    // Câu lệnh xóa dựa trên khóa chính maDG
    $sql = "DELETE FROM danhgia WHERE maDG = $maDG";

    if ($conn->query($sql)) {
        // Xóa thành công, gửi thông báo qua JavaScript và quay lại trang reviews.php
        echo "<script>
                alert('Đã xóa đánh giá thành công!');
                window.location.href = 'reviews.php';
              </script>";
    } else {
        // Lỗi truy vấn
        echo "<script>
                alert('Lỗi: Không thể xóa đánh giá này. " . $conn->error . "');
                window.location.href = 'reviews.php';
              </script>";
    }
} else {
    // Nếu không có ID, quay lại trang quản lý
    header("Location: reviews.php");
    exit();
}

$conn->close();
?>