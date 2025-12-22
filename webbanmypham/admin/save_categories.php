<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
// Yêu cầu file config.php (đặt ở thư mục gốc) để có kết nối $conn
require("../config.php"); 

if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    // Chuyển hướng về trang danh mục nếu không phải Admin
    header("Location: categories.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Kiểm tra phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ====================================================================
    // 1. LẤY VÀ LÀM SẠCH DỮ LIỆU
    // ====================================================================
    
    $action_type = $_POST['action_type'] ?? '';

    // Lấy dữ liệu và làm sạch bằng real_escape_string
    $maDM = $conn->real_escape_string($_POST['maDM'] ?? ''); 
    $tenDM = $conn->real_escape_string($_POST['tenDM'] ?? '');
    $moTa = $conn->real_escape_string($_POST['moTa'] ?? '');
    
    // ====================================================================
    // 2. XỬ LÝ THEO CHẾ ĐỘ (ADD/EDIT)
    // ====================================================================

    if ($action_type == 'add') {
        // --- CHẾ ĐỘ THÊM MỚI ---

        if (empty($tenDM)) {
            die("Lỗi: Vui lòng điền đủ Tên danh mục.");
        }
        
        // Kiểm tra Tên danh mục đã tồn tại chưa (Sử dụng bảng danhmucsp)
        $check_sql = "SELECT tenDM FROM danhmucsp WHERE tenDM = '$tenDM'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result && $check_result->num_rows > 0) {
            die("Lỗi: Tên danh mục **" . htmlspecialchars($tenDM) . "** đã tồn tại. Vui lòng chọn tên khác.");
        }

        // Câu lệnh INSERT (Sử dụng bảng danhmucsp)
        $sql = "INSERT INTO danhmucsp (tenDM, moTa) 
                VALUES ('$tenDM', '$moTa')";
        
        $message_success = "Thêm danh mục **" . htmlspecialchars($tenDM) . "** thành công!";

    } elseif ($action_type == 'edit') {
        // --- CHẾ ĐỘ CHỈNH SỬA ---
        
        // Cần phải có Mã danh mục và Tên danh mục
        if (empty($maDM) || empty($tenDM)) {
             die("Lỗi: Không tìm thấy Mã danh mục hoặc Tên danh mục bị thiếu.");
        }
        
        // 2.1. Kiểm tra tên danh mục mới có trùng với tên danh mục khác (trừ chính nó) không
        $check_duplicate_sql = "SELECT maDM FROM danhmucsp WHERE tenDM = '$tenDM' AND maDM != '$maDM'";
        $duplicate_result = $conn->query($check_duplicate_sql);

        if ($duplicate_result && $duplicate_result->num_rows > 0) {
            die("Lỗi: Tên danh mục **" . htmlspecialchars($tenDM) . "** đã được sử dụng cho danh mục khác.");
        }

        $updateFields = [];
        
        // Thêm các trường cần cập nhật
        $updateFields[] = "tenDM = '$tenDM'";
        $updateFields[] = "moTa = '$moTa'";
        
        if (empty($updateFields)) {
            $message = "Không có thông tin nào được thay đổi.";
            header("Location: categories.php?message=" . urlencode($message));
            exit();
        }

        $setClause = implode(", ", $updateFields);
        
        // Câu lệnh UPDATE (Sử dụng bảng danhmucsp)
        $sql = "UPDATE danhmucsp SET $setClause WHERE maDM = '$maDM'";
        
        $message_success = "Cập nhật danh mục có mã **" . htmlspecialchars($maDM) . "** thành công!";

    } else {
        die("Hành động không hợp lệ.");
    }

    // ====================================================================
    // 3. THỰC THI SQL VÀ CHUYỂN HƯỚNG
    // ====================================================================

    if ($conn->query($sql) === TRUE) {
        // Chuyển hướng về trang danh sách danh mục với thông báo thành công
        header("Location: categories.php?message=" . urlencode($message_success));
        exit();
    } else {
        // Chuyển hướng về trang danh sách với thông báo lỗi
        header("Location: categories.php?error=" . urlencode("Lỗi thực thi CSDL: " . $conn->error));
        exit();
    }
} else {
    // Nếu không phải POST request, chuyển hướng
    header("Location: categories.php");
    exit();
}
?>