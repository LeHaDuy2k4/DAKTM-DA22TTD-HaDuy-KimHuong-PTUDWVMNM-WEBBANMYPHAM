<?php 
    require("sidebar.php"); 
    require("../config.php"); 

    // Kiểm tra quyền Admin
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;
    if (!$isAdmin) { header("Location: ../login.php"); exit(); }

    $maDonhang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $orderInfo = null;
    $orderDetails = [];

    if (isset($conn) && $maDonhang > 0) {
        // 1. LẤY THÔNG TIN TỔNG QUAN ĐƠN HÀNG
        $sql_order = "SELECT d.*, n.hoTen FROM dondathang d 
                      JOIN nguoidung n ON d.tenDangnhap = n.tenDangnhap 
                      WHERE d.maDonhang = $maDonhang";
        $res_order = $conn->query($sql_order);
        $orderInfo = $res_order->fetch_assoc();

        if ($orderInfo) {
            $currentStatus = $orderInfo['trangthai'];
            
            // --- LOGIC KHÓA ĐƠN HÀNG ---
            // Khóa khi đơn ở trạng thái: Đã hủy, Đã hoàn thành HOẶC Yêu cầu trả hàng
            $isLocked = ($currentStatus == 'Đã hủy' || $currentStatus == 'Đã hoàn thành' || $currentStatus == 'Yêu cầu trả hàng');

            // 2. XỬ LÝ CẬP NHẬT TRẠNG THÁI
            // Chỉ cho phép cập nhật nếu đơn chưa bị khóa
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status']) && !$isLocked) {
                $newStatus = $conn->real_escape_string($_POST['trangthai']);
                $sql_update = "UPDATE dondathang SET trangthai = '$newStatus' WHERE maDonhang = $maDonhang";
                
                if ($conn->query($sql_update)) {
                    echo "<script>
                            alert('Cập nhật trạng thái đơn hàng thành công!'); 
                            window.location.href='orders.php'; // Quay về trang danh sách đơn hàng
                          </script>";
                    exit();
                }
            }

            // 3. LẤY CHI TIẾT SẢN PHẨM
            $sql_details = "SELECT ct.*, m.tenMH, m.hinhAnh 
                            FROM chitietdathang ct 
                            JOIN mathang m ON ct.maMH = m.maMH 
                            WHERE ct.maDonhang = $maDonhang";
            $res_details = $conn->query($sql_details);
            while($row = $res_details->fetch_assoc()) {
                $orderDetails[] = $row;
            }
        }
    }

    if (!$orderInfo) { echo "<div class='main-content'><h1>Đơn hàng không tồn tại!</h1></div>"; exit(); }
?>

<style>
/* THIẾT LẬP FONT CHỮ TIMES NEW ROMAN TOÀN CỤC */
* { font-family: "Times New Roman", Times, serif; }

