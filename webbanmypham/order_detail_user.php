<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require("header.php");

// 1. KIỂM TRA ĐĂNG NHẬP & MÃ ĐƠN HÀNG
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php");
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];
$maDonhang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. TRUY VẤN THÔNG TIN TỔNG QUAN ĐƠN HÀNG
$sql_order = "SELECT * FROM dondathang 
              WHERE maDonhang = $maDonhang AND tenDangnhap = '$tenDangNhap' LIMIT 1";
$res_order = $conn->query($sql_order);
$orderInfo = $res_order->fetch_assoc();

if (!$orderInfo) {
    echo "<div class='container' style='padding:100px; text-align:center;'><h3>Đơn hàng không tồn tại hoặc bạn không có quyền xem.</h3><a href='order_history.php'>Quay lại lịch sử mua hàng</a></div>";
    require("footer.php");
    exit();
}

// 3. TRUY VẤN DANH SÁCH SẢN PHẨM TRONG ĐƠN
$sql_items = "SELECT ct.*, m.tenMH, m.hinhAnh 
              FROM chitietdathang ct 
              JOIN mathang m ON ct.maMH = m.maMH 
              WHERE ct.maDonhang = $maDonhang";
$res_items = $conn->query($sql_items);
$items = [];
while ($row = $res_items->fetch_assoc()) {
    $items[] = $row;
}
?>

<style>
    :root { --pink-primary: #E91E63; --pink-light: #fff0f5; }
    .detail-container { max-width: 900px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', sans-serif; }
    .back-link { color: #888; text-decoration: none; font-size: 0.9rem; margin-bottom: 20px; display: inline-block; transition: 0.3s; }
    .back-link:hover { color: var(--pink-primary); }
    
    .panel { background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 25px; border: 1px solid #f0f0f0; }
    .panel-title { color: var(--pink-primary); font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
    
    /* Thông tin vận chuyển */
    .delivery-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-label { color: #999; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 5px; font-weight: 600; }
    .info-content { color: #333; font-weight: 600; line-height: 1.5; }

    /* Bảng sản phẩm */
    .item-table { width: 100%; border-collapse: collapse; }
    .item-table th { text-align: left; padding: 12px; color: #999; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #eee; }
    .item-table td { padding: 15px 12px; border-bottom: 1px solid #fafafa; vertical-align: middle; }
    .product-cell { display: flex; align-items: center; gap: 15px; }
    .product-img { width: 70px; height: 70px; object-fit: contain; border-radius: 8px; border: 1px solid #f5f5f5; background: #fff; }
    
    .price-text { font-weight: 700; color: #333; }
    .total-row { display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 2px dashed #eee; }
    .total-amount { font-size: 1.6rem; color: var(--pink-primary); font-weight: 800; }
</style>

<div class="detail-container">
    <a href="order_history.php" class="back-link"><i class="fa-solid fa-chevron-left"></i> Quay lại lịch sử đơn hàng</a>
    
    <div class="panel">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <h2 class="panel-title" style="border:none; margin:0;">CHI TIẾT ĐƠN HÀNG #<?php echo str_pad($orderInfo['maDonhang'], 5, '0', STR_PAD_LEFT); ?></h2>
            <span style="font-weight: 700; color: var(--pink-primary); background: var(--pink-light); padding: 5px 15px; border-radius: 20px; font-size: 0.9rem;">
                <?php echo strtoupper($orderInfo['trangthai']); ?>
            </span>
        </div>
        <p style="color: #999; font-size: 0.85rem; margin-top: 5px;">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($orderInfo['ngayDat'])); ?></p>
        
        <div class="delivery-grid" style="margin-top: 25px;">
            <div>
                <div class="info-label"><i class="fa-solid fa-user"></i> Người nhận hàng</div>
                <div class="info-content"><?php echo htmlspecialchars($items[0]['hoTenNguoiNhan']); ?></div>
                <div style="color:#666; font-size: 0.9rem; margin-top:3px;"><i class="fa-solid fa-phone" style="font-size: 10px;"></i> <?php echo htmlspecialchars($items[0]['sdtNguoiNhan']); ?></div>
            </div>
            <div>
                <div class="info-label"><i class="fa-solid fa-location-dot"></i> Địa chỉ giao hàng</div>
                <div class="info-content"><?php echo htmlspecialchars($items[0]['diaChiGiaoHang']); ?></div>
            </div>
        </div>

        <?php if(!empty($items[0]['ghiChu'])): ?>
            <div style="margin-top: 20px; padding: 12px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #ddd;">
                <div class="info-label" style="margin:0; font-size: 0.75rem;">Ghi chú từ khách hàng:</div>
                <div style="font-size: 0.9rem; color: #555; font-style: italic;"><?php echo htmlspecialchars($items[0]['ghiChu']); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2 class="panel-title"><i class="fa-solid fa-box-open"></i> Danh sách mặt hàng</h2>
        <table class="item-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Đơn giá</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $subtotal = $item['giaBan'] * $item['soLuong'];
                ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="<?php echo htmlspecialchars($item['hinhAnh']); ?>" class="product-img" onerror="this.src='https://via.placeholder.com/70?text=Product'">
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600; color: #444;"><?php echo htmlspecialchars($item['tenMH']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;" class="price-text"><?php echo number_format($item['giaBan'], 0, ',', '.'); ?>₫</td>
                    <td style="text-align: center;">x<?php echo $item['soLuong']; ?></td>
                    <td style="text-align: right;" class="price-text"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-row">
            <div style="text-align: right;">
                <div style="color: #999; font-size: 0.9rem; margin-bottom: 5px; font-weight: 600;">TỔNG THANH TOÁN</div>
                <div class="total-amount"><?php echo number_format($orderInfo['tongTien'], 0, ',', '.'); ?> VNĐ</div>
            </div>
        </div>
    </div>
</div>

<?php require("footer.php"); ?>