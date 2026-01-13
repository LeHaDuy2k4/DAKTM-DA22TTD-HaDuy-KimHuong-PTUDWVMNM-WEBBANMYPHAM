<?php
session_start();
require_once("config.php");
require("header.php");

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['tenDangnhap'])) {
    header("Location: login.php");
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];
$maDonhang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = "";

// 2. LẤY THÔNG TIN ĐƠN HÀNG (để kiểm tra quyền sở hữu và trạng thái)
$sql_check = "SELECT * FROM dondathang 
              WHERE maDonhang = $maDonhang 
              AND tenDangnhap = '$tenDangNhap'
              AND (trangthai LIKE '%giao%' OR trangthai LIKE '%thành%')"; // Chỉ cho phép trả đơn đã giao/hoàn thành

$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    // Nếu không tìm thấy đơn hoặc đơn không đủ điều kiện
    echo "<div class='container' style='padding: 50px; text-align: center;'>
            <h2 style='color: #dc3545;'>Không tìm thấy đơn hàng hoặc đơn hàng không đủ điều kiện trả!</h2>
            <a href='order_history.php' class='btn-back'>Quay lại lịch sử</a>
          </div>";
    require("footer.php");
    exit();
}

$order = $result_check->fetch_assoc();

// 3. XỬ LÝ KHI NGƯỜI DÙNG BẤM GỬI YÊU CẦU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_submit_return'])) {
    $lyDo = $conn->real_escape_string($_POST['lyDoTra']);
    
    if (empty($lyDo)) {
        $message = "<div class='alert alert-danger'>Vui lòng nhập lý do trả hàng!</div>";
    } else {
        $sql_update = "UPDATE dondathang 
                       SET trangthai = 'Yêu cầu trả hàng', 
                           lyDoTra = '$lyDo'
                       WHERE maDonhang = $maDonhang";
                       
        if ($conn->query($sql_update)) {
            echo "<script>
                    alert('Yêu cầu trả hàng đã được gửi thành công!');
                    window.location.href = 'order_history.php';
                  </script>";
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Lỗi hệ thống: " . $conn->error . "</div>";
        }
    }
}
?>

<style>
    .return-container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .page-title { color: #E91E63; text-align: center; margin-bottom: 25px; font-weight: 700; }
    .order-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px dashed #ddd; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit; resize: vertical; min-height: 120px; }
    .form-control:focus { border-color: #E91E63; }
    
    .btn-submit { background: #9C27B0; color: white; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 1rem; }
    .btn-submit:hover { background: #7B1FA2; box-shadow: 0 4px 10px rgba(156, 39, 176, 0.3); }
    
    .btn-back { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-weight: 500; }
    .btn-back:hover { color: #333; text-decoration: underline; }
    
    .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
    .alert-danger { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
</style>

<div class="return-container">
    <h2 class="page-title"><i class="fa-solid fa-rotate-left"></i> YÊU CẦU TRẢ HÀNG</h2>
    
    <?php echo $message; ?>

    <div class="order-info">
        <p><strong>Mã đơn hàng:</strong> #<?php echo str_pad($order['maDonhang'], 5, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['ngayDat'])); ?></p>
        <p><strong>Tổng tiền:</strong> <span style="color: #E91E63; font-weight: 700;"><?php echo number_format($order['tongTien'], 0, ',', '.'); ?>₫</span></p>
    </div>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Lý do trả hàng chi tiết:</label>
            <textarea name="lyDoTra" class="form-control" placeholder="Ví dụ: Sản phẩm bị vỡ, sai màu sắc, không đúng mô tả... (Vui lòng mô tả càng chi tiết càng tốt)"></textarea>
        </div>

        <button type="submit" name="btn_submit_return" class="btn-submit">
            XÁC NHẬN GỬI YÊU CẦU
        </button>
    </form>

    <a href="order_history.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đơn hàng</a>
</div>

<?php require("footer.php"); ?>