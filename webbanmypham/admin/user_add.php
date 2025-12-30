<?php 
    // Yêu cầu file sidebar.php
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
    require("../config.php"); 
    
    // Khai báo mặc định cho người dùng mới
    $user = [
        'tenDangnhap' => '',
        'hoTen' => '',
        'email' => '',
        'dienThoai' => '',
        'diaChi' => '',
        'gioiTinh' => 0, // 0: Nữ (mặc định)
        'quyen' => 0, // 0: User (mặc định)
        'trangThai' => 1, // 1: Hoạt động (mặc định)
    ];
    
    // Kiểm tra chế độ SỬA
    // Giả định ID là tên đăng nhập (string)
    $isEditMode = isset($_GET['id']) && !empty($_GET['id']);
    
    if ($isEditMode) {
        $targetUsername = $conn->real_escape_string($_GET['id']);
        
        // Truy vấn CSDL để lấy thông tin người dùng cần sửa
        $sql = "SELECT tenDangnhap, hoTen, gioiTinh, email, dienThoai, diaChi, quyen, trangThai 
                FROM nguoidung 
                WHERE tenDangnhap = '$targetUsername'";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Gán dữ liệu thật vào biến $user
            $fetched_user = $result->fetch_assoc();
            
            // Đảm bảo dữ liệu số được ép kiểu chính xác
            $fetched_user['quyen'] = (int)$fetched_user['quyen'];
            $fetched_user['trangThai'] = (int)$fetched_user['trangThai'];
            $fetched_user['gioiTinh'] = (int)$fetched_user['gioiTinh'];
            
            $user = $fetched_user; // Cập nhật $user với dữ liệu fetch được
            
        } else {
            // Xử lý nếu không tìm thấy người dùng
            header("Location: users.php?error=" . urlencode("Không tìm thấy người dùng cần sửa."));
            exit();
        }
    }
    
    // Cập nhật tiêu đề và nút bấm dựa trên chế độ
    $pageTitle = $isEditMode ? "Sửa Thông Tin Người Dùng: " . htmlspecialchars($user['tenDangnhap']) : "Thêm Người Dùng Mới";
    $submitButtonText = $isEditMode ? "Cập Nhật" : "Thêm Người Dùng";
    $submitButtonClass = $isEditMode ? "btn-edit-user" : "btn-save-user";
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

/* Chia đôi trường nhập liệu */
.form-row {
    display: flex;
    gap: 20px;
}
.form-row .form-group {
    flex: 1;
}

/* --- CHECKBOX/RADIO STYLES --- */
.radio-group {
    display: flex;
    gap: 20px;
    padding: 10px 0;
}
.radio-group label {
    font-weight: normal; /* Giảm trọng lượng font cho label radio */
}
.radio-group input[type="radio"] {
    margin-right: 5px;
    width: auto;
}

/* --- BUTTONS --- */
.btn-action {
    padding: 12px 25px;
    border: none;
    border-radius: 30px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    /* Đã loại bỏ display: inline-flex và gap: 8px */
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    font-size: 1rem;
}

/* Nút Thêm/Lưu (Màu Xanh lá) */
.btn-save-user {
    background: #20c997; 
    color: white;
}
.btn-save-user:hover { 
    background: #17a57a; 
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(32, 201, 151, 0.4);
}

/* Nút Cập nhật (Màu Hồng/Đỏ) */
.btn-edit-user {
    background: #e91e63; 
    color: white;
}
.btn-edit-user:hover { 
    background: #d63384; 
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(233, 30, 99, 0.4);
}

