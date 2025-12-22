<?php 
// Tên file: admin/save_product.php (SỬ DỤNG PHƯƠNG PHÁP NỐI CHUỖI SQL)

// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền hạn và yêu cầu file kết nối database
require("../config.php"); 

if (!isset($_SESSION['quyen']) || $_SESSION['quyen'] != 1) {
    header("Location: products.php?error=" . urlencode("Bạn không có quyền thực hiện hành động này."));
    exit();
}

// Kiểm tra phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ====================================================================
    // 1. LẤY VÀ LÀM SẠCH DỮ LIỆU
    // ====================================================================
    
    $action_type = $_POST['action_type'] ?? '';

    // Lấy dữ liệu và làm sạch bằng real_escape_string
    $tenMH = $conn->real_escape_string(trim($_POST['tenMH'] ?? ''));
    $moTa = $conn->real_escape_string(trim($_POST['moTa'] ?? ''));
    $hinhAnh = $conn->real_escape_string(trim($_POST['hinhAnh'] ?? '')); // URL ảnh

    // Lấy dữ liệu số (Không cần real_escape_string, nhưng vẫn nên dùng floatval/intval)
    $DonGia = floatval($_POST['DonGia'] ?? 0);
    $soLuongTon = intval($_POST['soLuongTon'] ?? 0);
    $maDM = $conn->real_escape_string($_POST['maDM'] ?? '');
    $maTH = $conn->real_escape_string($_POST['maTH'] ?? '');
    $trangThai = (int)($_POST['trangThai'] ?? 0);
    
    // Dữ liệu chỉ dùng khi EDIT
    $maMH_old = $conn->real_escape_string($_POST['maMH_old'] ?? ''); 

    $sql = "";
    $message_success = "";
    $error = "";

    // ====================================================================
    // 2. XỬ LÝ THEO CHẾ ĐỘ (ADD/EDIT)
    // ====================================================================

    // --- KIỂM TRA DỮ LIỆU BẮT BUỘC ---
    if (empty($tenMH) || empty($maDM) || empty($maTH) || empty($hinhAnh) || $DonGia < 0 || $soLuongTon < 0) {
        header("Location: products.php?error=" . urlencode("Vui lòng điền đầy đủ thông tin sản phẩm bắt buộc."));
        exit();
    }


    if ($action_type == 'add') {
        // --- CHẾ ĐỘ THÊM MỚI ---

        // MaMH là AUTO_INCREMENT, không cần thêm vào INSERT list
        $sql = "INSERT INTO mathang (tenMH, DonGia, soLuongTon, hinhAnh, maDM, maTH, moTa, trangThai) 
                VALUES ('$tenMH', $DonGia, $soLuongTon, '$hinhAnh', '$maDM', '$maTH', '$moTa', $trangThai)";
        
        $message_success = "Thêm sản phẩm **" . htmlspecialchars($tenMH) . "** thành công!";

    } elseif ($action_type == 'edit' && !empty($maMH_old)) {
        // --- CHẾ ĐỘ CHỈNH SỬA ---

        // Chuẩn bị các trường cập nhật
        $updateFields = [];
        $updateFields[] = "tenMH = '$tenMH'";
        $updateFields[] = "DonGia = $DonGia";
        $updateFields[] = "soLuongTon = $soLuongTon";
        $updateFields[] = "hinhAnh = '$hinhAnh'";
        $updateFields[] = "maDM = '$maDM'";
        $updateFields[] = "maTH = '$maTH'";
        $updateFields[] = "moTa = '$moTa'";
        $updateFields[] = "trangThai = $trangThai";
        
        $setClause = implode(", ", $updateFields);
        
        $sql = "UPDATE mathang SET $setClause WHERE maMH = '$maMH_old'";
        
        $message_success = "Cập nhật sản phẩm **" . htmlspecialchars($tenMH) . "** thành công!";

    } else {
        header("Location: products.php?error=" . urlencode("Hành động hoặc mã mặt hàng không hợp lệ."));
        exit();
    }

    // ====================================================================
    // 3. THỰC THI SQL VÀ CHUYỂN HƯỚNG
    // ====================================================================

    if ($conn->query($sql) === TRUE) {
        // Chuyển hướng về trang danh sách sản phẩm với thông báo thành công
        header("Location: products.php?message=" . urlencode($message_success));
        exit();
    } else {
        // Chuyển hướng về trang danh sách với thông báo lỗi
        header("Location: products.php?error=" . urlencode("Lỗi thực thi CSDL: " . $conn->error));
        exit();
    }
} else {
    // Nếu không phải POST request, chuyển hướng
    header("Location: products.php");
    exit();
}
?>