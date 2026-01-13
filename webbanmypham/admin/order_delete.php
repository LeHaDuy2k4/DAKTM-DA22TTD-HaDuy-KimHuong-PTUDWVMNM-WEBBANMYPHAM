<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: orders.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database
require("../config.php"); 

// Kiểm tra xem ID có được gửi qua URL không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $id = (int)$_GET['id']; // Ép kiểu int để an toàn vì mã đơn hàng là số
    
    // --- BƯỚC 1: KIỂM TRA TRẠNG THÁI ĐƠN HÀNG (NGUYÊN TẮC NGHIỆP VỤ) ---
    $check_sql = "SELECT trangthai FROM dondathang WHERE maDonhang = $id";
    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows > 0) {
        $order_info = $check_result->fetch_assoc();
        $status = $order_info['trangthai'];
        
        // --- CẬP NHẬT: Danh sách các trạng thái ĐƯỢC PHÉP xóa ---
        // Cho phép xóa: Đã hủy, Đã hoàn thành, Yêu cầu trả hàng
        $allowed_delete_status = ['Đã hủy', 'Đã hoàn thành', 'Yêu cầu trả hàng'];

        // Nếu trạng thái hiện tại KHÔNG nằm trong danh sách cho phép
        if (!in_array($status, $allowed_delete_status)) {
            header("Location: orders.php?error=" . urlencode("Không thể xóa đơn hàng đang xử lý (Chỉ xóa đơn Đã hủy, Đã hoàn thành hoặc Yêu cầu trả hàng)."));
            exit();
        }
    } else {
        header("Location: orders.php?error=" . urlencode("Đơn hàng không tồn tại."));
        exit();
    }
    // --- KẾT THÚC KIỂM TRA ---

    
    // --- BƯỚC 2: THỰC HIỆN XOÁ ---
    // Do ràng buộc khóa ngoại trong CSDL, cần xóa chi tiết trước
    $delete_detail_sql = "DELETE FROM chitietdathang WHERE maDonhang = $id";
    $conn->query($delete_detail_sql);

    // Sau đó xóa đơn hàng chính
    $delete_order_sql = "DELETE FROM dondathang WHERE maDonhang = $id";
    
    if ($conn->query($delete_order_sql) === TRUE) {
        $message = "Đã xóa vĩnh viễn đơn hàng #" . $id . " thành công!";
        header("Location: orders.php?message=" . urlencode($message));
        exit();
    } else {
        header("Location: orders.php?error=" . urlencode("Lỗi khi xóa CSDL: " . $conn->error));
        exit();
    }
    
} else {
    // Không có ID, chuyển hướng về trang danh sách
    header("Location: orders.php");
    exit();
}
?>