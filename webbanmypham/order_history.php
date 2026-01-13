<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require("header.php");

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php");
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];
$message = "";

// 2. XỬ LÝ HỦY ĐƠN HÀNG (Chỉ xử lý hủy, phần trả hàng đã chuyển sang trang riêng)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btn_cancel'])) {
        $maDonhangHuy = (int)$_POST['maDonhang'];
        $sql_cancel = "UPDATE dondathang 
                       SET trangthai = 'Đã hủy' 
                       WHERE maDonhang = $maDonhangHuy 
                       AND tenDangnhap = '$tenDangNhap' 
                       AND (trangthai = 'Chờ xử lý' OR trangthai = '0')";
        
        if ($conn->query($sql_cancel) && $conn->affected_rows > 0) {
            $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Đã hủy đơn hàng #$maDonhangHuy thành công!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Không thể hủy đơn hàng này.</div>";
        }
    }
}

// 3. TRUY VẤN DANH SÁCH ĐƠN HÀNG
$sql_orders = "SELECT DISTINCT d.maDonhang, d.ngayDat, d.tongTien, d.trangthai, 
                               ct.hoTenNguoiNhan, ct.diaChiGiaoHang, ct.sdtNguoiNhan
               FROM dondathang d
               LEFT JOIN chitietdathang ct ON d.maDonhang = ct.maDonhang
               WHERE d.tenDangnhap = '$tenDangNhap' 
               ORDER BY d.ngayDat DESC";
