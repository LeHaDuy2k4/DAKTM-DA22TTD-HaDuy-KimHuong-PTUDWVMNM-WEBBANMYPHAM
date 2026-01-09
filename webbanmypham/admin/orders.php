<?php 
    // Yêu cầu file sidebar.php (Giả định chứa session_start())
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database
    require("../config.php"); 

    // KHAI BÁO BIẾN KIỂM TRA QUYỀN ADMIN (quyen = 1)
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    // Khởi tạo mảng orders rỗng
    $orders = [];
    
    // ====================================================================
    // 1. TRUY VẤN CƠ SỞ DỮ LIỆU (JOIN dondathang với nguoidung)
    // ====================================================================
    if (isset($conn) && $conn->connect_error === null) {
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        
        // Truy vấn lấy đơn hàng và họ tên người mua
        $sql = "SELECT d.maDonhang, d.ngayDat, d.tongTien, d.trangthai, n.hoTen 
                FROM dondathang d 
                JOIN nguoidung n ON d.tenDangnhap = n.tenDangnhap";
        
        if (!empty($search)) {
            $sql .= " WHERE d.maDonhang LIKE '%$search%' OR n.hoTen LIKE '%$search%'";
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
* {
    font-family: "Times New Roman", Times, serif;
}

.main-content {
    margin-left: 250px; 
    padding: 25px;
    background-color: #fff8fb; 
    min-height: 100vh;
}

.dashboard-title {
    color: #e91e63;
    margin-bottom: 25px;
    font-weight: 700;
}

.toolbar-panel {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.search-group {
    display: flex;
    align-items: center;
    border: 1px solid #ffd6e5;
    border-radius: 30px; 
    padding: 5px 5px 5px 15px;
    width: 100%;
    max-width: 400px;
    background: #fff;
}

.search-group input { 
    border: none; 
    outline: none; 
    flex: 1; 
    color: #555; 
    font-size: 0.95rem; /* Giữ nguyên cỡ chữ cũ */
    font-family: "Times New Roman", Times, serif; 
}

.btn-search {
    background: #e91e63; color: white; border: none; border-radius: 50%;
    width: 35px; height: 35px; cursor: pointer; display: flex; align-items: center; justify-content: center;
}

/* --- TABLE STYLES --- */
.table-panel {
    background: #ffffff; padding: 25px; border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; margin-top: -15px; }

.custom-table th {
    color: #e91e63; font-weight: 700; padding: 10px 15px; text-align: left;
    text-transform: uppercase; font-size: 0.85rem; /* Giữ nguyên cỡ chữ cũ */
    border-bottom: 2px solid #fff0f5;
}

.custom-table tbody tr { background: white; transition: 0.2s; }
.custom-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08); }

.custom-table td { padding: 15px; vertical-align: middle; color: #555; border-top: 1px solid #fcfcfc; border-bottom: 1px solid #fcfcfc; }
.custom-table td:first-child { border-left: 1px solid #fcfcfc; border-radius: 10px 0 0 10px; }
.custom-table td:last-child { border-right: 1px solid #fcfcfc; border-radius: 0 10px 10px 0; }

/* --- ORDER SPECIFIC ELEMENTS --- */
.order-id-badge {
    font-family: "Times New Roman", Times, serif; /* Đổi font nhưng giữ nguyên style badge */
    background: #fff0f5; color: #d81b60; padding: 5px 10px;
    border-radius: 6px; font-weight: 600; border: 1px dashed #e91e63;
}

.price-text { color: #e91e63; font-weight: 700; font-size: 1rem; }

/* Trạng thái đơn hàng bằng Badge */
.status-badge {
    padding: 6px 12px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; display: inline-block;
}
.status-pending { background: #fff8e1; color: #f57f17; } 
.status-shipping { background: #e3f2fd; color: #1565c0; } 
.status-completed { background: #e8f5e9; color: #2e7d32; } 
.status-cancelled { background: #ffebee; color: #c62828; } 

.action-btn {
    padding: 6px 15px; border-radius: 6px; display: inline-flex;
    text-decoration: none; font-size: 0.9rem; font-weight: 600;
    background: #f0f0f0; color: #555; transition: 0.3s;
}
.action-btn:hover { background: #e91e63; color: white; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Đơn Hàng</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm mã đơn hàng hoặc tên khách hàng..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                        <th width="15%">Mã Đơn</th>
                        <th width="25%">Khách hàng</th>
                        <th width="20%">Ngày đặt</th>
                        <th width="15%">Tổng tiền</th>
                        <th width="15%">Trạng thái</th>
                        <th width="10%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($orders)) {
                            foreach ($orders as $order) {
                                // Xác định class CSS cho trạng thái
                                $statusClass = '';
                                $st = mb_strtolower($order['trangthai']);
                                if (strpos($st, 'chờ') !== false) $statusClass = 'status-pending';
                                elseif (strpos($st, 'đang') !== false) $statusClass = 'status-shipping';
                                elseif (strpos($st, 'hoàn') !== false || strpos($st, 'đã giao') !== false) $statusClass = 'status-completed';
                                elseif (strpos($st, 'hủy') !== false) $statusClass = 'status-cancelled';
                                else $statusClass = 'status-pending';

                                echo '<tr>';
                                echo '<td><span class="order-id-badge">#' . str_pad($order['maDonhang'], 5, '0', STR_PAD_LEFT) . '</span></td>';
                                echo '<td><div class="user-text"><h4>' . htmlspecialchars($order['hoTen']) . '</h4></div></td>';
                                echo '<td>' . date('d/m/Y H:i', strtotime($order['ngayDat'])) . '</td>';
                                echo '<td><span class="price-text">' . number_format($order['tongTien'], 0, ',', '.') . '₫</span></td>';
                                echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($order['trangthai']) . '</span></td>';
                                echo '<td>
                                        <a href="order_detail.php?id=' . $order['maDonhang'] . '" class="action-btn">Chi tiết</a>
                                      </td>';
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

<script>
    // Loại bỏ thông báo Swiper nếu có xung đột giao diện
    document.querySelectorAll('.swiper-notification').forEach(el => el.remove());
</script>