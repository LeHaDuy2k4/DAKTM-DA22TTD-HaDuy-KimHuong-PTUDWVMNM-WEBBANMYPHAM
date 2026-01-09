<?php 
    // Yêu cầu các file hệ thống
    require("sidebar.php"); 
    require("../config.php"); 
    
    // 1. KHỞI TẠO GIÁ TRỊ MẶC ĐỊNH
    $promo = [
        'maKM' => '',
        'tenKM' => '',
        'phantramgiam' => 0,
        'ngayBD' => date('Y-m-d'),
        'ngayKT' => date('Y-m-d', strtotime('+7 days')),
    ];
    
    // 2. KIỂM TRA CHẾ ĐỘ SỬA
    $isEditMode = isset($_GET['id']) && !empty($_GET['id']);
    if ($isEditMode) {
        $targetID = (int)$_GET['id'];
        $sql = "SELECT maKM, tenKM, phantramgiam, ngayBD, ngayKT FROM khuyenmai WHERE maKM = $targetID";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $promo = $result->fetch_assoc();
        } else {
            header("Location: promotions.php?error=" . urlencode("Không tìm thấy mã khuyến mãi."));
            exit();
        }
    }
    
    $pageTitle = $isEditMode ? "Sửa Chương Trình: " . htmlspecialchars($promo['tenKM']) : "Thêm Khuyến Mãi Mới";
    $submitButtonText = $isEditMode ? "Cập Nhật" : "Lưu Khuyến Mãi";
    $submitButtonClass = $isEditMode ? "btn-edit-user" : "btn-save-user";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    /* CSS đồng bộ với Admin Dashboard */
    .main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
    .dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }
    .form-panel { background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 0.95rem; }
    .form-group input { width: 100%; padding: 12px; border: 1px solid #ffe1ec; border-radius: 8px; box-sizing: border-box; font-size: 1rem; transition: 0.3s; background-color: #fcfcfc; }
    .form-group input:focus { border-color: #e91e63; box-shadow: 0 0 5px rgba(233, 30, 99, 0.2); outline: none; }
    .form-row { display: flex; gap: 20px; }
    .form-row .form-group { flex: 1; }
    .btn-action { padding: 12px 25px; border: none; border-radius: 30px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 1rem; }
    .btn-save-user { background: #20c997; color: white; }
    .btn-save-user:hover { background: #17a57a; transform: translateY(-1px); }
    .btn-edit-user { background: #e91e63; color: white; }
    .btn-edit-user:hover { background: #d63384; transform: translateY(-1px); }
    .btn-back { background: #f0f0f0; color: #555; text-decoration: none; margin-right: 15px; }
    .promo-badge { background: #fff0f5; color: #d81b60; padding: 5px 12px; border-radius: 6px; font-weight: 700; border: 1px dashed #e91e63; margin-bottom: 20px; display: inline-block; }
    .info-box { background: #e7f3ff; color: #0c5460; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border-left: 5px solid #007bff; }
</style>

<div class="main-content">
    <h1 class="dashboard-title"><?php echo $pageTitle; ?></h1>

    <div class="form-panel">
        <div class="info-box">
            <i class="fa-solid fa-circle-info"></i> 
            Mẹo: Sau khi tạo mã khuyến mãi, bạn có thể gán nó cho từng mặt hàng hoặc áp dụng hàng loạt theo thương hiệu tại trang quản lý sản phẩm.
        </div>

        <form action="save_promotion.php" method="POST">
            <input type="hidden" name="action_type" value="<?php echo $isEditMode ? 'edit' : 'add'; ?>">
            <?php if ($isEditMode): ?>
                <input type="hidden" name="maKM" value="<?php echo $promo['maKM']; ?>">
                <div class="promo-badge">MÃ KHUYẾN MÃI: KM<?php echo str_pad($promo['maKM'], 3, '0', STR_PAD_LEFT); ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="tenKM">Tên chương trình khuyến mãi *</label>
                <input type="text" id="tenKM" name="tenKM" placeholder="Ví dụ: Giảm giá mùa hè 2026"
                       value="<?php echo htmlspecialchars($promo['tenKM']); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phantramgiam">Phần trăm giảm (%) *</label>
                    <input type="number" step="0.01" min="0" max="100" id="phantramgiam" name="phantramgiam" 
                           value="<?php echo (float)$promo['phantramgiam']; ?>" required>
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <small style="color: #888; display: block; padding-top: 10px;">(Giá trị giảm từ 0% đến 100%)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="ngayBD">Ngày bắt đầu *</label>
                    <input type="date" id="ngayBD" name="ngayBD" 
                           value="<?php echo $promo['ngayBD']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="ngayKT">Ngày kết thúc *</label>
                    <input type="date" id="ngayKT" name="ngayKT" 
                           value="<?php echo $promo['ngayKT']; ?>" required>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right;">
                <a href="promotions.php" class="btn-action btn-back">Hủy bỏ</a>
                <button type="submit" class="btn-action <?php echo $submitButtonClass; ?>">
                    <i class="fa-solid fa-check"></i> <?php echo $submitButtonText; ?>
                </button>
            </div>
        </form>
    </div>
</div>