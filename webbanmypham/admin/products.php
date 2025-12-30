<?php
// Tên file: admin/products.php (Quản lý Sản phẩm)

// Yêu cầu file sidebar.php (Giả định chứa session_start())
require("sidebar.php"); 
    
// Yêu cầu file kết nối database 
require("../config.php"); 

// KHAI BÁO BIẾN KIỂM TRA QUYỀN ADMIN (quyen = 1)
$isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

// Khởi tạo mảng products rỗng
$products = [];
$search_query = ""; // Biến lưu trữ từ khóa tìm kiếm
    
// ====================================================================
// 1. XỬ LÝ TÌM KIẾM
// ====================================================================
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// ====================================================================
// 2. TRUY VẤN CƠ SỞ DỮ LIỆU (Bảng: mathangsp - Giả định)
// ====================================================================
 
// Chỉ truy vấn khi kết nối thành công 
if (isset($conn) && $conn->connect_error === null) {
    
    // Đã thêm: moTa và ngayNhap vào truy vấn SELECT
    $sql = "SELECT 
                mh.maMH, mh.tenMH, mh.DonGia, mh.soLuongTon, mh.moTa, mh.hinhAnh, mh.trangThai,
                mh.ngayNhap, dm.tenDM, th.tenTH
            FROM mathang mh
            LEFT JOIN danhmucsp dm ON mh.maDM = dm.maDM
            LEFT JOIN thuonghieu th ON mh.maTH = th.maTH"; 
    
    // Thêm điều kiện tìm kiếm nếu có
    if (!empty($search_query)) {
        // Sử dụng Prepared Statement để bảo mật
        $sql .= " WHERE mh.tenMH LIKE ? OR mh.maMH LIKE ?";
    }
    
    $sql .= " ORDER BY mh.maMH DESC";

    // 2.2. Chuẩn bị và thực thi truy vấn
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        if (!empty($search_query)) {
            $search_param = "%" . $search_query . "%";
            $stmt->bind_param("ss", $search_param, $search_param);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        $stmt->close();
    } 
}
// Ghi chú: Nếu kết nối thất bại, $products sẽ là mảng rỗng.
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* --- GIỮ NGUYÊN CSS KHUNG SƯỜN --- */
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
    transition: 0.3s;
    background: #fff;
}

