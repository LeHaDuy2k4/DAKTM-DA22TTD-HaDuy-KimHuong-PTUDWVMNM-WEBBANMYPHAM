<?php
session_start();
require_once("config.php");

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php");
    exit();
}

// 2. KIỂM TRA ID CẦN XÓA
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_ctgh = (int)$_GET['id']; // ID của bảng chitietgiohang
    $tenDangNhap = $_SESSION['tenDangnhap'];

    /* BẢO MẬT: Không chỉ xóa theo ID, mà phải JOIN với bảng giohang 
       để chắc chắn sản phẩm này thuộc về người đang đăng nhập.
       Tránh trường hợp người dùng thay đổi ID trên URL để xóa đồ của người khác.
    */
    $sql = "DELETE ct FROM chitietgiohang ct 
            JOIN giohang g ON ct.giohang_id = g.id 
            WHERE ct.id = $id_ctgh AND g.tenDangNhap = '$tenDangNhap' AND g.trangthai = 0";

    if ($conn->query($sql)) {
        // Xóa thành công, quay lại trang giỏ hàng
        header("Location: cart.php?message=" . urlencode("Đã xóa sản phẩm khỏi giỏ hàng."));
        exit();
    } else {
        // Lỗi truy vấn
        die("Lỗi khi xóa: " . $conn->error);
    }
} else {
    // Nếu không có ID, quay lại trang giỏ hàng
    header("Location: cart.php");
    exit();
}