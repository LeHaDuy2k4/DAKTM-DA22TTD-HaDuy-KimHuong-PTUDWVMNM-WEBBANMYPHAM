<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: slideshow.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Yêu cầu file kết nối database
require("../config.php"); 

// Kiểm tra xem ID có được gửi qua URL không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Ép kiểu sang int để đảm bảo an toàn vì ID thường là số
    $targetId = (int)$_GET['id'];
    
    // --- BƯỚC 1: KIỂM TRA SỰ TỒN TẠI & LẤY TÊN ẢNH ---
    // Cần lấy tên ảnh để xóa file vật lý sau khi xóa trong CSDL
    $check_sql = "SELECT hinhAnh FROM trinhchieu WHERE maTC = $targetId";
    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows > 0) {
        $slide_info = $check_result->fetch_assoc();
        $fileName = $slide_info['hinhAnh'];
        $filePath = "../uploads/" . $fileName;

        // --- BƯỚC 2: THỰC HIỆN XOÁ TRONG CSDL ---
        $delete_sql = "DELETE FROM trinhchieu WHERE maTC = $targetId";
        
        if ($conn->query($delete_sql) === TRUE) {
            
            // --- BƯỚC 3: XOÁ FILE ẢNH VẬT LÝ (Dọn dẹp server) ---
            if (!empty($fileName) && file_exists($filePath)) {
                unlink($filePath); // Hàm xóa file của PHP
            }

            $message = "Đã xóa slide mã #" . $targetId . " thành công!";
            // Sử dụng tham số 'message' giống mẫu user_delete.php
            header("Location: slideshow.php?message=" . urlencode($message));
            exit();
        } else {
            header("Location: slideshow.php?error=" . urlencode("Lỗi khi xóa CSDL: " . $conn->error));
            exit();
        }

    } else {
        header("Location: slideshow.php?error=" . urlencode("Slide không tồn tại hoặc đã bị xóa."));
        exit();
    }
    
} else {
    // Không có ID, chuyển hướng về trang danh sách
    header("Location: slideshow.php");
    exit();
}
?>