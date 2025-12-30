<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    // Chuyển hướng về trang sản phẩm nếu không phải Admin
    header("Location: products.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
require("../config.php"); 

// Kiểm tra xem ID (maMH) có được gửi qua URL không
if (isset($_GET['maMH']) && !empty($_GET['maMH'])) {
    
    // Lấy và làm sạch mã mặt hàng
    $targetMaMH = $conn->real_escape_string($_GET['maMH']);
    
    // --- BƯỚC 1: KIỂM TRA MỐI QUAN HỆ KHÓA NGOẠI ---
    // Kiểm tra xem sản phẩm này có đang được sử dụng trong các đơn hàng không
    $check_orders_sql = "SELECT COUNT(*) AS total FROM chitietdonhang WHERE maMH = '$targetMaMH'";
    $check_result = $conn->query($check_orders_sql);
    
    if ($check_result && $check_result->fetch_assoc()['total'] > 0) {
        $error_message = "Lỗi: Không thể xóa sản phẩm này vì đã có trong các đơn hàng đã xử lý. Vui lòng kiểm tra lại.";
        header("Location: products.php?error=" . urlencode($error_message));
        exit();
    }
    
    
    // --- BƯỚC 2: THỰC HIỆN XOÁ SẢN PHẨM ---
    // Sử dụng bảng 'mathangsp'
    $delete_sql = "DELETE FROM mathang WHERE maMH = '$targetMaMH'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa sản phẩm có mã **" . htmlspecialchars($targetMaMH) . "** thành công!";
        header("Location: products.php?message=" . urlencode($message));
        exit();
    } else {
        // Xử lý lỗi SQL (nếu có lỗi khác, thường là do lỗi DB)
        $error_message = "Lỗi khi xóa CSDL: " . $conn->error;
        
        // Cung cấp thông báo rõ ràng hơn nếu lỗi là do ràng buộc khóa ngoại (phòng trường hợp logic BƯỚC 1 không hoàn hảo)
        if (strpos($conn->error, 'foreign key constraint fails') !== false) {
             $error_message = "Lỗi khóa ngoại: Sản phẩm này đang được sử dụng trong các đơn hàng và không thể xóa.";
        }
        
        header("Location: products.php?error=" . urlencode($error_message));
        exit();
    }
    
} else {
    // Không có maMH, chuyển hướng về trang danh sách
    header("Location: products.php");
    exit();
}
?>