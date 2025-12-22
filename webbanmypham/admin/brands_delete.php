<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    // Chuyển hướng về trang thương hiệu nếu không phải Admin
    header("Location: brands.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
require("../config.php"); 

// Kiểm tra xem ID (maTH) có được gửi qua URL không
if (isset($_GET['maTH']) && !empty($_GET['maTH'])) {
    
    $targetMaTH = $conn->real_escape_string($_GET['maTH']);
    
    // --- BƯỚC 1: KIỂM TRA MỐI QUAN HỆ KHÓA NGOẠI ---
    // Kiểm tra xem thương hiệu này có sản phẩm nào đang sử dụng không (Giả định bảng 'sanpham')
    $check_products_sql = "SELECT COUNT(*) AS total FROM sanpham WHERE maTH = '$targetMaTH'";
    $check_result = $conn->query($check_products_sql);
    
    // Giả định: Nếu có sản phẩm, không cho phép xóa (RESTRICT) để tránh mất dữ liệu.
    if ($check_result && $check_result->fetch_assoc()['total'] > 0) {
        $error_message = "Lỗi: Không thể xóa thương hiệu này vì còn có sản phẩm đang sử dụng. Vui lòng gỡ bỏ hoặc chuyển sản phẩm sang thương hiệu khác trước.";
        header("Location: brands.php?error=" . urlencode($error_message));
        exit();
    }
    
    // --- BƯỚC 2: THỰC HIỆN XOÁ ---
    // Sử dụng bảng 'thuonghieu'
    $delete_sql = "DELETE FROM thuonghieu WHERE maTH = '$targetMaTH'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa thương hiệu có mã **" . htmlspecialchars($targetMaTH) . "** thành công!";
        header("Location: brands.php?message=" . urlencode($message));
        exit();
    } else {
        // Xử lý lỗi SQL (có thể do ràng buộc khóa ngoại nếu bước 1 bị bỏ qua)
        $error_message = "Lỗi khi xóa CSDL: " . $conn->error;
        
        if (strpos($conn->error, 'foreign key constraint fails') !== false) {
             $error_message = "Lỗi khóa ngoại: Thương hiệu này đang được sản phẩm sử dụng và không thể xóa.";
        }
        
        header("Location: brands.php?error=" . urlencode($error_message));
        exit();
    }
    
} else {
    // Không có maTH, chuyển hướng về trang danh sách
    header("Location: brands.php");
    exit();
}
?>