<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: brands.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database
require("../config.php"); 

// Kiểm tra xem ID (maTH) có được gửi qua URL không
if (isset($_GET['maTH']) && !empty($_GET['maTH'])) {
    
    $targetMaTH = $conn->real_escape_string($_GET['maTH']);
    
    // --- BƯỚC 1: KIỂM TRA MỐI QUAN HỆ KHÓA NGOẠI ---
    // Kiểm tra xem thương hiệu này có sản phẩm (bảng 'mathang') nào đang sử dụng không
    // 
    $check_products_sql = "SELECT COUNT(*) AS total FROM mathang WHERE maTH = '$targetMaTH'";
    $check_result = $conn->query($check_products_sql);
    
    if ($check_result && $check_result->fetch_assoc()['total'] > 0) {
        // Nếu có sản phẩm đang sử dụng, chặn xóa để bảo toàn dữ liệu
        $error_message = "Lỗi: Không thể xóa thương hiệu này vì đang chứa sản phẩm. Vui lòng gỡ bỏ hoặc chuyển sản phẩm sang thương hiệu khác trước.";
        header("Location: brands.php?error=" . urlencode($error_message));
        exit();
    }
    
    
    // --- BƯỚC 2: THỰC HIỆN XOÁ THƯƠNG HIỆU ---
    $delete_sql = "DELETE FROM thuonghieu WHERE maTH = '$targetMaTH'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa thương hiệu mã " . htmlspecialchars($targetMaTH) . " thành công!";
        header("Location: brands.php?message=" . urlencode($message));
        exit();
    } else {
        // Xử lý lỗi SQL
        $error_message = "Lỗi khi xóa CSDL: " . $conn->error;
        header("Location: brands.php?error=" . urlencode($error_message));
        exit();
    }
    
} else {
    // Không có maTH, chuyển hướng về trang danh sách
    header("Location: brands.php");
    exit();
}
?>