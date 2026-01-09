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

// 2. TRUY VẤN DANH SÁCH ĐÁNH GIÁ ĐƠN HÀNG CỦA USER
// Chúng ta ưu tiên lấy mã đơn hàng. Nếu đánh giá cho cả đơn (maMH = 0) hoặc từng món, mã đơn vẫn là định danh chính.
$sql_reviews = "SELECT dg.*, d.tongTien, d.ngayDat
                FROM danhgia dg
                JOIN dondathang d ON dg.maDonhang = d.maDonhang
                WHERE dg.tenDangnhap = '$tenDangNhap'
                ORDER BY dg.ngayDG DESC";
$result_reviews = $conn->query($sql_reviews);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    :root { --primary-pink: #E91E63; --star-color: #FFC107; --bg-light: #fff8fb; }
    body { background-color: var(--bg-light); }
    .review-history-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
    .history-title { color: var(--primary-pink); font-weight: 700; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; text-transform: uppercase; }
    
    .review-card { background: #fff; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 20px; border: 1px solid #f0f0f0; }
    
    .order-info-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #eee; padding-bottom: 15px; margin-bottom: 15px; }
    .order-id { font-family: monospace; font-size: 1.2rem; font-weight: 800; color: #333; }
    .order-date { font-size: 0.85rem; color: #888; }
    
    .star-rating { color: var(--star-color); margin-bottom: 12px; font-size: 1.1rem; }
    .review-text { color: #444; line-height: 1.6; font-style: italic; background: #f9f9f9; padding: 15px; border-radius: 10px; border-left: 4px solid var(--primary-pink); margin-bottom: 15px; }
    
    .review-footer { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; }
    .sent-date { color: #999; }
    .status-tag { padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .status-show { background: #e8f5e9; color: #2e7d32; }
    .status-hide { background: #ffebee; color: #c62828; }

    .btn-revisit { text-decoration: none; color: var(--primary-pink); font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
    .btn-revisit:hover { text-decoration: underline; }
    .empty-state { text-align: center; padding: 80px 0; color: #bbb; }
</style>

<div class="review-history-container">
    <h2 class="history-title"><i class="fa-solid fa-receipt"></i> LỊCH SỬ ĐÁNH GIÁ ĐƠN HÀNG</h2>

    <?php if ($result_reviews && $result_reviews->num_rows > 0): ?>
        <?php while($row = $result_reviews->fetch_assoc()): ?>
            <div class="review-card">
                <div class="order-info-header">
                    <div>
                        <span class="order-id">MÃ ĐƠN: #<?php echo str_pad($row['maDonhang'], 5, '0', STR_PAD_LEFT); ?></span>
                        <div class="order-date">Ngày mua: <?php echo date('d/m/Y', strtotime($row['ngayDat'])); ?></div>
                    </div>
                    <div style="text-align: right;">
                        <a href="order_detail_user.php?id=<?php echo $row['maDonhang']; ?>" class="btn-revisit">Xem lại đơn hàng <i class="fa-solid fa-arrow-right-long"></i></a>
                    </div>
                </div>

                <div class="star-rating">
                    <span>Mức độ hài lòng: </span>
                    <?php 
                    for($i=1; $i<=5; $i++) {
                        echo ($i <= $row['soSao']) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                    }
                    ?>
                </div>

                <div class="review-text">
                    "<?php echo htmlspecialchars($row['noiDung']); ?>"
                </div>

                <div class="review-footer">
                    <span class="sent-date"><i class="fa-regular fa-clock"></i> Đã gửi đánh giá lúc: <?php echo date('d/m/Y H:i', strtotime($row['ngayDG'])); ?></span>
                    
                    <?php if($row['trangthai'] == 1): ?>
                        <span class="status-tag status-show"><i class="fa-solid fa-check"></i> Đang hiển thị</span>
                    <?php else: ?>
                        <span class="status-tag status-hide"><i class="fa-solid fa-eye-slash"></i> Đã ẩn bởi Admin</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-clipboard-list" style="font-size: 4rem; margin-bottom: 20px; display: block;"></i>
            <p style="font-size: 1.1rem; color: #999;">Bạn chưa gửi đánh giá nào cho các đơn hàng đã mua.</p>
            <a href="order_history.php" style="color: var(--primary-pink); font-weight: 700; text-decoration: none; margin-top: 15px; display: inline-block;">ĐẾN LỊCH SỬ MUA HÀNG ĐỂ ĐÁNH GIÁ &rarr;</a>
        </div>
    <?php endif; ?>
</div>

<?php require("footer.php"); ?>