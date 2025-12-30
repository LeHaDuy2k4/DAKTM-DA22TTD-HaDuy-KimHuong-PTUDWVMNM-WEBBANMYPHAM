<?php 
    // Yêu cầu file sidebar.php (Giả định chứa session_start())
    // Giả định file sidebar.php nằm cùng thư mục admin/
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database (Đảm bảo file config.php nằm ở thư mục gốc (../))
    require("../config.php"); 

    // KHAI BÁO BIẾN KIỂM TRA QUYỀN ADMIN (quyen = 1)
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    // Khởi tạo mảng brands rỗng
    $brands = [];
    $search_query = ""; // Biến lưu trữ từ khóa tìm kiếm
    
    // ====================================================================
    // 1. XỬ LÝ TÌM KIẾM
    // ====================================================================
    if (isset($_GET['search'])) {
        $search_query = trim($_GET['search']);
    }

    // ====================================================================
    // 2. TRUY VẤN CƠ SỞ DỮ LIỆU
    // ====================================================================
    
    // Chỉ truy vấn khi kết nối thành công 
    if (isset($conn) && $conn->connect_error === null) {
        
        // 2.1. Xây dựng câu truy vấn SQL (Giả định tên bảng là 'thuonghieu' và các cột: maTH, tenTH, quocGia, moTa)
        $sql = "SELECT maTH, tenTH, quocGia, moTa FROM thuonghieu"; 
        
        // Thêm điều kiện tìm kiếm nếu có
        if (!empty($search_query)) {
            // Sử dụng LIKE để tìm kiếm gần đúng trong tenTH, quocGia và moTa
            $sql .= " WHERE tenTH LIKE ? OR quocGia LIKE ? OR moTa LIKE ?";
        }
        
        // Sắp xếp theo mã thương hiệu
        $sql .= " ORDER BY maTH ASC";

        // 2.2. Chuẩn bị và thực thi truy vấn
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            if (!empty($search_query)) {
                $search_param = "%" . $search_query . "%";
                // Binding 3 tham số: tenTH, quocGia, moTa
                $stmt->bind_param("sss", $search_param, $search_param, $search_param);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $brands[] = $row; // Lưu kết quả vào mảng brands
                }
            }
            $stmt->close();
        } else {
             // Ghi chú: Có thể thêm logic xử lý lỗi prepare statement tại đây nếu cần.
        }
    }
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* --- GIỮ NGUYÊN CSS KHUNG SƯỜN & ÁP DỤNG CÁC CLASS LIÊN QUAN --- */

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
    max-width: 450px; /* Tăng chiều rộng tìm kiếm */
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

/* Các style tương ứng cho Mã */
.brand-code-badge { /* Đã đổi tên class */
    font-family: 'Consolas', monospace;
    background: #f0f8ff; /* Màu xanh nhạt */
    color: #007bff; /* Màu xanh đậm */
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px dashed #cce5ff;
}

.brand-name-text { /* Đã đổi tên class */
    font-weight: 600;
    color: #333;
}

.description-text {
    font-size: 0.9rem;
    color: #666;
    /* Giới hạn chiều dài mô tả */
    max-width: 300px; 
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
    -webkit-box-orient: vertical;
}

/* Style cho nút Hành động */
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
</style>

<div class="main-content">
    <h1 class="dashboard-title"><i class="fa-solid fa-tags"></i> Quản Lý Thương Hiệu</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm tên thương hiệu, quốc gia, mô tả..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <!-- Link tới trang thêm mới thương hiệu -->
        <a href="brands_add.php" class="btn-add-new">
            <i class="fa-solid fa-plus"></i> Thêm Thương Hiệu
        </a>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="10%">Mã TH</th>
                        <th width="20%">Tên Thương Hiệu</th>
                        <th width="15%">Quốc Gia</th>
                        <th width="35%">Mô tả</th>
                        <th class="action-column" width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // ====================================================================
                        // 3. TẠO HÀNG DỮ LIỆU ĐỘNG TỪ KẾT QUẢ TRUY VẤN
                        // ====================================================================

                        if (!empty($brands)) { // $brands là mảng được lấy từ truy vấn DB
                            foreach ($brands as $brand) {
                                
                                // Bắt đầu hàng dữ liệu
                                echo '<tr>';
                                
                                // Cột Mã Thương Hiệu
                                echo '<td><span class="brand-code-badge">' . htmlspecialchars($brand['maTH']) . '</span></td>';
                                
                                // Cột Tên Thương Hiệu
                                echo '<td><span class="brand-name-text">' . htmlspecialchars($brand['tenTH']) . '</span></td>';
                                
                                // Cột Quốc Gia
                                echo '<td>' . htmlspecialchars($brand['quocGia']) . '</td>';
                                
                                // Cột Mô tả
                                echo '<td><div class="description-text">' . htmlspecialchars($brand['moTa']) . '</div></td>';
                                
                                // CỘT HÀNH ĐỘNG 
                                echo '<td>';
                                
                                // Nút Sửa (Link giả định)
                                echo '<a href="brands_add.php?maTH=' . htmlspecialchars($brand['maTH']) . '" class="action-btn btn-edit" title="Sửa">Sửa</a>';
                                
                                // Nút Xóa (Link giả định)
                                $delete_msg = 'Bạn có chắc chắn muốn xóa thương hiệu \'' . addslashes($brand['tenTH']) . '\' không? Tất cả sản phẩm thuộc thương hiệu này có thể bị ảnh hưởng.';
                                echo '<a href="brands_delete.php?maTH=' . htmlspecialchars($brand['maTH']) . '" class="action-btn btn-delete" title="Xóa" onclick="return confirm(\'' . $delete_msg . '\')">Xóa</a>';

                                echo '</td>';
                                
                                echo '</tr>';
                            }
                        } else {
                            // Nếu không có dữ liệu hoặc kết nối thất bại
                            $db_error = isset($conn) && $conn->connect_error !== null ? $conn->connect_error : "Không có kết quả.";
                            if (!isset($conn)) $db_error = "Lỗi: Biến kết nối \$conn không tồn tại.";

                            if (!empty($search_query)) {
                                $message = "Không tìm thấy thương hiệu nào cho từ khóa: **" . htmlspecialchars($search_query) . "**";
                            } else {
                                $message = "Không tìm thấy thương hiệu nào trong hệ thống. (Lỗi DB: " . $db_error . ")";
                            }

                            echo '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #888;">' . $message . '</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>