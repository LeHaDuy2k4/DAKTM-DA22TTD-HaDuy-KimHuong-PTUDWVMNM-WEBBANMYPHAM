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

// Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
require("../config.php"); 
// Kiểm tra xem ID (maDM) có được gửi qua URL không
if (isset($_GET['maDM']) && !empty($_GET['maDM'])) {
    
    $targetMaDM = $conn->real_escape_string($_GET['maDM']);
    
    // --- BƯỚC 1: KIỂM TRA MỐI QUAN HỆ KHÓA NGOẠI (TÙY CHỌN, KHÔNG BẮT BUỘC) ---
    // Ghi chú: Việc xóa danh mục có thể thất bại nếu có ràng buộc khóa ngoại
    // và danh mục đó đang được sản phẩm sử dụng (ON DELETE RESTRICT).
    // Nếu DB đã thiết lập ON DELETE CASCADE, bạn có thể bỏ qua bước kiểm tra này.
    
    // Ví dụ kiểm tra xem danh mục này có sản phẩm nào đang sử dụng không (Giả định bảng 'sanpham')
    $check_products_sql = "SELECT COUNT(*) AS total FROM sanpham WHERE maDM = '$targetMaDM'";
    $check_result = $conn->query($check_products_sql);
    
    if ($check_result && $check_result->fetch_assoc()['total'] > 0) {
        // Nếu có sản phẩm đang sử dụng, cảnh báo người dùng (hoặc chuyển hướng).
        // Tùy thuộc vào thiết lập DB, có thể không cho phép xóa.
        $error_message = "Lỗi: Không thể xóa danh mục này vì còn có sản phẩm đang sử dụng. Vui lòng gỡ bỏ hoặc chuyển sản phẩm sang danh mục khác trước.";
        header("Location: categories.php?error=" . urlencode($error_message));
        exit();
    }
    
    // --- BƯỚC 2: THỰC HIỆN XOÁ ---
    $delete_sql = "DELETE FROM danhmucsp WHERE maDM = '$targetMaDM'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa danh mục có mã **" . htmlspecialchars($targetMaDM) . "** thành công!";
        header("Location: categories.php?message=" . urlencode($message));
        exit();
    } else {
        // Lỗi thường do khóa ngoại nếu bước 1 bị bỏ qua và ràng buộc DB là RESTRICT
        $error_message = "Lỗi khi xóa CSDL: " . $conn->error;
        
        // Nếu lỗi do khóa ngoại, cung cấp thông báo cụ thể hơn
        if (strpos($conn->error, 'foreign key constraint fails') !== false) {
             $error_message = "Lỗi khóa ngoại: Danh mục này đang được sản phẩm sử dụng và không thể xóa.";
        }
        
        header("Location: categories.php?error=" . urlencode($error_message));
        exit();
    }
    
} else {
    // Không có maDM, chuyển hướng về trang danh sách
    header("Location: categories.php");
    exit();
}
?>