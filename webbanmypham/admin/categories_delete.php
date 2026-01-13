<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: categories.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database
require("../config.php"); 

// Kiểm tra xem ID (maDM) có được gửi qua URL không
if (isset($_GET['maDM']) && !empty($_GET['maDM'])) {
    
    // Lấy và làm sạch mã danh mục
    $targetMaDM = $conn->real_escape_string($_GET['maDM']);
    
    // --- BƯỚC 1: KIỂM TRA MỐI QUAN HỆ KHÓA NGOẠI ---
    // Kiểm tra xem danh mục này có chứa sản phẩm nào không
    // Lưu ý: Tên bảng là 'mathang' dựa theo các file trước
    $check_products_sql = "SELECT COUNT(*) AS total FROM mathang WHERE maDM = '$targetMaDM'";
    $check_result = $conn->query($check_products_sql);
    
    if ($check_result && $check_result->fetch_assoc()['total'] > 0) {
        $error_message = "Lỗi: Không thể xóa danh mục này vì đang chứa sản phẩm. Vui lòng xóa hoặc di chuyển sản phẩm sang danh mục khác trước.";
        header("Location: categories.php?error=" . urlencode($error_message));
        exit();
    }
    
    // --- BƯỚC 2: THỰC HIỆN XOÁ DANH MỤC ---
    $delete_sql = "DELETE FROM danhmucsp WHERE maDM = '$targetMaDM'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa danh mục mã " . htmlspecialchars($targetMaDM) . " thành công!";
        header("Location: categories.php?message=" . urlencode($message));
        exit();
    } else {
        // Xử lý lỗi SQL
        $error_message = "Lỗi khi xóa CSDL: " . $conn->error;
        header("Location: categories.php?error=" . urlencode($error_message));
        exit();
    }
    
} else {
    // Không có maDM, chuyển hướng về trang danh sách
    header("Location: categories.php");
    exit();
}
?>