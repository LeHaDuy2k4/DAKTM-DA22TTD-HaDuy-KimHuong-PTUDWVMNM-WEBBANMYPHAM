<?php 
    require("../config.php"); 
    session_start();

    $success_msg = "";
    $error_msg = "";
    $redirect = false;

    // XỬ LÝ KHI NGƯỜI DÙNG NHẤN NÚT "ÁP DỤNG"
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_apply'])) {
        $apply_type = $_POST['apply_type']; // 'brand' hoặc 'category'
        $maKM = !empty($_POST['maKM']) ? (int)$_POST['maKM'] : "NULL";
        
        $sql_update = "";
        $target_name = "";

        if ($apply_type == 'brand') {
            $maTH = (int)$_POST['maTH'];
            $sql_update = "UPDATE mathang SET maKM = $maKM WHERE maTH = $maTH";
            $target_name = "thương hiệu";
        } else {
            $maDM = (int)$_POST['maDM'];
            $sql_update = "UPDATE mathang SET maKM = $maKM WHERE maDM = $maDM";
            $target_name = "danh mục";
        }

        if ($conn->query($sql_update)) {
            $affected = $conn->affected_rows;
            $success_msg = "Thành công! Đã cập nhật khuyến mãi cho $affected sản phẩm thuộc $target_name.";
            $redirect = true;
        } else {
            $error_msg = "Lỗi hệ thống: " . $conn->error;
        }
    }

    require("sidebar.php"); 

    // Lấy dữ liệu cho các Select box
    $brands = $conn->query("SELECT maTH, tenTH FROM thuonghieu ORDER BY tenTH ASC");
    $categories = $conn->query("SELECT maDM, tenDM FROM danhmucsp ORDER BY tenDM ASC");
    $today = date('Y-m-d');
    $promos = $conn->query("SELECT maKM, tenKM, phantramgiam FROM khuyenmai WHERE ngayKT >= '$today' ORDER BY ngayBD DESC");
?>

<style>
    /* THIẾT LẬP FONT CHỮ TIMES NEW ROMAN CHO TOÀN BỘ TRANG */
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
        text-align: center; 
    }

    .form-panel { 
        background: #ffffff; 
        padding: 30px; 
        border-radius: 12px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        max-width: 650px; 
        margin: 0 auto; 
    }
    
    .type-selector { 
        display: flex; 
        gap: 20px; 
        margin-bottom: 25px; 
        justify-content: center; 
        padding: 10px; 
        background: #fff0f5; 
        border-radius: 10px; 
    }

    .type-option { 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        font-weight: 600; 
        color: #e91e63; 
        font-size: 1.1rem;
    }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { 
        display: block; 
        margin-bottom: 8px; 
        font-weight: 600; 
        color: #555; 
        font-size: 1.1rem;
    }

    .form-group select { 
        width: 100%; 
        padding: 12px; 
        border: 1px solid #ffd6e5; 
        border-radius: 8px; 
        font-size: 1.1rem; 
        outline: none; 
        transition: 0.3s; 
    }

    .form-group select:focus { 
        border-color: #e91e63; 
        box-shadow: 0 0 5px rgba(233, 30, 99, 0.2); 
    }
    
    .btn-apply { 
        background: #e91e63; 
        color: white; 
        border: none; 
        padding: 15px 30px; 
        border-radius: 30px; 
        font-weight: 700; 
        cursor: pointer; 
        width: 100%; 
        transition: 0.3s; 
        font-size: 1.2rem; 
    }

    .btn-apply:hover { 
        background: #c2185b; 
        transform: translateY(-2px); 
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3); 
    }
    
    .alert { 
        padding: 15px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
        font-weight: 600; 
        text-align: center; 
        font-size: 1.1rem;
    }

    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-left: 5px solid #28a745; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-left: 5px solid #dc3545; }
    
    .hidden { display: none; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Gán Khuyến Mãi Hàng Loạt</h1>
    
    <div class="form-panel">
        <?php if ($success_msg): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label style="display: block; text-align: center; margin-bottom: 10px; font-weight: 600; font-size: 1.1rem;">Chọn phương thức gán giảm giá:</label>
            <div class="type-selector">
                <label class="type-option">
                    <input type="radio" name="apply_type" value="brand" checked onclick="toggleFields('brand')"> Theo Thương hiệu
                </label>
                <label class="type-option">
                    <input type="radio" name="apply_type" value="category" onclick="toggleFields('category')"> Theo Danh mục
                </label>
            </div>

            <div class="form-group" id="brand-group">
                <label>1. Chọn Thương hiệu áp dụng *</label>
                <select name="maTH" id="maTH" required>
                    <option value="">-- Chọn thương hiệu --</option>
                    <?php while($row = $brands->fetch_assoc()): ?>
                        <option value="<?php echo $row['maTH']; ?>"><?php echo htmlspecialchars($row['tenTH']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group hidden" id="category-group">
                <label>1. Chọn Danh mục áp dụng *</label>
                <select name="maDM" id="maDM">
                    <option value="">-- Chọn danh mục sản phẩm --</option>
                    <?php while($row = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $row['maDM']; ?>"><?php echo htmlspecialchars($row['tenDM']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>2. Chọn Chương trình Khuyến mãi *</label>
                <select name="maKM">
                    <option value="">-- Gỡ bỏ tất cả khuyến mãi --</option>
                    <?php while($row = $promos->fetch_assoc()): ?>
                        <option value="<?php echo $row['maKM']; ?>">
                            <?php echo htmlspecialchars($row['tenKM']); ?> (Giảm <?php echo (float)$row['phantramgiam']; ?>%)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <p style="color: #888; font-size: 1rem; margin-bottom: 20px; border-left: 3px solid #e91e63; padding-left: 10px; font-style: italic;">
                <i class="fa fa-info-circle"></i> Lưu ý: Toàn bộ sản phẩm thuộc đối tượng được chọn sẽ bị ghi đè mã khuyến mãi. Hãy cẩn trọng khi thực hiện.
            </p>

            <button type="submit" name="btn_apply" class="btn-apply" onclick="return confirm('Xác nhận cập nhật cho toàn bộ sản phẩm thuộc nhóm này?')">
                ÁP DỤNG NGAY
            </button>
        </form>
    </div>
</div>

<script>
    function toggleFields(type) {
        const brandGroup = document.getElementById('brand-group');
        const categoryGroup = document.getElementById('category-group');
        const brandSelect = document.getElementById('maTH');
        const categorySelect = document.getElementById('maDM');

        if (type === 'brand') {
            brandGroup.classList.remove('hidden');
            categoryGroup.classList.add('hidden');
            brandSelect.required = true;
            categorySelect.required = false;
            categorySelect.value = ""; 
        } else {
            brandGroup.classList.add('hidden');
            categoryGroup.classList.remove('hidden');
            brandSelect.required = false;
            categorySelect.required = true;
            brandSelect.value = ""; 
        }
    }

    <?php if ($redirect): ?>
    setTimeout(function() {
        window.location.href = "promotions.php?success=" + encodeURIComponent("<?php echo $success_msg; ?>");
    }, 3000);
    <?php endif; ?>

    document.querySelectorAll('.swiper-notification').forEach(el => el.remove());
</script>