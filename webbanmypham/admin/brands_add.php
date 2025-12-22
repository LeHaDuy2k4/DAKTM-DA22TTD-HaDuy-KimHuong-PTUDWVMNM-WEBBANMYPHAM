<?php 
    // Yêu cầu file sidebar.php (Giả định chứa session_start())
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
    require("../config.php"); 
    
    // --- 1. KHAI BÁO MẶC ĐỊNH ---
    $brand = [
        'maTH' => '',
        'tenTH' => '',
        'quocGia' => '', // Thêm trường Quốc Gia
        'moTa' => '',
    ];
    
    // --- 2. KIỂM TRA CHẾ ĐỘ SỬA ---
    // Kiểm tra xem có maTH trên URL không (ví dụ: brands_add.php?maTH=1)
    $isEditMode = isset($_GET['maTH']) && !empty($_GET['maTH']);
    
    if ($isEditMode) {
        // Lấy mã thương hiệu an toàn
        $targetMaTH = $conn->real_escape_string($_GET['maTH']);
        
        // Truy vấn CSDL bảng 'thuonghieu' để lấy thông tin cũ
        $sql = "SELECT maTH, tenTH, quocGia, moTa 
                FROM thuonghieu 
                WHERE maTH = '$targetMaTH'";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Gán dữ liệu từ DB vào biến $brand để hiển thị lên form
            $brand = $result->fetch_assoc();
        } else {
            // Nếu mã thương hiệu không tồn tại trong DB -> Chuyển hướng báo lỗi
            header("Location: brands.php?error=" . urlencode("Không tìm thấy thương hiệu cần sửa."));
            exit();
        }
    }
    
    // Cập nhật tiêu đề và nút bấm hiển thị động theo chế độ
    $pageTitle = $isEditMode ? "Sửa Thương Hiệu: " . htmlspecialchars($brand['tenTH']) : "Thêm Thương Hiệu Mới";
    $submitButtonText = $isEditMode ? "Cập Nhật Thương Hiệu" : "Thêm Thương Hiệu";
    $submitButtonClass = $isEditMode ? "btn-edit-item" : "btn-save-item";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* ========================================================================= */
/* --- STYLE CHUNG (Đồng bộ với Admin Dashboard) --- */
/* ========================================================================= */
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

/* --- FORM PANEL --- */
.form-panel {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    max-width: 800px;
    margin: 0 auto;
}

/* --- FORM FIELDS --- */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
    font-size: 0.95rem;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group input[type="email"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ffe1ec;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s;
    background-color: #fcfcfc;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #e91e63;
    box-shadow: 0 0 5px rgba(233, 30, 99, 0.2);
    outline: none;
}

/* Cấu trúc chia cột cho Tên và Quốc gia */
.form-row {
    display: flex;
    gap: 20px;
}

.form-column {
    flex: 1;
}

/* --- BUTTONS --- */
.btn-action {
    padding: 12px 25px;
    border: none;
    border-radius: 30px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none; /* Áp dụng cho cả <a> và <button> */
}

/* Nút Thêm/Lưu (Màu Xanh lá) */
.btn-save-item {
    background: #20c997; 
    color: white;
}
.btn-save-item:hover { 
    background: #17a57a; 
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(32, 201, 151, 0.4);
}

/* Nút Cập nhật (Màu Hồng/Đỏ) */
.btn-edit-item {
    background: #e91e63; 
    color: white;
}
.btn-edit-item:hover { 
    background: #d63384; 
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(233, 30, 99, 0.4);
}

/* Nút Quay lại (Màu Xám) */
.btn-back {
    background: #f0f0f0;
    color: #555;
    margin-right: 15px;
}
.btn-back:hover {
    background: #e0e0e0;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}
</style>

<div class="main-content">
    <h1 class="dashboard-title"><i class="fa-solid fa-tags"></i> <?php echo $pageTitle; ?></h1>

    <div class="form-panel">
        <!-- Action trỏ về save_brands.php để xử lý lưu/sửa -->
        <form action="save_brands.php" method="POST">
            
            <?php if ($isEditMode): ?>
                <!-- QUAN TRỌNG: Chế độ Sửa -->
                <!-- action_type = edit báo cho file xử lý biết là đang cập nhật -->
                <input type="hidden" name="action_type" value="edit">
                <!-- Gửi maTH cũ đi để biết dòng nào trong DB cần sửa -->
                <input type="hidden" name="maTH" value="<?php echo htmlspecialchars($brand['maTH']); ?>">
            <?php else: ?>
                <!-- QUAN TRỌNG: Chế độ Thêm mới -->
                <input type="hidden" name="action_type" value="add">
            <?php endif; ?>

            <!-- TRƯỜNG MÃ THƯƠNG HIỆU (CHỈ HIỂN THỊ ĐỂ XEM KHI SỬA) -->
            <?php if ($isEditMode): ?>
                <div class="form-group">
                    <label for="displayMaTH">Mã Thương Hiệu</label>
                    <input type="text" id="displayMaTH" 
                           value="<?php echo htmlspecialchars($brand['maTH']); ?>" readonly 
                           style="background-color: #eee; font-weight: 600; color: #333;">
                    <small style="color: #888;">(Bạn không thể thay đổi Mã thương hiệu)</small>
                </div>
            <?php endif; ?>

            <div class="form-row">
                <!-- TRƯỜNG TÊN THƯƠNG HIỆU -->
                <div class="form-group form-column">
                    <label for="tenTH">Tên Thương Hiệu *</label>
                    <input type="text" id="tenTH" name="tenTH" 
                           value="<?php echo htmlspecialchars($brand['tenTH']); ?>" 
                           required placeholder="Ví dụ: L'Oréal, The Ordinary, ...">
                </div>
                
                <!-- TRƯỜNG QUỐC GIA -->
                <div class="form-group form-column">
                    <label for="quocGia">Quốc Gia *</label>
                    <input type="text" id="quocGia" name="quocGia" 
                           value="<?php echo htmlspecialchars($brand['quocGia']); ?>" 
                           required placeholder="Ví dụ: Pháp, Hàn Quốc, Mỹ, ...">
                </div>
            </div>


            <!-- TRƯỜNG MÔ TẢ -->
            <div class="form-group">
                <label for="moTa">Mô tả chi tiết</label>
                <textarea id="moTa" name="moTa" rows="4" 
                          placeholder="Giới thiệu về nguồn gốc, triết lý và thế mạnh của thương hiệu này."><?php echo htmlspecialchars($brand['moTa']); ?></textarea>
            </div>
            
            <!-- HÀNG NÚT HÀNH ĐỘNG -->
            <div style="margin-top: 30px; text-align: right;">
                <a href="brands.php" class="btn-action btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                
                <button type="submit" class="btn-action <?php echo $submitButtonClass; ?>">
                    <i class="fa-solid fa-floppy-disk"></i> <?php echo $submitButtonText; ?>
                </button>
            </div>

        </form>
    </div>
</div>