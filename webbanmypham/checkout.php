<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require("header.php");

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php?error=" . urlencode("Vui lòng đăng nhập để thanh toán!"));
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];

// 2. LẤY GIỎ HÀNG HIỆN TẠI (Để hiển thị tóm tắt đơn hàng)
$sql_cart = "SELECT ct.*, m.tenMH, m.hinhAnh 
             FROM chitietgiohang ct
             JOIN giohang g ON ct.giohang_id = g.id
             JOIN mathang m ON ct.maMH = m.maMH
             WHERE g.tenDangNhap = '$tenDangNhap' AND g.trangthai = 0";
$res_cart = $conn->query($sql_cart);

if ($res_cart->num_rows == 0) {
    echo "<div style='padding:100px; text-align:center;'><h3>Giỏ hàng trống!</h3><a href='products.php'>Mua sắm ngay</a></div>";
    require("footer.php");
    exit();
}

// 3. XỬ LÝ LÚC NHẤN "ĐẶT HÀNG"
if (isset($_POST['btn_order'])) {
    $hoTen = $conn->real_escape_string($_POST['hoTen']);
    $sdt = $conn->real_escape_string($_POST['sdt']);
    $diaChi = $conn->real_escape_string($_POST['diaChi']);
    $ghiChu = $conn->real_escape_string($_POST['ghiChu']);
    
    // Tính tổng tiền đơn hàng
    $total_bill = 0;
    $items = [];
    while ($row = $res_cart->fetch_assoc()) {
        $total_bill += $row['soluong'] * $row['dongia'];
        $items[] = $row;
    }

    // Bắt đầu giao dịch (Transaction)
    $conn->begin_transaction();
    try {
        // Bước A: Thêm vào bảng dondathang
        $sql_order = "INSERT INTO dondathang (tenDangnhap, ngayDat, tongTien, trangthai) 
                      VALUES ('$tenDangNhap', NOW(), $total_bill, 'Chờ xử lý')";
        $conn->query($sql_order);
        $new_order_id = $conn->insert_id;

        // Bước B: Thêm vào bảng chitietdathang
        foreach ($items as $item) {
            $maMH = $item['maMH'];
            $sl = $item['soluong'];
            $gia = $item['dongia'];
            
            $sql_detail = "INSERT INTO chitietdathang (maDonhang, maMH, soLuong, giaBan, hoTenNguoiNhan, sdtNguoiNhan, diaChiGiaoHang, ghiChu) 
                           VALUES ($new_order_id, $maMH, $sl, $gia, '$hoTen', '$sdt', '$diaChi', '$ghiChu')";
            $conn->query($sql_detail);
            
            // Trừ tồn kho
            $conn->query("UPDATE mathang SET soluongTon = soluongTon - $sl WHERE maMH = $maMH");
        }

        // Bước C: Đánh dấu giỏ hàng đã thanh toán xong
        $conn->query("UPDATE giohang SET trangthai = 1 WHERE tenDangNhap = '$tenDangNhap' AND trangthai = 0");

        $conn->commit();
        echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php';</script>";
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<style>
    .checkout-wrapper { max-width: 1200px; margin: 40px auto; display: flex; gap: 30px; padding: 0 20px; }
    .form-section { flex: 2; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .summary-section { flex: 1; background: #fff8fb; padding: 30px; border-radius: 12px; border: 1px solid #ffd1dc; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #E91E63; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }
    .btn-submit { background: #E91E63; color: #fff; border: none; padding: 15px 30px; width: 100%; border-radius: 30px; font-weight: 700; cursor: pointer; }
</style>

<div class="checkout-wrapper">
    <div class="form-section">
        <h2 style="color: #E91E63; margin-bottom: 20px;">Thông tin nhận hàng</h2>
        <form method="POST">
            <div class="form-group">
                <label>Họ tên người nhận *</label>
                <input type="text" name="hoTen" required placeholder="Ví dụ: Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label>Số điện thoại *</label>
                <input type="text" name="sdt" required placeholder="Số điện thoại liên hệ">
            </div>
            <div class="form-group">
                <label>Địa chỉ giao hàng *</label>
                <textarea name="diaChi" required rows="3" placeholder="Số nhà, tên đường, phường/xã..."></textarea>
            </div>
            <div class="form-group">
                <label>Ghi chú (nếu có)</label>
                <textarea name="ghiChu" rows="2" placeholder="Lưu ý cho shipper..."></textarea>
            </div>
            <button type="submit" name="btn_order" class="btn-submit">XÁC NHẬN ĐẶT HÀNG</button>
        </form>
    </div>

    <div class="summary-section">
        <h3 style="color: #E91E63; border-bottom: 2px solid #ffd1dc; padding-bottom: 10px;">Đơn hàng của bạn</h3>
        <?php 
        $total = 0;
        $res_cart->data_seek(0);
        while($item = $res_cart->fetch_assoc()): 
            $sub = $item['soluong'] * $item['dongia'];
            $total += $sub;
        ?>
            <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 0.9rem;">
                <span><?php echo $item['tenMH']; ?> (x<?php echo $item['soluong']; ?>)</span>
                <span style="font-weight: 600;"><?php echo number_format($sub, 0, ',', '.'); ?>₫</span>
            </div>
        <?php endwhile; ?>
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px dashed #ffd1dc; display: flex; justify-content: space-between; font-size: 1.2rem; color: #E91E63; font-weight: 700;">
            <span>Tổng cộng:</span>
            <span><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
        </div>
    </div>
</div>

<?php require("footer.php"); ?>