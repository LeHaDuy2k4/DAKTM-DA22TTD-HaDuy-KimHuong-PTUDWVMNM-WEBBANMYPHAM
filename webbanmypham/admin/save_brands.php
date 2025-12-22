<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
// Yêu cầu file config.php (đặt ở thư mục gốc) để có kết nối $conn
require("../config.php"); 

if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    // Chuyển hướng về trang danh sách thương hiệu nếu không phải Admin
    header("Location: brands.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Kiểm tra phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ====================================================================
    // 1. LẤY VÀ LÀM SẠCH DỮ LIỆU
    // ====================================================================
    
    $action_type = $_POST['action_type'] ?? '';

    // Lấy dữ liệu và làm sạch bằng real_escape_string
    $maTH = $conn->real_escape_string($_POST['maTH'] ?? ''); 
    $tenTH = $conn->real_escape_string($_POST['tenTH'] ?? '');
    $quocGia = $conn->real_escape_string($_POST['quocGia'] ?? ''); // Lấy dữ liệu Quốc Gia
    $moTa = $conn->real_escape_string($_POST['moTa'] ?? '');
    
    // ====================================================================
    // 2. XỬ LÝ THEO CHẾ ĐỘ (ADD/EDIT)
    // ====================================================================

    if ($action_type == 'add') {
        // --- CHẾ ĐỘ THÊM MỚI ---

        if (empty($tenTH) || empty($quocGia)) {
            die("Lỗi: Vui lòng điền đủ Tên Thương Hiệu và Quốc Gia.");
        }
        
        // 2.1. Kiểm tra Tên Thương Hiệu đã tồn tại chưa (Sử dụng bảng thuonghieu)
        $check_sql = "SELECT tenTH FROM thuonghieu WHERE tenTH = '$tenTH'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result && $check_result->num_rows > 0) {
            die("Lỗi: Tên thương hiệu **" . htmlspecialchars($tenTH) . "** đã tồn tại. Vui lòng chọn tên khác.");
        }

        // 2.2. Câu lệnh INSERT (Sử dụng bảng thuonghieu)
        $sql = "INSERT INTO thuonghieu (tenTH, quocGia, moTa) 
                VALUES ('$tenTH', '$quocGia', '$moTa')";
        
        $message_success = "Thêm thương hiệu **" . htmlspecialchars($tenTH) . "** thành công!";

    } elseif ($action_type == 'edit') {
        // --- CHẾ ĐỘ CHỈNH SỬA ---
        
        // Cần phải có Mã TH, Tên TH và Quốc Gia
        if (empty($maTH) || empty($tenTH) || empty($quocGia)) {
            die("Lỗi: Mã thương hiệu, Tên thương hiệu hoặc Quốc gia bị thiếu.");
        }
        
        // 2.1. Kiểm tra tên thương hiệu mới có trùng với tên thương hiệu khác (trừ chính nó) không
        $check_duplicate_sql = "SELECT maTH FROM thuonghieu WHERE tenTH = '$tenTH' AND maTH != '$maTH'";
        $duplicate_result = $conn->query($check_duplicate_sql);

        if ($duplicate_result && $duplicate_result->num_rows > 0) {
            die("Lỗi: Tên thương hiệu **" . htmlspecialchars($tenTH) . "** đã được sử dụng cho thương hiệu khác.");
        }

        $updateFields = [];
        
        // Thêm các trường cần cập nhật
        $updateFields[] = "tenTH = '$tenTH'";
        $updateFields[] = "quocGia = '$quocGia'";
        $updateFields[] = "moTa = '$moTa'";
        
        if (empty($updateFields)) {
            $message = "Không có thông tin nào được thay đổi.";
            header("Location: brands.php?message=" . urlencode($message));
            exit();
        }

        $setClause = implode(", ", $updateFields);
        
        // 2.2. Câu lệnh UPDATE (Sử dụng bảng thuonghieu)
        $sql = "UPDATE thuonghieu SET $setClause WHERE maTH = '$maTH'";
        
        $message_success = "Cập nhật thương hiệu có mã **" . htmlspecialchars($maTH) . "** thành công!";

    } else {
        die("Hành động không hợp lệ.");
    }

    // ====================================================================
    // 3. THỰC THI SQL VÀ CHUYỂN HƯỚNG
    // ====================================================================

    if ($conn->query($sql) === TRUE) {
        // Chuyển hướng về trang danh sách thương hiệu với thông báo thành công
        header("Location: brands.php?message=" . urlencode($message_success));
        exit();
    } else {
        // Chuyển hướng về trang danh sách với thông báo lỗi
        header("Location: brands.php?error=" . urlencode("Lỗi thực thi CSDL: " . $conn->error));
        exit();
    }
} else {
    // Nếu không phải POST request, chuyển hướng
    header("Location: brands.php");
    exit();
}
?>