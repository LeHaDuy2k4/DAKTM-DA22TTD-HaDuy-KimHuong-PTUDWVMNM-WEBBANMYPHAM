<?php 
    // 1. KHỞI ĐỘNG SESSION VÀ GỌI CONFIG TRƯỚC TIÊN
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require("../config.php"); 

    // 2. KIỂM TRA QUYỀN ADMIN NGAY LẬP TỨC (Trước khi xuất bất kỳ HTML nào)
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;
    if (!$isAdmin) { 
        header("Location: ../login.php"); 
        exit(); 
    }

    // 3. SAU KHI KIỂM TRA XONG MỚI GỌI SIDEBAR (Vì file này chứa HTML)
    require("sidebar.php"); 

    $orders = [];
    $search_query = "";

    if (isset($_GET['search'])) {
        $search_query = trim($_GET['search']);
    }
    
    if (isset($conn) && $conn->connect_error === null) {
        $sql = "SELECT d.maDonhang, d.ngayDat, d.tongTien, d.trangthai, n.hoTen 
                FROM dondathang d 
                JOIN nguoidung n ON d.tenDangnhap = n.tenDangnhap";
        
        if (!empty($search_query)) {
            $sql .= " WHERE d.maDonhang LIKE '%$search_query%' OR n.hoTen LIKE '%$search_query%'";
        }
        
        $sql .= " ORDER BY d.ngayDat DESC"; 
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
    }
?>

<style>
/* THIẾT LẬP FONT CHỮ TIMES NEW ROMAN TOÀN CỤC */
* { font-family: "Times New Roman", Times, serif; }

.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }

/* TOOLBAR & SEARCH */
.toolbar-panel { background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; gap: 15px; }
.search-group { display: flex; align-items: center; border: 1px solid #ffd6e5; border-radius: 30px; padding: 5px 15px; width: 100%; max-width: 400px; background: #fff; }
.search-group input { border: none; outline: none; flex: 1; color: #555; font-size: 0.95rem; }
.btn-search { background: #e91e63; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; display: flex; align-items: center; justify-content: center; }

/* TABLE STYLES */
.table-panel { background: #ffffff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; margin-top: -15px; }
.custom-table th { color: #e91e63; font-weight: 700; padding: 10px 15px; text-align: left; border-bottom: 2px solid #fff0f5; text-transform: uppercase; font-size: 0.85rem; }
.custom-table td { padding: 15px; vertical-align: middle; color: #555; border-top: 1px solid #fcfcfc; border-bottom: 1px solid #fcfcfc; }
.custom-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08); }

/* ELEMENTS */
.order-id-badge { font-family: "Times New Roman", Times, serif; background: #fff0f5; color: #d81b60; padding: 5px 10px; border-radius: 6px; font-weight: 600; border: 1px dashed #e91e63; }
.price-text { color: #e91e63; font-weight: 700; font-size: 1rem; }

/* STATUS BADGES */
.status-badge { padding: 6px 12px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; display: inline-block; }
.status-pending { background: #fff8e1; color: #f57f17; } 
.status-shipping { background: #e3f2fd; color: #1565c0; } 
.status-completed { background: #e8f5e9; color: #2e7d32; } 
.status-cancelled { background: #ffebee; color: #c62828; } 
.status-return { background: #f3e5f5; color: #7b1fa2; border: 1px solid #e1bee7; } /* Mới: Màu tím cho trả hàng */

/* ACTION BUTTONS */
.action-btn { padding: 6px 12px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-right: 5px; transition: 0.2s; }
.btn-detail { background: #fff0f5; color: #e91e63; border: 1px solid #ffe1ec; }
.btn-detail:hover { background: #e91e63; color: white; }
.btn-delete { background: #ffeded; color: #dc3545; border: 1px solid #ffcccc; }
.btn-delete:hover { background: #dc3545; color: white; }
.btn-disabled { background: #f5f5f5; color: #ccc; border: 1px solid #eee; cursor: not-allowed; opacity: 0.6; }

/* ALERT MESSAGES */
.alert-box { padding: 15px; margin-bottom: 20px; border-radius: 8px; border: 1px solid transparent; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #d4edda; color: #155724; border-color: #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Đơn Hàng</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert-box alert-success">
            <i class="fa-solid fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-box alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm mã đơn hàng hoặc tên khách hàng..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="10%">Mã Đơn</th>
                        <th width="20%">Khách hàng</th>
                        <th width="15%">Ngày đặt</th>
                        <th width="15%">Tổng tiền</th>
                        <th width="15%">Trạng thái</th>
                        <th width="15%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($orders)) {
                            foreach ($orders as $order) {
                                // Xác định class CSS cho trạng thái
                                $st = mb_strtolower($order['trangthai']);
                                $statusClass = 'status-pending'; // Mặc định

                                if (strpos($st, 'chờ') !== false) $statusClass = 'status-pending';
                                elseif (strpos($st, 'đang') !== false) $statusClass = 'status-shipping';
                                elseif (strpos($st, 'hoàn') !== false || strpos($st, 'đã giao') !== false) $statusClass = 'status-completed';
                                elseif (strpos($st, 'hủy') !== false) $statusClass = 'status-cancelled';
                                elseif (strpos($st, 'trả') !== false) $statusClass = 'status-return'; // Class cho Trả hàng

                                echo '<tr>';
                                echo '<td><span class="order-id-badge">#' . str_pad($order['maDonhang'], 5, '0', STR_PAD_LEFT) . '</span></td>';
                                echo '<td><div class="user-text"><h4>' . htmlspecialchars($order['hoTen']) . '</h4></div></td>';
                                echo '<td>' . date('d/m/Y H:i', strtotime($order['ngayDat'])) . '</td>';
                                echo '<td><span class="price-text">' . number_format($order['tongTien'], 0, ',', '.') . '₫</span></td>';
                                echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($order['trangthai']) . '</span></td>';
                                echo '<td>';
                                
                                // Nút Chi tiết
                                echo '<a href="order_detail.php?id=' . $order['maDonhang'] . '" class="action-btn btn-detail" title="Xem chi tiết">Chi tiết</a>';
                                
                                // Nút Xóa
                                // Cho phép xóa các đơn: Đã hủy, Đã hoàn thành, Yêu cầu trả hàng
                                if ($order['trangthai'] == 'Đã hủy' || $order['trangthai'] == 'Đã hoàn thành' || $order['trangthai'] == 'Yêu cầu trả hàng') {
                                    echo '<a href="order_delete.php?id=' . $order['maDonhang'] . '" class="action-btn btn-delete" onclick="return confirm(\'CẢNH BÁO: Hành động này không thể hoàn tác.\\nBạn có chắc chắn muốn xóa vĩnh viễn đơn hàng #' . $order['maDonhang'] . ' không?\')">Xóa</a>';
                                } else {
                                    // Hiển thị nút mờ (disabled) cho các đơn đang xử lý
                                    echo '<span class="action-btn btn-disabled" title="Chỉ xóa được đơn đã hoàn thành, đã hủy hoặc yêu cầu trả">Xóa</span>';
                                }
                                
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Không tìm thấy đơn hàng nào.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>