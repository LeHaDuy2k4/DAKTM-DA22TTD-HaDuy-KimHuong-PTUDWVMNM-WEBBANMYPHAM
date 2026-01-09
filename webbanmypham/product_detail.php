<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("header.php");
require_once("config.php");

$product = null;
$error_message = "";
$today = date('Y-m-d'); // Lấy ngày hiện tại để kiểm tra khuyến mãi

// Lấy mã sản phẩm từ URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $maMH = $conn->real_escape_string($_GET['id']);

    // CẬP NHẬT SQL: Join thêm bảng khuyenmai để lấy thông tin giảm giá
    $sql = "SELECT p.maMH, p.tenMH, p.DonGia, p.soLuongTon, p.hinhAnh, p.moTa, 
                   p.maTH, p.maDM, p.maKM,
                   b.tenTH AS thuongHieu, c.tenDM AS danhMuc,
                   km.phantramgiam, km.ngayBD, km.ngayKT
            FROM mathang p
            LEFT JOIN thuonghieu b ON p.maTH = b.maTH
            LEFT JOIN danhmucsp c ON p.maDM = c.maDM
            LEFT JOIN khuyenmai km ON p.maKM = km.maKM
            WHERE p.maMH = '$maMH'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // --- LOGIC TÍNH GIÁ KHUYẾN MÃI ---
        $is_sale = false;
        $final_price = $product['DonGia'];
        
        // Nếu sản phẩm có mã KM và ngày hiện tại nằm trong khoảng cho phép
        if (!empty($product['maKM']) && $today >= $product['ngayBD'] && $today <= $product['ngayKT']) {
            $is_sale = true;
            $final_price = $product['DonGia'] - ($product['DonGia'] * ($product['phantramgiam'] / 100));
        }
    } else {
        $error_message = "Sản phẩm không tồn tại hoặc đã bị xóa.";
    }
} else {
    $error_message = "Không có sản phẩm được chọn.";
}

// --- XỬ LÝ LINK "TIẾP TỤC MUA SẮM" THÔNG MINH ---
$backUrl = "products.php"; 
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'products.php') !== false) {
        $backUrl = $referer;
    }
} elseif ($product && !empty($product['maDM'])) {
    $backUrl = "products.php?maDM=" . $product['maDM'];
}
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    :root {
        --primary-color: #E91E63;
        --primary-hover: #c2185b;
        --text-color: #333;
        --bg-color: #f9f9f9;
    }

    body { background-color: var(--bg-color); font-family: 'Poppins', sans-serif; }

    .product-detail-container {
        max-width: 1100px;
        margin: 40px auto;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        position: relative; /* Để định vị badge giảm giá */
    }

    /* Badge giảm giá */
    .sale-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: var(--primary-color);
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 700;
        z-index: 10;
        box-shadow: 0 4px 10px rgba(233, 30, 99, 0.3);
    }

    .product-image-section {
        flex: 1 1 400px;
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff0f5;
    }

    .product-image {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
        transition: transform 0.3s ease;
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
    }

    .product-image:hover { transform: scale(1.05); }

    .product-info-section {
        flex: 1 1 500px;
        padding: 40px;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #888;
        margin-bottom: 10px;
    }

    .product-name {
        font-size: 2.2rem;
        color: var(--text-color);
        font-weight: 700;
        margin-bottom: 15px;
        line-height: 1.2;
    }

    /* CSS cho Giá */
    .product-price-wrapper { margin-bottom: 20px; }
    .product-price {
        font-size: 2rem;
        color: var(--primary-color);
        font-weight: 600;
    }
    .old-price {
        font-size: 1.2rem;
        color: #aaa;
        text-decoration: line-through;
        margin-left: 15px;
        font-weight: 400;
    }

    .product-meta {
        margin-bottom: 25px;
        font-size: 0.95rem;
        color: #555;
    }
    
    .product-meta span {
        display: inline-block;
        margin-right: 20px;
        padding: 5px 10px;
        background: #f0f0f0;
        border-radius: 5px;
    }

    .product-description {
        margin-bottom: 30px;
        font-size: 1rem;
        border-top: 1px solid #eee;
        padding-top: 20px;
        color: #444;
    }

    .description-content { margin-top: 15px; line-height: 1.8; overflow-wrap: break-word; }

    .action-buttons { margin-top: auto; display: flex; gap: 15px; }

    .btn-add-cart {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 50px;
        cursor: pointer;
        transition: background 0.3s;
        flex-grow: 1;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .btn-add-cart:hover {
        background-color: var(--primary-hover);
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.4);
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #888;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .back-link:hover { color: var(--primary-color); }

    @media (max-width: 768px) {
        .product-detail-container { flex-direction: column; margin: 15px; border-radius: 0; }
        .product-image-section { padding: 20px; }
        .product-name { font-size: 1.8rem; }
    }
</style>

<div class="product-detail-container">
    <?php if ($error_message): ?>
        <div style="padding: 50px; text-align: center; width: 100%;">
            <p style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 20px;"><?php echo $error_message; ?></p>
            <a href="products.php" class="btn-add-cart" style="max-width: 200px;">Quay lại cửa hàng</a>
        </div>
    <?php else: ?>
        <?php if ($is_sale): ?>
            <div class="sale-badge">GIẢM <?php echo (float)$product['phantramgiam']; ?>%</div>
        <?php endif; ?>

        <div class="product-image-section">
            <img src="<?php echo htmlspecialchars($product['hinhAnh']); ?>" 
                 alt="<?php echo htmlspecialchars($product['tenMH']); ?>" 
                 class="product-image">
        </div>

        <div class="product-info-section">
            <div class="product-category">
                <?php echo htmlspecialchars($product['danhMuc']); ?> / <?php echo htmlspecialchars($product['thuongHieu']); ?>
            </div>
            
            <h1 class="product-name"><?php echo htmlspecialchars($product['tenMH']); ?></h1>
            
            <div class="product-price-wrapper">
                <span class="product-price">
                    <?php echo number_format($final_price, 0, ',', '.') ?> ₫
                </span>
                <?php if ($is_sale): ?>
                    <span class="old-price">
                        <?php echo number_format($product['DonGia'], 0, ',', '.') ?> ₫
                    </span>
                <?php endif; ?>
            </div>

            <div class="product-meta">
                <span><i class="fa fa-box"></i> Tồn kho: <?php echo (int)$product['soLuongTon']; ?></span>
                <span><i class="fa fa-tag"></i> Thương hiệu: <?php echo htmlspecialchars($product['thuongHieu']); ?></span>
            </div>

            <div class="product-description">
                <strong>Chi tiết sản phẩm:</strong>
                <div class="description-content">
                    <?php echo $product['moTa']; ?>
                </div>
            </div>

            <div class="action-buttons">
                <a href="add_to_cart.php?id=<?php echo $product['maMH']; ?>" class="btn-add-cart">
                    <i class="fa-solid fa-cart-shopping"></i> THÊM VÀO GIỎ
                </a>
            </div>
            
            <a href="<?php echo htmlspecialchars($backUrl); ?>" class="back-link">&larr; Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>

<?php require("footer.php"); ?>