$result_orders = $conn->query($sql_orders);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    :root { 
        --primary-pink: #E91E63; 
        --bg-light: #fcfcfc;
        --accent-orange: #FF9800; 
        --accent-purple: #9C27B0; /* Màu cho nút/trạng thái Trả hàng */
    }
    
    body { background-color: var(--bg-light); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .order-history-container { max-width: 950px; margin: 40px auto; padding: 0 20px; }
    .history-title { color: var(--primary-pink); font-weight: 700; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; text-transform: uppercase; letter-spacing: 1px; }
    
    /* Card đơn hàng */
    .order-card { background: #fff; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; border: 1px solid #f0f0f0; transition: transform 0.3s ease; }
    .order-card:hover { transform: translateY(-3px); }
    .order-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f5f5f5; padding-bottom: 15px; margin-bottom: 18px; }
    .order-id { font-weight: 800; color: #333; font-family: monospace; font-size: 1.2rem; }
    
    /* Badge trạng thái */
    .status-badge { padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-shipping { background: #d1ecf1; color: #0c5460; }
    .status-return { background: #f3e5f5; color: #7b1fa2; border: 1px solid #e1bee7; } /* Style tím cho trả hàng */

    .order-grid { display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: flex-end; }
    .delivery-info { font-size: 0.9rem; color: #666; line-height: 1.7; }
    .delivery-info b { color: #333; }
    .delivery-info i { color: var(--primary-pink); width: 20px; }
    .order-price { color: var(--primary-pink); font-size: 1.5rem; font-weight: 800; margin-top: 5px; }
    
    /* Nhóm nút bấm */
    .action-group { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; flex-wrap: wrap; }
    .btn-action { padding: 8px 18px; border-radius: 30px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: none; outline: none; }

    .btn-detail { background: var(--primary-pink); color: #fff; }
    .btn-detail:hover { background: #c2185b; box-shadow: 0 4px 12px rgba(233, 30, 99, 0.2); }

    .btn-review { background: var(--accent-orange); color: #fff; }
    .btn-review:hover { background: #e68a00; box-shadow: 0 4px 12px rgba(255, 152, 0, 0.2); }

    .btn-cancel { background: #fff; color: #dc3545; border: 1.5px solid #dc3545; }
    .btn-cancel:hover { background: #dc3545; color: #fff; }

    /* Style nút Trả hàng */
    .btn-return { background: #fff; color: var(--accent-purple); border: 1.5px solid var(--accent-purple); }
    .btn-return:hover { background: var(--accent-purple); color: #fff; box-shadow: 0 4px 12px rgba(156, 39, 176, 0.2); }
    
    .alert { padding: 15px; border-radius: 10px; margin-bottom: 25px; border: none; font-weight: 600; text-align: center; }
    .alert-success { background: #d4edda; color: #155724; }
    .alert-danger { background: #f8d7da; color: #721c24; }
</style>

<div class="order-history-container">
    <h2 class="history-title"><i class="fa-solid fa-clock-rotate-left"></i> Lịch Sử Mua Hàng</h2>
    
    <?php echo $message; ?>

    <?php if ($result_orders && $result_orders->num_rows > 0): ?>
        <?php while($order = $result_orders->fetch_assoc()): 
            $st = mb_strtolower($order['trangthai']);
            
            // Logic xác định class CSS cho badge trạng thái
            $statusClass = 'status-pending';
            if (strpos($st, 'hủy') !== false) $statusClass = 'status-cancelled';
            elseif (strpos($st, 'hoàn') !== false || strpos($st, 'đã giao') !== false) $statusClass = 'status-completed';
            elseif (strpos($st, 'đang') !== false) $statusClass = 'status-shipping';
            elseif (strpos($st, 'trả') !== false) $statusClass = 'status-return';
        ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-id">ĐƠN HÀNG #<?php echo str_pad($order['maDonhang'], 5, '0', STR_PAD_LEFT); ?></span>
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($order['trangthai']); ?>
                    </span>
                </div>
                
                <div class="order-grid">
                    <div class="delivery-info">
                        <p><i class="fa-solid fa-calendar-check"></i> Ngày đặt: <b><?php echo date('d/m/Y H:i', strtotime($order['ngayDat'])); ?></b></p>
                        <p><i class="fa-solid fa-user-tag"></i> Người nhận: <b><?php echo htmlspecialchars($order['hoTenNguoiNhan']); ?></b></p>
                        <p><i class="fa-solid fa-map-location-dot"></i> Địa chỉ: <span><?php echo htmlspecialchars($order['diaChiGiaoHang']); ?></span></p>
                        <p><i class="fa-solid fa-phone-volume"></i> Hotline: <span><?php echo htmlspecialchars($order['sdtNguoiNhan']); ?></span></p>
                    </div>
                    
                    <div style="text-align: right;">
                        <div style="font-size: 0.85rem; color: #999; font-weight: 600;">TỔNG THANH TOÁN</div>
                        <div class="order-price"><?php echo number_format($order['tongTien'], 0, ',', '.'); ?>₫</div>
                        
                        <div class="action-group">
                            <a href="order_detail_user.php?id=<?php echo $order['maDonhang']; ?>" class="btn-action btn-detail">
                                <i class="fa-solid fa-eye"></i> CHI TIẾT
                            </a>

                            <?php if (strpos($st, 'hoàn thành') !== false || strpos($st, 'đã giao') !== false): ?>
                                
                                <a href="user_reviews.php?order=<?php echo $order['maDonhang']; ?>" class="btn-action btn-review">
                                    <i class="fa-solid fa-star"></i> ĐÁNH GIÁ
                                </a>
                                
                                <a href="request_return.php?id=<?php echo $order['maDonhang']; ?>" class="btn-action btn-return">
                                    <i class="fa-solid fa-rotate-left"></i> TRẢ HÀNG
                                </a>

                            <?php endif; ?>

                            <?php if ($order['trangthai'] == 'Chờ xử lý' || $order['trangthai'] == '0'): ?>
                                <form method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');" style="margin:0;">
                                    <input type="hidden" name="maDonhang" value="<?php echo $order['maDonhang']; ?>">
                                    <button type="submit" name="btn_cancel" class="btn-action btn-cancel">
                                        <i class="fa-solid fa-ban"></i> HỦY ĐƠN
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 0; background: #fff; border-radius: 15px;">
            <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #eee; margin-bottom: 15px;"></i>
            <p style="color: #999;">Bạn chưa có đơn hàng nào.</p>
            <a href="products.php" style="color: var(--primary-pink); font-weight: 700; text-decoration:none;">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php require("footer.php"); ?>