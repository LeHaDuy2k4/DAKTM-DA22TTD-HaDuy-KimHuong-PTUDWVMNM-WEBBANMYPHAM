<?php 
    // Yêu cầu file sidebar.php (Giả định chứa session_start())
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database
    require("../config.php"); 

    // KHAI BÁO BIẾN KIỂM TRA QUYỀN ADMIN (quyen = 1)
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    // Khởi tạo mảng promotions rỗng
    $promotions = [];
    
    // ====================================================================
    // 1. TRUY VẤN CƠ SỞ DỮ LIỆU
    // ====================================================================
    if (isset($conn) && $conn->connect_error === null) {
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        
        // Truy vấn maKM, tenKM, phantramgiam, ngayBD, ngayKT
        $sql = "SELECT maKM, tenKM, phantramgiam, ngayBD, ngayKT FROM khuyenmai";
        
        if (!empty($search)) {
            $sql .= " WHERE tenKM LIKE '%$search%'";
        }
        
        $sql .= " ORDER BY ngayBD DESC"; 
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $promotions[] = $row;
            }
        }
    }
?>

<style>
/* --- STYLE CHUNG --- */
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

/* Thanh công cụ chứa tìm kiếm và các nút điều hướng */
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
    max-width: 350px;
    transition: 0.3s;
    background: #fff;
}

.search-group input {
    border: none;
    outline: none;
    flex: 1;
    color: #555;
    font-size: 0.95rem;
}

.btn-search {
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Nút chức năng phía bên phải */
.action-buttons-top {
    display: flex;
    gap: 10px;
}

.btn-nav {
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.btn-batch {
    background: #6f42c1; /* Màu tím cho chức năng gán hàng loạt */
    color: white;
    box-shadow: 0 4px 10px rgba(111, 66, 193, 0.3);
}

.btn-add-new {
    background: #20c997; 
    color: white;
    box-shadow: 0 4px 10px rgba(32, 201, 151, 0.3);
}

.btn-nav:hover {
    transform: translateY(-2px);
    filter: brightness(0.9);
}

/* --- TABLE STYLE --- */
.table-panel {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.custom-table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0 15px; 
    margin-top: -15px;
}

.custom-table th {
    color: #e91e63;
    font-weight: 700;
    padding: 10px 15px;
    text-align: left;
    border-bottom: 2px solid #fff0f5;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.custom-table td {
    padding: 15px;
    vertical-align: middle;
    color: #555;
}

.custom-table tbody tr {
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}

/* Badge trang trí */
.sku-badge {
    font-family: 'Consolas', monospace;
    background: #fff0f5;
    color: #d81b60;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    border: 1px dashed #e91e63;
}

.discount-pill {
    background: #e91e63;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
}

.status-tag {
    font-weight: 600;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 5px;
}
.active-km { color: #2e7d32; }
.active-km::before { content:''; width: 8px; height: 8px; background: #2e7d32; border-radius: 50%; }
.expired-km { color: #c62828; }
.expired-km::before { content:''; width: 8px; height: 8px; background: #c62828; border-radius: 50%; }
.upcoming-km { color: #1565c0; }
.upcoming-km::before { content:''; width: 8px; height: 8px; background: #1565c0; border-radius: 50%; }

/* Nhóm 2 nút hành động nằm ngang */
.action-group {
    display: flex;
    gap: 8px;
    align-items: center;
    white-space: nowrap;
}

.action-btn {
    padding: 6px 14px; 
    border-radius: 6px;
    display: inline-block;
    text-decoration: none;
    transition: 0.2s;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-edit { background: #fff0f5; color: #e91e63; border: 1px solid #ffe1ec; }
.btn-edit:hover { background: #e91e63; color: white; }

.btn-delete { background: #ffeded; color: #dc3545; border: 1px solid #ffcccc; }
.btn-delete:hover { background: #dc3545; color: white; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Khuyến Mãi</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm tên khuyến mãi..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <div class="action-buttons-top">
            <a href="promotion_batch_assign.php" class="btn-nav btn-batch">
                <i class="fa-solid fa-layer-group"></i> Gán KM Hàng Loạt
            </a>
            <a href="promotion_add.php" class="btn-nav btn-add-new">
                <i class="fa-solid fa-plus"></i> Thêm Khuyến Mãi
            </a>
        </div>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="10%">Mã KM</th>
                        <th width="25%">Tên Chương Trình</th>
                        <th width="15%">Giảm giá</th>
                        <th width="15%">Bắt đầu</th>
                        <th width="15%">Kết thúc</th>
                        <th width="10%">Trạng thái</th>
                        <th width="10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($promotions)) {
                            $today = date('Y-m-d');
                            foreach ($promotions as $promo) {
                                // Xác định trạng thái dựa trên ngày hiện tại so với ngayBD và ngayKT
                                if ($today < $promo['ngayBD']) {
                                    $statusClass = 'upcoming-km';
                                    $statusText = 'Sắp diễn ra';
                                } elseif ($today > $promo['ngayKT']) {
                                    $statusClass = 'expired-km';
                                    $statusText = 'Hết hạn';
                                } else {
                                    $statusClass = 'active-km';
                                    $statusText = 'Đang chạy';
                                }

                                echo '<tr>';
                                // Định dạng mã KM (ví dụ KM001)
                                echo '<td><span class="sku-badge">KM' . str_pad($promo['maKM'], 3, '0', STR_PAD_LEFT) . '</span></td>';
                                echo '<td><div class="user-text"><h4>' . htmlspecialchars($promo['tenKM']) . '</h4></div></td>';
                                echo '<td><span class="discount-pill">-' . (float)$promo['phantramgiam'] . '%</span></td>';
                                echo '<td>' . date('d/m/Y', strtotime($promo['ngayBD'])) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($promo['ngayKT'])) . '</td>';
                                echo '<td><span class="status-tag ' . $statusClass . '">' . $statusText . '</span></td>';
                                
                                // Cột hành động với 2 nút nằm ngang
                                echo '<td>';
                                echo '<div class="action-group">';
                                echo '<a href="promotion_add.php?id=' . $promo['maKM'] . '" class="action-btn btn-edit">Sửa</a>';
                                echo '<a href="promotion_delete.php?id=' . $promo['maKM'] . '" class="action-btn btn-delete" onclick="return confirm(\'Bạn có chắc chắn muốn xóa khuyến mãi này?\')">Xóa</a>';
                                echo '</div>';
                                echo '</td>';
                                
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" style="text-align: center; padding: 30px; color: #888;">Không tìm thấy chương trình khuyến mãi nào.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Loại bỏ các thông báo tự động từ các thư viện bên ngoài nếu có
    document.querySelectorAll('.swiper-notification').forEach(el => el.remove());
</script>