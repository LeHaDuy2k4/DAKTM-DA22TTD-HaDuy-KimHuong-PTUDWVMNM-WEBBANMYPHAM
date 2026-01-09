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

// Kiểm tra xem ID (maTC) có được gửi qua URL không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Ép kiểu ID sang số nguyên để bảo mật
    $targetId = (int)$conn->real_escape_string($_GET['id']);
    
    // --- BƯỚC 1: LẤY TÊN FILE ẢNH TRƯỚC KHI XOÁ DỮ LIỆU ---
    // Mục đích: Để xóa file ảnh thật trong thư mục uploads
    $get_img_sql = "SELECT hinhAnh FROM trinhchieu WHERE maTC = $targetId";
    $result = $conn->query($get_img_sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileName = $row['hinhAnh'];
        $filePath = "../uploads/" . $fileName;

        // --- BƯỚC 2: THỰC HIỆN XOÁ TRONG CƠ SỞ DỮ LIỆU ---
        $delete_sql = "DELETE FROM trinhchieu WHERE maTC = $targetId";
        
        if ($conn->query($delete_sql) === TRUE) {
            
            // --- BƯỚC 3: XOÁ FILE ẢNH VẬT LÝ TRÊN SERVER (Nếu tồn tại) ---
            if (!empty($fileName) && file_exists($filePath)) {
                unlink($filePath); // Hàm xóa file của PHP
            }

            $message = "Đã xóa slide mã số #" . $targetId . " thành công!";
            header("Location: slideshow.php?success=" . urlencode($message));
            exit();
        } else {
            header("Location: slideshow.php?error=" . urlencode("Lỗi khi xóa CSDL: " . $conn->error));
            exit();
        }

    } else {
        header("Location: slideshow.php?error=" . urlencode("Slide không tồn tại hoặc đã bị xóa trước đó."));
        exit();
    }
    
} else {
    // Không có ID, chuyển hướng về trang danh sách
    header("Location: slideshow.php");
    exit();
}
?>