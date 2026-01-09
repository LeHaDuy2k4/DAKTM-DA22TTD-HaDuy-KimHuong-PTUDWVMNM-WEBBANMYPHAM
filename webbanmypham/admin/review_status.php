<?php
session_start();
require("../config.php");

// 1. KIỂM TRA QUYỀN ADMIN
// Đảm bảo chỉ Admin (quyen = 1) mới có thể thay đổi trạng thái hiển thị
if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: ../login.php");
    exit();
}

// 2. LẤY MÃ ĐÁNH GIÁ CẦN XỬ LÝ
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $maDG = (int)$_GET['id'];

    // 3. TRUY VẤN TRẠNG THÁI HIỆN TẠI
    $sql_check = "SELECT trangthai FROM danhgia WHERE maDG = $maDG";
    $result = $conn->query($sql_check);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Đảo ngược trạng thái: Nếu đang là 1 (Hiện) thì thành 0 (Ẩn) và ngược lại
        $newStatus = ($row['trangthai'] == 1) ? 0 : 1;

        // 4. CẬP NHẬT TRẠNG THÁI MỚI VÀO DATABASE
        $sql_update = "UPDATE danhgia SET trangthai = $newStatus WHERE maDG = $maDG";

        if ($conn->query($sql_update)) {
            // Cập nhật thành công, quay về trang danh sách
            header("Location: reviews.php");
            exit();
        } else {
            echo "Lỗi cập nhật: " . $conn->error;
        }
    } else {
        echo "Không tìm thấy đánh giá tương ứng.";
    }
} else {
    // Nếu không có ID truyền vào, đẩy về trang quản lý
    header("Location: reviews.php");
    exit();
}

$conn->close();
?>