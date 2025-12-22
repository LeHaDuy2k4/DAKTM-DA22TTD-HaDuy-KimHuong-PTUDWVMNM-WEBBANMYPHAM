<?php 
// Tên file: admin/product_master.php (GỘP LOGIC ADD/EDIT và SAVE)

// Yêu cầu file sidebar.php
require("sidebar.php"); 
    
// Yêu cầu file kết nối database (Đường dẫn từ admin/ ra ngoài)
require("../config.php"); 

// Khai báo mặc định cho mặt hàng mới
$product = [
    'maMH' => '',
    'tenMH' => '',
    'DonGia' => 0,
    'soLuongTon' => 0,
    'hinhAnh' => '', // Sẽ lưu URL ảnh
    'maDM' => '', // ID Danh mục
    'maTH' => '', // ID Thương hiệu
    'moTa' => '',
    'trangThai' => 1, // 1: Hiển thị (mặc định)
];

// Mảng chứa Danh mục và Thương hiệu từ DB (để đổ vào Select)
$categories = [];
$brands = [];
$error_message = ""; 
$message_success = "";

// ====================================================================
// A. TRUY VẤN DANH MỤC & THƯƠNG HIỆU (LUÔN CHẠY)
// ====================================================================

if (isset($conn) && $conn->connect_error === null) {
    // 1.1. Lấy Danh mục
    $sql_dm = "SELECT maDM, tenDM FROM danhmucsp ORDER BY tenDM ASC";
    $result_dm = $conn->query($sql_dm);
    if ($result_dm) {
        while ($row = $result_dm->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // 1.2. Lấy Thương hiệu
    $sql_th = "SELECT maTH, tenTH FROM thuonghieu ORDER BY tenTH ASC";
    $result_th = $conn->query($sql_th);
    if ($result_th) {
        while ($row = $result_th->fetch_assoc()) {
            $brands[] = $row;
        }
    }
}

// ====================================================================
// B. XỬ LÝ POST DATA (LOGIC SAVE/UPDATE)
// ====================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. LẤY VÀ LÀM SẠCH DỮ LIỆU ---
    $action_type = $_POST['action_type'] ?? '';

    // Lấy dữ liệu và làm sạch
    $tenMH = $conn->real_escape_string(trim($_POST['tenMH'] ?? ''));
    $moTa = $conn->real_escape_string(trim($_POST['moTa'] ?? ''));
    $hinhAnh = $conn->real_escape_string(trim($_POST['hinhAnh'] ?? '')); 
    $maDM = $conn->real_escape_string($_POST['maDM'] ?? '');
    $maTH = $conn->real_escape_string($_POST['maTH'] ?? '');
    $maMH_old = $conn->real_escape_string($_POST['maMH_old'] ?? ''); 

    $DonGia = floatval($_POST['DonGia'] ?? 0);
    $soLuongTon = intval($_POST['soLuongTon'] ?? 0);
    $trangThai = (int)($_POST['trangThai'] ?? 0);
    
    // --- KIỂM TRA DỮ LIỆU BẮT BUỘC ---
    if (empty($tenMH) || empty($maDM) || empty($maTH) || empty($hinhAnh) || $DonGia < 0 || $soLuongTon < 0) {
        $error_message = "Vui lòng điền đầy đủ thông tin sản phẩm bắt buộc.";
    }

    if (empty($error_message)) {
        if ($action_type == 'add') {
            // --- THÊM MỚI ---
            $sql = "INSERT INTO mathang (tenMH, DonGia, soLuongTon, hinhAnh, maDM, maTH, moTa, trangThai, ngayNhap) 
                    VALUES ('$tenMH', $DonGia, $soLuongTon, '$hinhAnh', '$maDM', '$maTH', '$moTa', $trangThai, NOW())";
            $message_success_temp = "Đã thêm sản phẩm **" . htmlspecialchars($tenMH) . "** thành công!";

        } elseif ($action_type == 'edit' && !empty($maMH_old)) {
            // --- CẬP NHẬT ---
            $updateFields = [];
            $updateFields[] = "tenMH = '$tenMH'";
            $updateFields[] = "DonGia = $DonGia";
            $updateFields[] = "soLuongTon = $soLuongTon";
            $updateFields[] = "hinhAnh = '$hinhAnh'";
            $updateFields[] = "maDM = '$maDM'";
            $updateFields[] = "maTH = '$maTH'";
            $updateFields[] = "moTa = '$moTa'";
            $updateFields[] = "trangThai = $trangThai";
            
            $setClause = implode(", ", $updateFields);
            $sql = "UPDATE mathang SET $setClause WHERE maMH = '$maMH_old'";
            $message_success_temp = "Đã cập nhật sản phẩm **" . htmlspecialchars($tenMH) . "** thành công!";
        } 
        
        // --- THỰC THI SQL VÀ CHUYỂN HƯỚNG ---
        if (!empty($sql)) {
            if ($conn->query($sql) === TRUE) {
                // Chuyển hướng về trang danh sách sản phẩm (products.php)
                header("Location: products.php?message=" . urlencode($message_success_temp));
                exit();
            } else {
                $error_message = "Lỗi thực thi CSDL: " . $conn->error;
            }
        }
    }
    
    // Nếu có lỗi (chuyển hướng thất bại), giữ lại dữ liệu đã POST để người dùng sửa
    if (!empty($error_message)) {
        $product = [
            'maMH' => $maMH_old, 
            'tenMH' => $tenMH,
            'DonGia' => $DonGia,
            'soLuongTon' => $soLuongTon,
            'hinhAnh' => $hinhAnh,
            'maDM' => $maDM,
            'maTH' => $maTH,
            'moTa' => $moTa,
            'trangThai' => $trangThai,
        ];
    }
}


// ====================================================================
// C. KIỂM TRA CHẾ ĐỘ SỬA LẦN ĐẦU (GET REQUEST)
// ====================================================================

// Chỉ chạy nếu không phải là POST request đang có lỗi
$isEditModeFinal = false;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['maMH']) && !empty($_GET['maMH']) && empty($error_message)) {
    $targetMaMH = $conn->real_escape_string($_GET['maMH']);
    
    // Truy vấn CSDL để lấy thông tin sản phẩm cần sửa
    $sql_edit = "SELECT maMH, tenMH, DonGia, soLuongTon, hinhAnh, maDM, maTH, moTa, trangThai 
                 FROM mathang
                 WHERE maMH = '$targetMaMH'";
    
    $result = $conn->query($sql_edit);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc(); 
        $isEditModeFinal = true;
    } else {
        // Nếu không tìm thấy sản phẩm khi truy cập bằng GET
        header("Location: products.php?error=" . urlencode("Không tìm thấy sản phẩm cần sửa."));
        exit();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($product['maMH'])) {
    // Nếu là POST request bị lỗi, và form đang ở chế độ EDIT
    $isEditModeFinal = true;
}


