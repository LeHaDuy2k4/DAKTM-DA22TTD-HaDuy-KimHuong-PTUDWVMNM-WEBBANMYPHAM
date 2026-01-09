<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("config.php");

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php?error=" . urlencode("Vui lòng đăng nhập để mua hàng!"));
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];
$today = date('Y-m-d');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $maMH = $conn->real_escape_string($_GET['id']);
    $soLuongThem = isset($_POST['soLuong']) ? (int)$_POST['soLuong'] : 1;
    if ($soLuongThem <= 0) $soLuongThem = 1;

    // --- BƯỚC A: LẤY THÔNG TIN TỒN KHO & GIÁ ---
    $sql_product = "SELECT m.tenMH, m.DonGia, m.soLuongTon, km.phantramgiam, km.ngayBD, km.ngayKT 
                    FROM mathang m 
                    LEFT JOIN khuyenmai km ON m.maKM = km.maKM 
                    WHERE m.maMH = '$maMH' LIMIT 1";
    
    $res_product = $conn->query($sql_product);
    if ($res_product && $res_product->num_rows > 0) {
        $product = $res_product->fetch_assoc();
        $tonKho = (int)$product['soLuongTon'];
        
        // Kiểm tra ngay lập tức: Nếu kho đã bằng 0
        if ($tonKho <= 0) {
            header("Location: product_detail.php?id=$maMH&error=" . urlencode("Sản phẩm hiện đã hết hàng!"));
            exit();
        }

        // Kiểm tra số lượng yêu cầu có vượt kho không
        if ($soLuongThem > $tonKho) {
            header("Location: product_detail.php?id=$maMH&error=" . urlencode("Chỉ còn $tonKho sản phẩm trong kho."));
            exit();
        }

        // Tính giá thực tế
        $donGiaThucTe = $product['DonGia'];
        if ($product['phantramgiam'] && $today >= $product['ngayBD'] && $today <= $product['ngayKT']) {
            $donGiaThucTe = $product['DonGia'] - ($product['DonGia'] * ($product['phantramgiam'] / 100));
        }
    } else {
        die("Sản phẩm không tồn tại.");
    }

    // --- BƯỚC B: LẤY GIỎ HÀNG ---
    $id_giohang_cha = 0;
    $res_cart = $conn->query("SELECT id FROM giohang WHERE tenDangNhap = '$tenDangNhap' AND trangthai = 0 LIMIT 1");
    if ($res_cart->num_rows > 0) {
        $id_giohang_cha = $res_cart->fetch_assoc()['id'];
    } else {
        $conn->query("INSERT INTO giohang (tenDangNhap, ngaytao, trangthai) VALUES ('$tenDangNhap', NOW(), 0)");
        $id_giohang_cha = $conn->insert_id;
    }

    // --- BƯỚC C: KIỂM TRA GIỚI HẠN KHI CỘNG DỒN ---
    $res_item = $conn->query("SELECT id, soluong FROM chitietgiohang WHERE giohang_id = $id_giohang_cha AND maMH = '$maMH' LIMIT 1");

    if ($res_item->num_rows > 0) {
        $item = $res_item->fetch_assoc();
        $trongGio = (int)$item['soluong'];
        $tongMoi = $trongGio + $soLuongThem;

        // Nếu tổng sau khi thêm vượt quá kho
        if ($tongMoi > $tonKho) {
            $conLai = $tonKho - $trongGio;
            if ($conLai <= 0) {
                $msg = "Bạn đã thêm tối đa số lượng có sẵn trong kho ($tonKho) vào giỏ hàng.";
            } else {
                $msg = "Bạn đã có $trongGio sản phẩm trong giỏ. Chỉ có thể thêm tối đa $conLai sản phẩm nữa.";
            }
            // Quay lại trang detail để hiện thông báo lỗi ngay tại đó
            header("Location: product_detail.php?id=$maMH&error=" . urlencode($msg));
            exit();
        }

        $conn->query("UPDATE chitietgiohang SET soluong = $tongMoi, dongia = $donGiaThucTe WHERE id = " . $item['id']);
    } else {
        $conn->query("INSERT INTO chitietgiohang (giohang_id, maMH, soluong, dongia) VALUES ($id_giohang_cha, '$maMH', $soLuongThem, $donGiaThucTe)");
    }

    header("Location: cart.php?success=" . urlencode("Đã thêm vào giỏ hàng!"));
    exit();
}