/* Nút Quay lại (Màu Xám) */
.btn-back {
    background: #f0f0f0;
    color: #555;
    text-decoration: none;
    margin-right: 15px;
}
.btn-back:hover {
    background: #e0e0e0;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}
</style>

<div class="main-content">
    <h1 class="dashboard-title"><?php echo $pageTitle; ?></h1>

    <div class="form-panel">
        <!-- Đặt action về save_user.php để xử lý CSDL -->
        <form action="save_user.php" method="POST">
            
            <?php if ($isEditMode): ?>
                <!-- Hidden field: Gửi tên đăng nhập cũ để biết người dùng nào cần sửa -->
                <input type="hidden" name="action_type" value="edit">
                <input type="hidden" name="tenDangnhap_old" value="<?php echo htmlspecialchars($user['tenDangnhap']); ?>">
            <?php else: ?>
                <input type="hidden" name="action_type" value="add">
            <?php endif; ?>

            <!-- Hàng 1: Tên đăng nhập & Họ tên -->
            <div class="form-row">
                <div class="form-group">
                    <label for="tenDangnhap">Tên đăng nhập *</label>
                    <input type="text" id="tenDangnhap" name="tenDangnhap" 
                           value="<?php echo htmlspecialchars($user['tenDangnhap']); ?>" required 
                           <?php echo $isEditMode ? 'readonly style="background-color: #eee;"' : ''; ?>>
                    <?php if ($isEditMode): ?>
                        <small style="color: #888;">(Không thể sửa tên đăng nhập)</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="hoTen">Họ và Tên</label>
                    <input type="text" id="hoTen" name="hoTen" 
                           value="<?php echo htmlspecialchars($user['hoTen']); ?>">
                </div>
            </div>

            <!-- Hàng 2: Email & Mật khẩu (hoặc Đặt lại MK) -->
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="matKhau"><?php echo $isEditMode ? 'Mật khẩu (Để trống nếu không đổi)' : 'Mật khẩu *'; ?></label>
                    <input type="password" id="matKhau" name="matKhau" 
                           <?php echo !$isEditMode ? 'required' : ''; ?>>
                </div>
            </div>

            <!-- Hàng 3: Điện thoại & Địa chỉ -->
            <div class="form-row">
                <div class="form-group">
                    <label for="dienThoai">Điện thoại</label>
                    <input type="text" id="dienThoai" name="dienThoai" 
                           value="<?php echo htmlspecialchars($user['dienThoai']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="diaChi">Địa chỉ</label>
                    <input type="text" id="diaChi" name="diaChi" 
                           value="<?php echo htmlspecialchars($user['diaChi']); ?>">
                </div>
            </div>
            
            <!-- Hàng 4: Giới tính -->
            <div class="form-row">
                <div class="form-group">
                    <label>Giới tính</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gioiTinh" value="1" 
                                <?php echo $user['gioiTinh'] == 1 ? 'checked' : ''; ?>> Nam
                        </label>
                        <label>
                            <input type="radio" name="gioiTinh" value="0" 
                                <?php echo $user['gioiTinh'] == 0 ? 'checked' : ''; ?>> Nữ
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <!-- Để trống để giữ cân bằng form-row -->
                </div>
            </div>

            <!-- Hàng 5: Quyền & Trạng thái -->
            <div class="form-row">
                <div class="form-group">
                    <label>Phân quyền *</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="quyen" value="1" 
                                <?php echo $user['quyen'] == 1 ? 'checked' : ''; ?>> Admin
                        </label>
                        <label>
                            <input type="radio" name="quyen" value="0" 
                                <?php echo $user['quyen'] == 0 ? 'checked' : ''; ?>> Người dùng
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng thái *</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="trangThai" value="1" 
                                <?php echo $user['trangThai'] == 1 ? 'checked' : ''; ?>> Hoạt động
                        </label>
                        <label>
                            <input type="radio" name="trangThai" value="0" 
                                <?php echo $user['trangThai'] == 0 ? 'checked' : ''; ?>> Khóa
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- HÀNG NÚT HÀNH ĐỘNG -->
            <div style="margin-top: 30px; text-align: right;">
                <a href="users.php" class="btn-action btn-back">
                    Quay lại
                </a>
                
                <button type="submit" class="btn-action <?php echo $submitButtonClass; ?>">
                    <?php echo $submitButtonText; ?>
                </button>
            </div>

        </form>
    </div>
</div>