.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
.detail-grid { display: grid; grid-template-columns: 1fr 350px; gap: 25px; }
.panel { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
.panel-title { color: #e91e63; font-weight: 700; margin-bottom: 15px; border-bottom: 1px solid #fff0f5; padding-bottom: 10px; font-size: 1.1rem; }

.info-row { display: flex; margin-bottom: 12px; font-size: 0.95rem; }
.info-label { width: 130px; color: #888; font-weight: 500; }
.info-value { color: #333; font-weight: 600; flex: 1; }

.product-table { width: 100%; border-collapse: collapse; }
.product-table th { text-align: left; padding: 12px; color: #e91e63; border-bottom: 2px solid #fff0f5; font-size: 0.85rem; text-transform: uppercase; }
.product-table td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; vertical-align: middle; }
.product-img { width: 65px; height: 65px; object-fit: contain; border-radius: 8px; border: 1px solid #eee; background: #fff; }

/* Status Select Styles */
.status-select { width: 100%; padding: 12px; border: 1px solid #ffd6e5; border-radius: 8px; margin-bottom: 15px; outline: none; font-family: "Times New Roman", Times, serif; }
.status-select:disabled { background: #f5f5f5; cursor: not-allowed; }

.btn-update { background: #e91e63; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; }
.btn-update:hover:not(:disabled) { background: #c2185b; box-shadow: 0 4px 12px rgba(233, 30, 99, 0.2); }
.btn-update:disabled { background: #ccc; cursor: not-allowed; }

/* Alert Messages */
.status-locked-msg { background: #fff3f3; color: #d32f2f; padding: 12px; border-radius: 8px; font-size: 0.85rem; border: 1px solid #ffcdd2; margin-bottom: 15px; font-weight: 600; }

.return-request-box { background: #f3e5f5; border: 1px solid #e1bee7; color: #7b1fa2; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
.return-title { font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }

.total-text { font-size: 1.3rem; color: #e91e63; font-weight: 800; text-align: right; margin-top: 15px; }
.order-id-badge { background: #fff0f5; color: #d81b60; padding: 5px 12px; border-radius: 6px; font-weight: 600; border: 1px dashed #e91e63; font-family: "Times New Roman", Times, serif; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">
        <a href="orders.php" style="color: inherit; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        Chi Tiết Đơn Hàng <span class="order-id-badge">#<?php echo str_pad($orderInfo['maDonhang'], 5, '0', STR_PAD_LEFT); ?></span>
    </h1>

    <div class="detail-grid">
        <div class="left-col">
            
            <?php if ($orderInfo['trangthai'] == 'Yêu cầu trả hàng'): ?>
            <div class="return-request-box">
                <div class="return-title"><i class="fa-solid fa-rotate-left"></i> KHÁCH HÀNG YÊU CẦU TRẢ HÀNG</div>
                <div>
                    <strong>Lý do:</strong> 
                    <?php 
                        // Ưu tiên hiển thị cột lyDoTra nếu có, nếu không thì hiển thị từ ghiChu
                        if (isset($orderInfo['lyDoTra']) && !empty($orderInfo['lyDoTra'])) {
                            echo htmlspecialchars($orderInfo['lyDoTra']);
                        } else {
                            echo "Xem trong phần Ghi chú bên dưới.";
                        }
                    ?>
                </div>
                <div style="margin-top: 10px; font-size: 0.9rem; font-style: italic;">
                    --> Đơn hàng đang bị tạm khóa để xử lý yêu cầu trả hàng.
                </div>
            </div>
            <?php endif; ?>

            <div class="panel">
                <h2 class="panel-title"><i class="fa-solid fa-box"></i> Danh sách mặt hàng</h2>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th width="100">Giá bán</th>
                            <th width="80">SL</th>
                            <th width="120">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['hinhAnh']); ?>" 
                                     class="product-img" 
                                     onerror="this.src='https://via.placeholder.com/100?text=No+Image'">
                            </td>
                            <td class="info-value"><?php echo htmlspecialchars($item['tenMH']); ?></td>
                            <td><?php echo number_format($item['giaBan'], 0, ',', '.'); ?>₫</td>
                            <td style="text-align: center;"><?php echo $item['soLuong']; ?></td>
                            <td style="color:#e91e63; font-weight:700;"><?php echo number_format($item['giaBan'] * $item['soLuong'], 0, ',', '.'); ?>₫</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-text">TỔNG CỘNG: <?php echo number_format($orderInfo['tongTien'], 0, ',', '.'); ?>₫</div>
            </div>

            <div class="panel">
                <h2 class="panel-title"><i class="fa-solid fa-location-dot"></i> Thông tin nhận hàng</h2>
                <div class="info-row">
                    <div class="info-label">Người nhận:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderDetails[0]['hoTenNguoiNhan']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Số điện thoại:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderDetails[0]['sdtNguoiNhan']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Địa chỉ:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderDetails[0]['diaChiGiaoHang']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ghi chú:</div>
                    <div class="info-value" style="font-weight: 400; color: #666; background: #f9f9f9; padding: 5px; border-radius: 4px;">
                        <?php echo !empty($orderDetails[0]['ghiChu']) ? htmlspecialchars($orderDetails[0]['ghiChu']) : 'Không có ghi chú'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-col">
            <div class="panel">
                <h2 class="panel-title"><i class="fa-solid fa-gear"></i> Xử lý đơn hàng</h2>
                
                <?php if ($isLocked): ?>
                    <div class="status-locked-msg">
                        <i class="fa-solid fa-lock"></i> Đơn hàng đã kết thúc hoặc đang yêu cầu trả hàng (<?php echo $currentStatus; ?>). Không thể chỉnh sửa.
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <label class="info-label" style="display: block; margin-bottom: 8px;">Trạng thái hiện tại:</label>
                    <select name="trangthai" class="status-select" <?php echo $isLocked ? 'disabled' : ''; ?>>
                        <option value="Chờ xử lý" <?php if($currentStatus == 'Chờ xử lý') echo 'selected'; ?>>Chờ xử lý</option>
                        <option value="Đang giao hàng" <?php if($currentStatus == 'Đang giao hàng') echo 'selected'; ?>>Đang giao hàng</option>
                        <option value="Đã hoàn thành" <?php if($currentStatus == 'Đã hoàn thành') echo 'selected'; ?>>Đã hoàn thành (Giao thành công)</option>
                        <option value="Đã hủy" <?php if($currentStatus == 'Đã hủy') echo 'selected'; ?>>Đã hủy (Hết hàng/Khách hủy/Hoàn tiền)</option>
                        
                        <?php if($currentStatus == 'Yêu cầu trả hàng'): ?>
                            <option value="Yêu cầu trả hàng" selected>Yêu cầu trả hàng (Đang xử lý)</option>
                        <?php endif; ?>
                    </select>
                    
                    <button type="submit" name="update_status" class="btn-update" <?php echo $isLocked ? 'disabled' : ''; ?>>
                        <?php echo $isLocked ? 'ĐƠN HÀNG ĐÃ ĐÓNG' : 'CẬP NHẬT & QUAY LẠI'; ?>
                    </button>
                </form>
            </div>

            <div class="panel">
                <h2 class="panel-title"><i class="fa-solid fa-user"></i> Người đặt hàng</h2>
                <div class="info-row">
                    <div class="info-label">Tài khoản:</div>
                    <div class="info-value" style="color: #e91e63;"><?php echo htmlspecialchars($orderInfo['tenDangnhap']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Họ tên:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderInfo['hoTen']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Thời gian đặt:</div>
                    <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($orderInfo['ngayDat'])); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>