<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: products.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database
require("../config.php"); 

// Kiểm tra xem ID (maMH) có được gửi qua URL không
if (isset($_GET['maMH']) && !empty($_GET['maMH'])) {
    
    // Lấy và làm sạch dữ liệu đầu vào
    $targetID = $conn->real_escape_string($_GET['maMH']);
    
    // --- BƯỚC 1: KIỂM TRA SỰ TỒN TẠI & RÀNG BUỘC KHÓA NGOẠI ---
    // Kiểm tra sản phẩm có tồn tại không
    $check_exist_sql = "SELECT tenMH FROM mathang WHERE maMH = '$targetID'";
    $exist_result = $conn->query($check_exist_sql);

    if ($exist_result && $exist_result->num_rows > 0) {
        $product_info = $exist_result->fetch_assoc();
        
        // Kiểm tra xem sản phẩm có nằm trong đơn hàng nào không (bảng chitietdathang)
        // Lưu ý: Tên bảng là 'chitietdathang' dựa theo các file trước
        $check_order_sql = "SELECT COUNT(*) as total FROM chitietdathang WHERE maMH = '$targetID'";
        $order_result = $conn->query($check_order_sql);
        $order_data = $order_result->fetch_assoc();

        if ($order_data['total'] > 0) {
            // Nếu sản phẩm đã từng được mua, KHÔNG ĐƯỢC XÓA để bảo toàn lịch sử đơn hàng
            header("Location: products.php?error=" . urlencode("Không thể xóa: Sản phẩm này đang nằm trong " . $order_data['total'] . " đơn hàng. Hãy ẩn sản phẩm thay vì xóa."));
            exit();
        }
    } else {
        header("Location: products.php?error=" . urlencode("Sản phẩm không tồn tại."));
        exit();
    }
    // --- KẾT THÚC KIỂM TRA ---

    
    // --- BƯỚC 2: THỰC HIỆN XOÁ ---
    $delete_sql = "DELETE FROM mathang WHERE maMH = '$targetID'";
    
    if ($conn->query($delete_sql) === TRUE) {
        // (Tùy chọn: Bạn có thể thêm code xóa file ảnh tại đây nếu muốn dọn dẹp server)
        
        $message = "Đã xóa sản phẩm mã " . htmlspecialchars($targetID) . " thành công!";
        header("Location: products.php?message=" . urlencode($message));
        exit();
    } else {
        // Thông báo lỗi cụ thể
        header("Location: products.php?error=" . urlencode("Lỗi khi xóa CSDL: " . $conn->error));
        exit();
    }
    
} else {
    // Không có ID, chuyển hướng về trang danh sách
    header("Location: products.php");
    exit();
}
?>