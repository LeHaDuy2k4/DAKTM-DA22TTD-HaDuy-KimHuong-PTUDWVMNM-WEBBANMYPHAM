<?php
session_start();
// 1. KẾT NỐI CƠ SỞ DỮ LIỆU
require("../config.php");

// 2. KIỂM TRA QUYỀN TRUY CẬP (Tùy chọn: Đảm bảo chỉ Admin mới có thể xóa)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: promotions.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// 3. KIỂM TRA ID CẦN XÓA
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Ép kiểu về số nguyên để bảo mật SQL Injection
    $maKM = (int)$_GET['id'];

    // 4. THỰC THI LỆNH XÓA
    // Bảng khuyenmai có khóa chính là maKM
    $sql = "DELETE FROM khuyenmai WHERE maKM = $maKM";

    if ($conn->query($sql) === TRUE) {
        // Thành công: Quay về trang danh sách với thông báo xanh
        header("Location: promotions.php?success=" . urlencode("Đã xóa chương trình khuyến mãi thành công!"));
        exit();
    } else {
        // Thất bại: Nếu có lỗi ràng buộc dữ liệu (ví dụ KM đang được dùng trong đơn hàng)
        header("Location: promotions.php?error=" . urlencode("Không thể xóa khuyến mãi này: " . $conn->error));
        exit();
    }
} else {
    // Nếu không có ID truyền vào, đẩy về trang danh sách
    header("Location: promotions.php");
    exit();
}

// Đóng kết nối
$conn->close();
?>