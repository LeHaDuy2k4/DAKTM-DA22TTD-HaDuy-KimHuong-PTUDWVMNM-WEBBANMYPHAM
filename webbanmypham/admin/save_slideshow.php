<?php
session_start();
require("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action_type = $_POST['action_type'];
    $tieuDe = $conn->real_escape_string($_POST['tieuDe']);
    $linkAnh = $conn->real_escape_string($_POST['linkAnh']);
    $thuTu = (int)$_POST['thuTu'];
    $trangThai = (int)$_POST['trangThai'];
    
    $hinhAnh = "";

    // 1. XỬ LÝ UPLOAD ẢNH MỚI
    if (isset($_FILES['hinhAnh']) && $_FILES['hinhAnh']['error'] == 0) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["hinhAnh"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
        if (in_array(strtolower($fileType), $allowTypes)) {
            if (move_uploaded_file($_FILES["hinhAnh"]["tmp_name"], $targetFilePath)) {
                $hinhAnh = $fileName;
                
                // Nếu sửa và có ảnh cũ, thực hiện xóa ảnh cũ để sạch server
                if ($action_type == 'edit' && !empty($_POST['hinhAnh_old'])) {
                    $oldFile = "../uploads/" . $_POST['hinhAnh_old'];
                    if (file_exists($oldFile)) { unlink($oldFile); }
                }
            }
        }
    }

    // 2. THỰC THI SQL CHO BẢNG: trinhchieu
    if ($action_type == 'add') {
        if (empty($hinhAnh)) { die("Lỗi: Vui lòng chọn ảnh."); }
        $sql = "INSERT INTO trinhchieu (tieuDe, hinhAnh, linkAnh, thuTu, trangThai) 
                VALUES ('$tieuDe', '$hinhAnh', '$linkAnh', $thuTu, $trangThai)";
    } 
    elseif ($action_type == 'edit') {
        $maTC = (int)$_POST['maTC'];
        // Nếu không chọn ảnh mới, dùng lại tên ảnh cũ
        if (empty($hinhAnh)) {
            $hinhAnh = $conn->real_escape_string($_POST['hinhAnh_old']);
        }

        $sql = "UPDATE trinhchieu 
                SET tieuDe='$tieuDe', hinhAnh='$hinhAnh', linkAnh='$linkAnh', thuTu=$thuTu, trangThai=$trangThai 
                WHERE maTC=$maTC";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: slideshow.php?success=1");
        exit();
    } else {
        echo "Lỗi SQL: " . $conn->error;
    }
}
$conn->close();
?>