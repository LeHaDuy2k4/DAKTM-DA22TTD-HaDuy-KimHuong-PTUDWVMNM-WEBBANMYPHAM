<?php 
    // Yêu cầu file sidebar.php
    require("sidebar.php"); 
    
    // Yêu cầu file kết nối database
    require("../config.php"); 
    
    // Khai báo mặc định cho slide mới
    $slide = [
        'maTC' => '',
        'tieuDe' => '',
        'hinhAnh' => '',
        'linkAnh' => '',
        'thuTu' => 0,
        'trangThai' => 1, 
    ];
    
    // Kiểm tra chế độ SỬA
    $isEditMode = isset($_GET['id']) && !empty($_GET['id']);
    
    if ($isEditMode) {
        $targetId = (int)$conn->real_escape_string($_GET['id']);
        
        // TRUY VẤN TỪ BẢNG: trinhchieu
        $sql = "SELECT * FROM trinhchieu WHERE maTC = $targetId";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $slide = $result->fetch_assoc();
            $slide['thuTu'] = (int)$slide['thuTu'];
            $slide['trangThai'] = (int)$slide['trangThai'];
        } else {
            header("Location: slideshow.php?error=" . urlencode("Không tìm thấy slide cần sửa."));
            exit();
        }
    }
    
    $pageTitle = $isEditMode ? "Sửa Slide: #" . $slide['maTC'] : "Thêm Slide Trình Chiếu Mới";
    $submitButtonText = $isEditMode ? "Cập Nhật Slide" : "Thêm Slide";
    $submitButtonClass = $isEditMode ? "btn-edit-slide" : "btn-save-slide";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }
.form-panel { background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 0.95rem; }
.form-group input[type="text"], .form-group input[type="number"], .form-group input[type="file"] {
    width: 100%; padding: 12px; border: 1px solid #ffe1ec; border-radius: 8px; box-sizing: border-box; font-size: 1rem; background-color: #fcfcfc;
}
.form-row { display: flex; gap: 20px; }
.form-row .form-group { flex: 1; }
.radio-group { display: flex; gap: 20px; padding: 10px 0; }
.image-preview { margin-top: 10px; width: 200px; height: 100px; border-radius: 8px; object-fit: cover; border: 1px solid #ffe1ec; display: block; }
.btn-action { padding: 12px 25px; border: none; border-radius: 30px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 1rem; }
.btn-save-slide { background: #20c997; color: white; }
.btn-edit-slide { background: #e91e63; color: white; }
.btn-back { background: #f0f0f0; color: #555; text-decoration: none; margin-right: 15px; }
</style>

<div class="main-content">
    <h1 class="dashboard-title"><?php echo $pageTitle; ?></h1>
    <div class="form-panel">
        <form action="save_slideshow.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="action_type" value="<?php echo $isEditMode ? 'edit' : 'add'; ?>">
            <?php if ($isEditMode): ?>
                <input type="hidden" name="maTC" value="<?php echo $slide['maTC']; ?>">
                <input type="hidden" name="hinhAnh_old" value="<?php echo $slide['hinhAnh']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="tieuDe">Tiêu đề Slide *</label>
                <input type="text" id="tieuDe" name="tieuDe" value="<?php echo htmlspecialchars($slide['tieuDe']); ?>" required>
            </div>

            <div class="form-group">
                <label for="linkAnh">Liên kết (URL)</label>
                <input type="text" id="linkAnh" name="linkAnh" value="<?php echo htmlspecialchars($slide['linkAnh']); ?>">
            </div>

            <div class="form-group">
                <label for="hinhAnh">Hình ảnh Slide <?php echo $isEditMode ? '(Chọn nếu muốn thay đổi)' : '*'; ?></label>
                <input type="file" id="hinhAnh" name="hinhAnh" accept="image/*" <?php echo !$isEditMode ? 'required' : ''; ?>>
                <?php if ($isEditMode && !empty($slide['hinhAnh'])): ?>
                    <p style="font-size: 0.85rem; color: #888; margin-top: 10px;">Ảnh hiện tại:</p>
                    <img src="../uploads/<?php echo $slide['hinhAnh']; ?>" class="image-preview">
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="thuTu">Thứ tự hiển thị</label>
                    <input type="number" id="thuTu" name="thuTu" value="<?php echo $slide['thuTu']; ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Trạng thái hiển thị</label>
                    <div class="radio-group">
                        <label><input type="radio" name="trangThai" value="1" <?php echo $slide['trangThai'] == 1 ? 'checked' : ''; ?>> Hiện</label>
                        <label><input type="radio" name="trangThai" value="0" <?php echo $slide['trangThai'] == 0 ? 'checked' : ''; ?>> Ẩn</label>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right;">
                <a href="slideshow.php" class="btn-action btn-back">Quay lại</a>
                <button type="submit" class="btn-action <?php echo $submitButtonClass; ?>"><?php echo $submitButtonText; ?></button>
            </div>
        </form>
    </div>
</div>