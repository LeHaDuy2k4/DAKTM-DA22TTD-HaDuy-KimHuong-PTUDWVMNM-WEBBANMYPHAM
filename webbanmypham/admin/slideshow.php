<?php 
    // Yêu cầu file sidebar.php (Giả định chứa session_start())
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database
    require("../config.php"); 

    // KHAI BÁO BIẾN KIỂM TRA QUYỀN ADMIN
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    $slides = [];
    
    // ====================================================================
    // 1. TRUY VẤN CƠ SỞ DỮ LIỆU (SỬA TÊN BẢNG THÀNH: trinhchieu)
    // ====================================================================
    if (isset($conn) && $conn->connect_error === null) {
        // Thay đổi slideshow thành trinhchieu ở dòng dưới đây
        $sql = "SELECT maTC, tieuDe, hinhAnh, linkAnh, thuTu, trangThai FROM trinhchieu ORDER BY thuTu ASC"; 
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $slides[] = $row;
            }
        }
    }
?>

<style>
/* ... CSS của bạn ... */
.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }
.toolbar-panel { background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: flex-end; }
.btn-add-new { background: #20c997; color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 10px rgba(32, 201, 151, 0.3); transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; }
.btn-add-new:hover { background: #17a57a; transform: translateY(-2px); }
.table-panel { background: #ffffff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; margin-top: -15px; }
.custom-table th { color: #e91e63; font-weight: 700; padding: 10px 15px; text-align: left; border-bottom: 2px solid #fff0f5; text-transform: uppercase; font-size: 0.85rem; }
.custom-table tbody tr { background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.02); transition: 0.2s; }
.custom-table td { padding: 15px; vertical-align: middle; color: #555; border-top: 1px solid #fcfcfc; border-bottom: 1px solid #fcfcfc; }
.slide-preview { width: 120px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid #eee; }
.order-badge { background: #f0f0f0; padding: 4px 10px; border-radius: 4px; font-weight: bold; }
.status-active { color: #2e7d32; font-weight: 600; display: flex; align-items: center; gap: 5px;}
.status-active::before { content:''; width: 8px; height: 8px; background: #2e7d32; border-radius: 50%; display: inline-block; }
.status-locked { color: #888; font-weight: 600; display: flex; align-items: center; gap: 5px;}
.status-locked::before { content:''; width: 8px; height: 8px; background: #888; border-radius: 50%; display: inline-block; }
.action-btn { padding: 6px 12px; border-radius: 6px; display: inline-flex; text-decoration: none; margin-right: 8px; transition: all 0.2s; font-size: 0.9rem; font-weight: 600; }
.btn-edit { background: #fff0f5; color: #e91e63; border: 1px solid #ffe1ec; }
.btn-edit:hover { background: #e91e63; color: white; }
.btn-delete { background: #ffeded; color: #dc3545; border: 1px solid #ffcccc; }
.btn-delete:hover { background: #dc3545; color: white; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Trình Chiếu (Slideshow)</h1>

    <div class="toolbar-panel">
        <a href="slideshow_add.php" class="btn-add-new">
            <i class="fa-solid fa-plus"></i> Thêm Slide Mới
        </a>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="5%">STT</th>
                        <th width="15%">Hình ảnh</th>
                        <th width="25%">Tiêu đề</th>
                        <th width="25%">Liên kết (Link)</th>
                        <th width="10%">Trạng thái</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($slides)) {
                            foreach ($slides as $slide) {
                                $statusClass = $slide['trangThai'] == 1 ? 'status-active' : 'status-locked';
                                $statusText = $slide['trangThai'] == 1 ? 'Hiển thị' : 'Ẩn';
                                $imgPath = "../uploads/" . $slide['hinhAnh'];
                                
                                echo '<tr>';
                                echo '<td><span class="order-badge">' . $slide['thuTu'] . '</span></td>';
                                echo '<td><img src="' . $imgPath . '" class="slide-preview" alt="Slide"></td>';
                                echo '<td><strong>' . htmlspecialchars($slide['tieuDe']) . '</strong></td>';
                                echo '<td><small>' . htmlspecialchars($slide['linkAnh']) . '</small></td>';
                                echo '<td><span class="' . $statusClass . '">' . $statusText . '</span></td>';
                                echo '<td>';
                                echo '<a href="slideshow_add.php?id=' . $slide['maTC'] . '" class="action-btn btn-edit">Sửa</a>';
                                echo '<a href="slideshow_delete.php?id=' . $slide['maTC'] . '" class="action-btn btn-delete" onclick="return confirm(\'Xóa slide này?\')">Xóa</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Chưa có slide nào trong bảng trinhchieu.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>