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
$maDonhang = isset($_GET['order']) ? (int)$_GET['order'] : 0;

// 2. KIỂM TRA ĐƠN HÀNG VÀ LẤY MÃ SẢN PHẨM ĐẠI DIỆN
if ($maDonhang <= 0) {
    echo "<div class='container' style='padding:50px; text-align:center;'><h3>Mã đơn hàng không hợp lệ!</h3><a href='order_history.php'>Quay lại lịch sử</a></div>";
    exit();
}

// Lấy 1 mã mặt hàng từ đơn hàng này để thỏa mãn ràng buộc khóa phụ maMH
$sql_info = "SELECT ct.maMH, d.trangthai 
             FROM dondathang d 
             JOIN chitietdathang ct ON d.maDonhang = ct.maDonhang 
             WHERE d.maDonhang = $maDonhang AND d.tenDangnhap = '$tenDangNhap' 
             LIMIT 1";
$res_info = $conn->query($sql_info);
$order_data = $res_info->fetch_assoc();

if (!$order_data || mb_strtolower($order_data['trangthai']) !== 'đã hoàn thành') {
    echo "<div class='container' style='padding:50px; text-align:center;'><h3>Đơn hàng không tồn tại hoặc chưa hoàn thành!</h3></div>";
    exit();
}

$maMH_dai_dien = $order_data['maMH'];

// 3. KIỂM TRA XEM ĐƠN HÀNG NÀY ĐÃ ĐƯỢC ĐÁNH GIÁ CHƯA
$sql_check = "SELECT maDG FROM danhgia WHERE tenDangnhap = '$tenDangNhap' AND maDonhang = $maDonhang";
$res_check = $conn->query($sql_check);

if ($res_check && $res_check->num_rows > 0) {
    echo "<script>alert('Đơn hàng này đã được đánh giá rồi!'); window.location.href='order_detail_user.php?id=$maDonhang';</script>";
    exit();
}

// 4. XỬ LÝ GỬI ĐÁNH GIÁ
if (isset($_POST['btn_submit_review'])) {
    $soSao = (int)$_POST['soSao'];
    $noiDung = $conn->real_escape_string($_POST['noiDung']);
    
    // Câu lệnh INSERT khớp hoàn toàn với cấu trúc: maDG, tenDangnhap, maDonhang, maMH, soSao, noiDung, ngayDG, trangthai
    $sql_insert = "INSERT INTO danhgia (tenDangnhap, maDonhang, maMH, soSao, noiDung, ngayDG, trangthai) 
                   VALUES ('$tenDangNhap', $maDonhang, $maMH_dai_dien, $soSao, '$noiDung', NOW(), 1)";
    
    if ($conn->query($sql_insert)) {
        echo "<script>alert('Cảm ơn bạn đã đánh giá đơn hàng!'); window.location.href='order_history.php';</script>";
        exit();
    } else {
        $error = "Lỗi SQL: " . $conn->error;
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    .review-wrapper { background-color: #fff8fb; min-height: 80vh; padding: 40px 0; font-family: 'Segoe UI', sans-serif; }
    .review-container { max-width: 600px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .product-title { color: #E91E63; margin-bottom: 5px; font-weight: 700; text-align: center; text-transform: uppercase; }
    .order-sub { text-align: center; color: #888; margin-bottom: 25px; font-size: 1rem; }
    
    /* Star Rating System */
    .rating-stars { display: flex; flex-direction: row-reverse; justify-content: center; gap: 12px; margin-bottom: 25px; }
    .rating-stars input { display: none; }
    .rating-stars label { font-size: 40px; color: #ddd; cursor: pointer; transition: 0.2s; }
    .rating-stars input:checked ~ label, 
    .rating-stars label:hover, 
    .rating-stars label:hover ~ label { color: #FFC107; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 10px; font-weight: 600; color: #555; text-align: center; }
    .form-group textarea { 
        width: 100%; padding: 15px; border: 1px solid #ffd6e5; border-radius: 12px; 
        resize: none; font-family: inherit; outline: none; transition: border-color 0.3s;
    }
    .form-group textarea:focus { border-color: #E91E63; }
    
    .btn-submit { 
        background: #E91E63; color: white; border: none; padding: 15px 30px; border-radius: 50px; 
        width: 100%; font-weight: 700; cursor: pointer; font-size: 1.1rem; transition: 0.3s;
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.2);
    }
    .btn-submit:hover { background: #c2185b; transform: translateY(-2px); }
    .back-btn { display: block; text-align: center; margin-top: 20px; color: #999; text-decoration: none; font-size: 0.9rem; }
</style>

<div class="review-wrapper">
    <div class="review-container">
        <h2 class="product-title">Đánh giá đơn hàng</h2>
        <p class="order-sub">Mã hóa đơn: #<?php echo str_pad($maDonhang, 5, '0', STR_PAD_LEFT); ?></p>
        
        <?php if(isset($error)): ?>
            <div style="color: red; background: #fee; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align:center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Mức độ hài lòng của bạn:</label>
                <div class="rating-stars">
                    <input type="radio" id="star5" name="soSao" value="5" required/><label for="star5"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star4" name="soSao" value="4" /><label for="star4"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star3" name="soSao" value="3" /><label for="star3"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star2" name="soSao" value="2" /><label for="star2"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star1" name="soSao" value="1" /><label for="star1"><i class="fa-solid fa-star"></i></label>
                </div>
            </div>

            <div class="form-group">
                <label>Nội dung nhận xét:</label>
                <textarea name="noiDung" rows="6" placeholder="Sản phẩm và dịch vụ giao hàng như thế nào?..." required></textarea>
            </div>

            <button type="submit" name="btn_submit_review" class="btn-submit">
                <i class="fa-solid fa-paper-plane"></i> GỬI ĐÁNH GIÁ NGAY
            </button>
            
            <a href="order_detail_user.php?id=<?php echo $maDonhang; ?>" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Quay lại chi tiết đơn hàng
            </a>
        </form>
    </div>
</div>

<?php require("footer.php"); ?>