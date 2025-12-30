<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn (chỉ Admin mới được truy cập file này)
// Yêu cầu file config.php (đặt ở thư mục gốc) để có kết nối $conn
require("../config.php"); 

if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: users.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Kiểm tra phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ====================================================================
    // 1. LẤY VÀ LÀM SẠCH DỮ LIỆU
    // ====================================================================
    
    $action_type = $_POST['action_type'] ?? '';

    // Lấy dữ liệu và làm sạch bằng real_escape_string
    $tenDangnhap = $conn->real_escape_string($_POST['tenDangnhap'] ?? '');
    $hoTen = $conn->real_escape_string($_POST['hoTen'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $dienThoai = $conn->real_escape_string($_POST['dienThoai'] ?? '');
    $diaChi = $conn->real_escape_string($_POST['diaChi'] ?? '');
    
    // Lấy dữ liệu radio button (số nguyên)
    $gioiTinh = (int)($_POST['gioiTinh'] ?? 0);
    $quyen = (int)($_POST['quyen'] ?? 0);
    $trangThai = (int)($_POST['trangThai'] ?? 0);
    
    // Mật khẩu (để trống nếu không đổi)
    $matKhau = $_POST['matKhau'] ?? '';
    $hashedPassword = '';
    
    // ====================================================================
    // 2. XỬ LÝ THEO CHẾ ĐỘ (ADD/EDIT)
    // ====================================================================

    if ($action_type == 'add') {
        // --- CHẾ ĐỘ THÊM MỚI ---

        if (empty($tenDangnhap) || empty($email) || empty($matKhau)) {
            die("Lỗi: Vui lòng điền đủ Tên đăng nhập, Email và Mật khẩu.");
        }
        
        // Kiểm tra Tên đăng nhập đã tồn tại chưa
        $check_sql = "SELECT tenDangnhap FROM nguoidung WHERE tenDangnhap = '$tenDangnhap'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result && $check_result->num_rows > 0) {
            die("Lỗi: Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.");
        }

        // Mã hóa mật khẩu (sử dụng MD5 theo cấu trúc bảng của bạn)
        $hashedPassword = md5($matKhau); 
        
        $sql = "INSERT INTO nguoidung (tenDangnhap, matKhau, hoTen, gioiTinh, email, quyen, trangThai, dienThoai, diaChi) 
                VALUES ('$tenDangnhap', '$hashedPassword', '$hoTen', $gioiTinh, '$email', $quyen, $trangThai, '$dienThoai', '$diaChi')";
        
        $message_success = "Thêm người dùng **" . htmlspecialchars($tenDangnhap) . "** thành công!";

    } elseif ($action_type == 'edit') {
        // --- CHẾ ĐỘ CHỈNH SỬA ---

        $tenDangnhap_old = $conn->real_escape_string($_POST['tenDangnhap_old'] ?? '');
        $updateFields = [];
        
        // 1. Thêm các trường cần cập nhật
        $updateFields[] = "hoTen = '$hoTen'";
        $updateFields[] = "email = '$email'";
        $updateFields[] = "dienThoai = '$dienThoai'";
        $updateFields[] = "diaChi = '$diaChi'";
        $updateFields[] = "gioiTinh = $gioiTinh";
        $updateFields[] = "quyen = $quyen";
        $updateFields[] = "trangThai = $trangThai";
        
        // 2. Xử lý Mật khẩu (Chỉ cập nhật nếu có nhập mật khẩu mới)
        if (!empty($matKhau)) {
            $hashedPassword = md5($matKhau);
            $updateFields[] = "matKhau = '$hashedPassword'";
        }
        
        if (empty($updateFields)) {
            $message = "Không có thông tin nào được thay đổi.";
            header("Location: users.php?message=" . urlencode($message));
            exit();
        }

        $setClause = implode(", ", $updateFields);
        
        $sql = "UPDATE nguoidung SET $setClause WHERE tenDangnhap = '$tenDangnhap_old'";
        
        $message_success = "Cập nhật người dùng **" . htmlspecialchars($tenDangnhap_old) . "** thành công!";

    } else {
        die("Hành động không hợp lệ.");
    }

    // ====================================================================
    // 3. THỰC THI SQL VÀ CHUYỂN HƯỚNG
    // ====================================================================

    if ($conn->query($sql) === TRUE) {
        // Chuyển hướng về trang danh sách người dùng với thông báo thành công
        header("Location: users.php?message=" . urlencode($message_success));
        exit();
    } else {
        // Chuyển hướng về trang danh sách với thông báo lỗi
        header("Location: users.php?error=" . urlencode("Lỗi thực thi CSDL: " . $conn->error));
        exit();
    }
} else {
    // Nếu không phải POST request, chuyển hướng
    header("Location: users.php");
    exit();
}
?>