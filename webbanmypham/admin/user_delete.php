<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: users.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
require("../config.php"); 

// Kiểm tra xem ID (tenDangnhap) có được gửi qua URL không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $targetUsername = $conn->real_escape_string($_GET['id']);
    
    // --- BƯỚC 1: KIỂM TRA ĐỂ ĐẢM BẢO KHÔNG XÓA ADMIN KHÁC (NGUYÊN TẮC AN TOÀN) ---
    $check_admin_sql = "SELECT quyen FROM nguoidung WHERE tenDangnhap = '$targetUsername'";
    $check_result = $conn->query($check_admin_sql);

    if ($check_result && $check_result->num_rows > 0) {
        $user_info = $check_result->fetch_assoc();
        
        // Nếu người dùng đó là Admin (quyen = 1) và không phải là người đang đăng nhập, HỦY BỎ.
        $isCurrentUser = $targetUsername === ($_SESSION['tenDangnhap'] ?? '');

        if ($user_info['quyen'] == 1 || $isCurrentUser) {
            header("Location: users.php?error=" . urlencode("Không thể xóa tài khoản Quản trị viên hoặc tài khoản đang đăng nhập."));
            exit();
        }
    } else {
        header("Location: users.php?error=" . urlencode("Người dùng không tồn tại."));
        exit();
    }
    // --- KẾT THÚC KIỂM TRA ---

    
    // --- BƯỚC 2: THỰC HIỆN XOÁ ---
    $delete_sql = "DELETE FROM nguoidung WHERE tenDangnhap = '$targetUsername'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Đã xóa người dùng " . htmlspecialchars($targetUsername) . " thành công!";
        header("Location: users.php?message=" . urlencode($message));
        exit();
    } else {
        header("Location: users.php?error=" . urlencode("Lỗi khi xóa CSDL: " . $conn->error));
        exit();
    }
    
} else {
    // Không có ID, chuyển hướng về trang danh sách
    header("Location: users.php");
    exit();
}
?>