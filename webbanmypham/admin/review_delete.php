<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: reviews.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
require("../config.php"); 

// Kiểm tra xem ID (maDG) có được gửi qua URL không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Ép kiểu sang số nguyên (int) vì mã đánh giá thường là số tự tăng
    $targetId = (int)$_GET['id'];
    
    // --- BƯỚC 1: KIỂM TRA SỰ TỒN TẠI CỦA ĐÁNH GIÁ ---
    // Mặc dù đánh giá không có quyền "Admin" để bảo vệ, 
    // nhưng ta kiểm tra xem nó có tồn tại không để báo lỗi chính xác.
    $check_sql = "SELECT maDG FROM danhgia WHERE maDG = $targetId";
    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows == 0) {
        header("Location: reviews.php?error=" . urlencode("Đánh giá không tồn tại hoặc đã bị xóa."));
        exit();
    }
    // --- KẾT THÚC KIỂM TRA ---

    
    // --- BƯỚC 2: THỰC HIỆN XOÁ ---
    $delete_sql = "DELETE FROM danhgia WHERE maDG = $targetId";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa đánh giá mã #" . $targetId . " thành công!";
        header("Location: reviews.php?message=" . urlencode($message));
        exit();
    } else {
        header("Location: reviews.php?error=" . urlencode("Lỗi khi xóa CSDL: " . $conn->error));
        exit();
    }
    
} else {
    // Không có ID, chuyển hướng về trang danh sách
    header("Location: reviews.php");
    exit();
}
?>