.search-group:focus-within {
    border-color: #e91e63;
    box-shadow: 0 0 5px rgba(233, 30, 99, 0.2);
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
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-search:hover { background: #c2185b; }

.btn-add-new {
    background: #20c997; 
    color: white;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(32, 201, 151, 0.3);
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-add-new:hover { background: #17a57a; transform: translateY(-2px); }

/* --- TABLE --- */
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

.custom-table tbody tr {
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    transition: 0.2s;
}

.custom-table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08);
}

.custom-table td {
    padding: 15px;
    vertical-align: middle;
    color: #555;
    border-top: 1px solid #fcfcfc;
    border-bottom: 1px solid #fcfcfc;
}

.custom-table td:first-child { border-left: 1px solid #fcfcfc; border-radius: 10px 0 0 10px; }
.custom-table td:last-child { border-right: 1px solid #fcfcfc; border-radius: 0 10px 10px 0; }

/* --- PRODUCT ELEMENTS --- */

/* Badge Mã MH */
.product-code-badge {
    font-family: 'Consolas', monospace;
    background: #fff0f5;
    color: #d81b60;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px dashed #e91e63;
}

/* Ảnh Sản phẩm */
.product-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #eee;
}

/* Thông tin Sản phẩm */
.product-name-text {
    font-weight: 600;
    color: #333;
    display: block;
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Giá */
.price-text {
    font-weight: 700;
    color: #20c997; /* Màu xanh lá cây cho giá */
}

/* Tồn kho */
.stock-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
}

.stock-high { background: #e6f7e8; color: #2e7d32; }
.stock-low { background: #fff0e6; color: #EF6C00; }
.stock-out { background: #fef0f0; color: #c62828; }

/* Trạng thái */
.status-active { color: #2e7d32; font-weight: 600; font-size: 0.85rem; }
.status-hidden { color: #888; font-weight: 500; font-size: 0.85rem; }

/* Các nút Hành động */
.action-btn {
    padding: 6px 12px; 
    border-radius: 6px; 
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    margin-right: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
    font-size: 0.9rem;
    font-weight: 600;
}

.btn-edit { 
    background: #fff0f5;
    color: #e91e63;
    border: 1px solid #ffe1ec;
}
.btn-edit:hover { 
    background: #e91e63;
    color: white; 
    transform: translateY(-1px);
}

.btn-delete { 
    background: #ffeded;
    color: #dc3545;
    border: 1px solid #ffcccc;
}
.btn-delete:hover { 
    background: #dc3545;
    color: white; 
    transform: translateY(-1px);
}

.action-column {
    min-width: 140px;
}
.description-text {
    font-size: 0.9rem;
    color: #666;
    max-width: 250px; 
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
    -webkit-box-orient: vertical;
}
</style>

<div class="main-content">
    <h1 class="dashboard-title"><i class="fa-solid fa-bottle-droplet"></i> Quản Lý Sản Phẩm</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm Mã MH, Tên MH..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <a href="products_add.php" class="btn-add-new">
            <i class="fa-solid fa-plus"></i> Thêm Mặt Hàng
        </a>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="8%">Mã MH</th>
                        <th width="15%">Tên mặt hàng </th>
                        <th width="8%">Giá</th>
                        <th width="8%">Tồn Kho</th>
                        <th width="15%">Mô tả</th> 
                        <th width="5%">Ảnh</th> 
                        <th width="8%">Danh mục</th>
                        <th width="8%">Thương hiệu</th>
                        <th width="8%">Ngày nhập</th>
                        <th width="8%">Trạng thái</th>
                        <th class="action-column" width="10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // ====================================================================
                    // 3. TẠO HÀNG DỮ LIỆU ĐỘNG TỪ KẾT QUẢ TRUY VẤN
                    // ====================================================================

                    if (!empty($products)) {
                        foreach ($products as $prod) {
                            
                            // Xử lý tồn kho
                            if ($prod['soLuongTon'] > 50) {
                                $stockClass = 'stock-high';
                                $stockText = 'Cao';
                            } elseif ($prod['soLuongTon'] > 0) {
                                $stockClass = 'stock-low';
                                $stockText = 'Thấp';
                            } else {
                                $stockClass = 'stock-out';
                                $stockText = 'Hết hàng';
                            }
                            
                            // Xử lý trạng thái (Giả định 1: Hiển thị, 0: Ẩn/Ngừng bán)
                            $statusClass = $prod['trangThai'] == 1 ? 'status-active' : 'status-hidden';
                            $statusText = $prod['trangThai'] == 1 ? 'Hiển thị' : 'Ẩn';

                            echo '<tr>';
                            
                            // 1. Cột Mã MH
                            echo '<td><span class="product-code-badge">' . htmlspecialchars($prod['maMH']) . '</span></td>';
                            
                            // 2. Cột Tên Sản Phẩm
                            echo '<td><span class="product-name-text" title="' . htmlspecialchars($prod['tenMH']) . '">' . htmlspecialchars($prod['tenMH']) . '</span></td>';
                            
                            // 3. Cột Đơn Giá
                            echo '<td><span class="price-text">' . number_format($prod['DonGia'], 0, ',', '.') . 'đ</span></td>';
                            
                            // 4. Cột Tồn Kho
                            echo '<td><span class="stock-badge ' . $stockClass . '">' . htmlspecialchars($prod['soLuongTon']) . '</span></td>';
                            
                            // 5. Cột Mô tả (Mới)
                            echo '<td><div class="description-text">' . htmlspecialchars($prod['moTa'] ?? '') . '</div></td>';
                            
                            // 6. Cột Ảnh (Mới)
                            // Sử dụng URL ảnh trực tiếp từ DB
                            echo '<td><img src="' . htmlspecialchars($prod['hinhAnh'] ?? 'default_url') . '" class="product-thumb" alt="Ảnh sản phẩm"></td>';
                            
                            // 7. Cột Danh mục
                            echo '<td>' . htmlspecialchars($prod['tenDM'] ?? 'N/A') . '</td>';
                            
                            // 8. Cột Thương hiệu
                            echo '<td>' . htmlspecialchars($prod['tenTH'] ?? 'N/A') . '</td>';

                            // 9. Cột Ngày nhập (Mới)
                            // Giả định ngày nhập là Y-m-d H:i:s, chỉ hiển thị ngày
                            $ngayNhapFormatted = date('Y-m-d', strtotime($prod['ngayNhap'] ?? 'N/A'));
                            echo '<td>' . $ngayNhapFormatted . '</td>';
                            
                            // 10. Cột Trạng thái
                            echo '<td><span class="' . $statusClass . '">' . $statusText . '</span></td>';
                            
                            // 11. CỘT HÀNH ĐỘNG 
                            echo '<td>';
                            
                            // Nút Sửa
                            echo '<a href="products_add.php?maMH=' . htmlspecialchars($prod['maMH']) . '" class="action-btn btn-edit" title="Sửa">Sửa</a>';
                            
                            // Nút Xóa
                            echo '<a href="products_delete.php?maMH=' . htmlspecialchars($prod['maMH']) . '" class="action-btn btn-delete" title="Xóa" onclick="return confirm(\'Bạn có chắc chắn muốn xóa sản phẩm \\\\\\\'' . addslashes($prod['tenMH']) . '\\\\\\\'' . ' không?\')">Xóa</a>';

                            echo '</td>';
                            
                            echo '</tr>';
                        }
                    } else {
                        // Nếu không có dữ liệu
                        $error_message = empty($search_query) ? "Không tìm thấy sản phẩm nào trong hệ thống." : "Không tìm thấy sản phẩm nào cho từ khóa: **" . htmlspecialchars($search_query) . "**";
                        
                        // Cập nhật colspan lên 11 (tổng số cột)
                        echo '<tr><td colspan="11" style="text-align: center; padding: 30px; color: #888;">' . $error_message . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>