<?php 
    // Yêu cầu file sidebar.php
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database
    require("../config.php"); 

    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    // Khởi tạo mảng reviews rỗng
    $reviews = [];
    
    if (isset($conn) && $conn->connect_error === null) {
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        
        // TRUY VẤN JOIN: Lấy thông tin từ bảng danhgia, nguoidung và mathang
        // maDonhang được lấy trực tiếp từ bảng danhgia để đảm bảo tính xác thực
        $sql = "SELECT dg.maDG, dg.tenDangnhap, dg.maDonhang, dg.soSao, dg.noiDung, dg.ngayDG, dg.trangthai, 
                       n.hoTen, m.tenMH 
                FROM danhgia dg
                JOIN nguoidung n ON dg.tenDangnhap = n.tenDangnhap
                JOIN mathang m ON dg.maMH = m.maMH";
        
        if (!empty($search)) {
            $sql .= " WHERE dg.noiDung LIKE '%$search%' 
                      OR n.hoTen LIKE '%$search%' 
                      OR m.tenMH LIKE '%$search%'
                      OR dg.maDonhang LIKE '%$search%'"; 
        }
        
        $sql .= " ORDER BY dg.ngayDG DESC"; 
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
    }
?>

<style>
/* Kế thừa và tối ưu CSS dựa trên khung sườn của bạn */
.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }
.toolbar-panel { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; }
.search-group { display: flex; align-items: center; border: 1px solid #ffd6e5; border-radius: 30px; padding: 5px 15px; width: 100%; max-width: 400px; background: #fff; }
.search-group input { border: none; outline: none; flex: 1; color: #555; }
.btn-search { background: #e91e63; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; display: flex; align-items: center; justify-content: center; }

.table-panel { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; margin-top: -15px; }
.custom-table th { color: #e91e63; font-weight: 700; padding: 10px; text-align: left; border-bottom: 2px solid #fff0f5; font-size: 0.85rem; text-transform: uppercase; }
.custom-table tbody tr { background: white; transition: 0.2s; }
.custom-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(233,30,99,0.08); }
.custom-table td { padding: 15px; vertical-align: middle; color: #555; border-top: 1px solid #fcfcfc; border-bottom: 1px solid #fcfcfc; }

/* Style cho mã đơn hàng lấy từ bảng dondathang */
.order-badge { background: #e3f2fd; color: #1565c0; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-family: monospace; font-size: 0.9rem; }
.star-rating { color: #ffc107; font-size: 0.9rem; }
.review-content { font-style: italic; color: #666; font-size: 0.9rem; line-height: 1.4; }
.item-name { font-weight: 600; color: #d81b60; }

.action-group { display: flex; gap: 8px; }
.action-btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 700; transition: 0.2s; display: inline-block; }
.btn-toggle { background: #fff0f5; color: #e91e63; border: 1px solid #ffe1ec; }
.btn-toggle:hover { background: #e91e63; color: white; }
.btn-delete { background: #fff5f5; color: #dc3545; border: 1px solid #ffdada; }
.btn-delete:hover { background: #dc3545; color: white; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Đánh Giá Đơn Hàng</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm đơn hàng, khách hàng, nội dung..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                        <th width="12%">Mã đơn hàng</th>
                        <th width="15%">Người đánh giá</th>
                        <th width="18%">Sản phẩm</th>
                        <th width="10%">Xếp hạng</th>
                        <th width="25%">Nội dung nhận xét</th>
                        <th width="10%">Ngày gửi</th>
                        <th width="10%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($reviews)) {
                            foreach ($reviews as $row) {
                                // Hiển thị số sao dựa trên cột soSao
                                $stars = str_repeat('<i class="fa-solid fa-star"></i>', $row['soSao']);
                                $stars .= str_repeat('<i class="fa-regular fa-star"></i>', 5 - $row['soSao']);

                                echo '<tr>';
                                // HIỂN THỊ MÃ ĐƠN HÀNG
                                echo '<td><span class="order-badge">#' . str_pad($row['maDonhang'], 5, '0', STR_PAD_LEFT) . '</span></td>';
                                
                                echo '<td><b>' . htmlspecialchars($row['hoTen']) . '</b><br><small style="color:#999;">@' . htmlspecialchars($row['tenDangnhap']) . '</small></td>';
                                echo '<td><span class="item-name">' . htmlspecialchars($row['tenMH']) . '</span></td>';
                                echo '<td><span class="star-rating">' . $stars . '</span></td>';
                                echo '<td><div class="review-content">"' . htmlspecialchars($row['noiDung']) . '"</div></td>';
                                echo '<td><small>' . date('d/m/Y', strtotime($row['ngayDG'])) . '</small></td>';
                                
                                echo '<td>';
                                echo '<div class="action-group">';
                                    $btnText = ($row['trangthai'] == 1) ? 'Ẩn' : 'Hiện';
                                    echo '<a href="review_status.php?id=' . $row['maDG'] . '" class="action-btn btn-toggle">' . $btnText . '</a>';
                                    echo '<a href="review_delete.php?id=' . $row['maDG'] . '" class="action-btn btn-delete" onclick="return confirm(\'Xóa đánh giá này?\')">Xóa</a>';
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" style="text-align: center; padding: 30px; color:#999;">Không có dữ liệu đánh giá.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>