// Cập nhật tiêu đề và nút bấm dựa trên chế độ
$pageTitle = $isEditModeFinal ? "Sửa Sản Phẩm: " . htmlspecialchars($product['tenMH']) : "Thêm Sản Phẩm Mới";
$submitButtonText = $isEditModeFinal ? "Cập Nhật Sản Phẩm" : "Thêm Sản Phẩm";
$submitButtonClass = $isEditModeFinal ? "btn-edit-item" : "btn-save-item";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* --- CSS --- */
.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; min-height: 100vh; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }
.form-panel { background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 0.95rem; }
.form-group input:not([type="radio"]):not([type="checkbox"]),
.form-group select,
.form-group textarea {
    width: 100%; padding: 12px; border: 1px solid #ffe1ec; border-radius: 8px; box-sizing: border-box; font-size: 1rem; transition: border-color 0.3s, box-shadow 0.3s; background-color: #fcfcfc;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #e91e63; box-shadow: 0 0 5px rgba(233, 30, 99, 0.2); outline: none; }

.form-row { display: flex; gap: 20px; }
.form-row .form-group { flex: 1; }

.radio-group { display: flex; gap: 20px; padding: 10px 0; }
.radio-group label { font-weight: normal; }
.radio-group input[type="radio"] { margin-right: 5px; width: auto; }

.btn-action { padding: 12px 25px; border: none; border-radius: 30px; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-size: 1rem; }

.btn-save-item { background: #20c997; color: white; }
.btn-save-item:hover { background: #17a57a; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(32, 201, 151, 0.4); }
.btn-edit-item { background: #e91e63; color: white; }
.btn-edit-item:hover { background: #d63384; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(233, 30, 99, 0.4); }
.btn-back { background: #f0f0f0; color: #555; text-decoration: none; margin-right: 15px; }
.btn-back:hover { background: #e0e0e0; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(0,0,0,0.1); }

/* --- STYLE CHO TRƯỜNG FILE UPLOAD VÀ THUMBNAIL --- */
.image-upload-preview {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 10px;
}
.current-thumb {
    width: 80px;
    height: 80px;
    object-fit: contain; 
    border-radius: 8px;
    border: 1px solid #ccc;
}
.file-name {
    font-size: 0.9rem;
    color: #888;
    max-width: 300px;
    word-wrap: break-word; 
}
/* --- MESSAGE ALERT --- */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<div class="main-content">
    <h1 class="dashboard-title"><i class="fa-solid fa-bottle-droplet"></i> <?php echo $pageTitle; ?></h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> Lỗi: <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="form-panel">
        <form action="save_products.php" method="POST">
            
            <?php if ($isEditModeFinal): ?>
                <input type="hidden" name="action_type" value="edit">
                <input type="hidden" name="maMH_old" value="<?php echo htmlspecialchars($product['maMH']); ?>"> 
                <input type="hidden" name="hinhAnh_old" value="<?php echo htmlspecialchars($product['hinhAnh']); ?>">
            <?php else: ?>
                <input type="hidden" name="action_type" value="add">
            <?php endif; ?>
            
            <?php if ($isEditModeFinal): ?>
                <div class="form-group">
                    <label>Mã Mặt Hàng</label>
                    <input type="text" value="<?php echo htmlspecialchars($product['maMH']); ?>" readonly style="background-color: #eee; font-weight: 600;">
                    <small style="color: #888;">(Không thể sửa mã mặt hàng)</small>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="tenMH">Tên Mặt Hàng *</label>
                <input type="text" id="tenMH" name="tenMH" 
                       value="<?php echo htmlspecialchars($product['tenMH']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="moTa">Mô tả chi tiết</label>
                <textarea id="moTa" name="moTa" rows="4" 
                          placeholder="Mô tả chi tiết sản phẩm..."><?php echo htmlspecialchars($product['moTa']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="DonGia">Đơn Giá (VNĐ) *</label>
                    <input type="number" id="DonGia" name="DonGia" 
                           value="<?php echo htmlspecialchars($product['DonGia']); ?>" required min="0">
                </div>
                
                <div class="form-group">
                    <label for="soLuongTon">Số Lượng Tồn Kho *</label>
                    <input type="number" id="soLuongTon" name="soLuongTon" 
                           value="<?php echo htmlspecialchars($product['soLuongTon']); ?>" required min="0">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="maDM">Danh Mục *</label>
                    <select id="maDM" name="maDM" required>
                        <option value="">-- Chọn Danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['maDM']); ?>"
                                <?php echo ($product['maDM'] == $cat['maDM']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['tenDM']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="maTH">Thương Hiệu *</label>
                    <select id="maTH" name="maTH" required>
                        <option value="">-- Chọn Thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo htmlspecialchars($brand['maTH']); ?>"
                                <?php echo ($product['maTH'] == $brand['maTH']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['tenTH']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="hinhAnh">URL Ảnh Đại Diện *</label>
                <input type="text" id="hinhAnh" name="hinhAnh" 
                       value="<?php echo htmlspecialchars($product['hinhAnh']); ?>" 
                       placeholder="Dán link ảnh (URL) vào đây..." 
                       required>
                
                <?php if ($isEditModeFinal && !empty($product['hinhAnh'])): ?>
                    <div class="image-upload-preview">
                        <img src="<?php echo htmlspecialchars($product['hinhAnh']); ?>" alt="Ảnh hiện tại" class="current-thumb">
                        <div class="file-name">URL ảnh hiện tại: <?php echo htmlspecialchars($product['hinhAnh']); ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Trạng thái hiển thị *</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="trangThai" value="1" 
                            <?php echo $product['trangThai'] == 1 ? 'checked' : ''; ?>> Hiển thị (Bán)
                    </label>
                    <label>
                        <input type="radio" name="trangThai" value="0" 
                            <?php echo $product['trangThai'] == 0 ? 'checked' : ''; ?>> Ẩn (Ngừng bán tạm thời)
                    </label>
                </div>
            </div>
            
            <div style="margin-top: 30px; text-align: right;">
                <a href="products.php" class="btn-action btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                
                <button type="submit" class="btn-action <?php echo $submitButtonClass; ?>">
                    <i class="fa-solid fa-floppy-disk"></i> <?php echo $submitButtonText; ?>
                </button>
            </div>

        </form>
    </div>
</div>