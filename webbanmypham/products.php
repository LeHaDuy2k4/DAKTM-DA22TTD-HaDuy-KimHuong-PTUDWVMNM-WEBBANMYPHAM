<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("header.php");
require_once("config.php");

$products = [];
$filter_title = "Tất cả sản phẩm";
$sql_where = "WHERE 1=1"; // Sử dụng 1=1 để dễ dàng nối chuỗi logic AND
$params = [];
$param_types = "";
$today = date('Y-m-d');

if (isset($conn) && $conn->connect_error === null) {
    
    // 1. XỬ LÝ TÌM KIẾM (Nếu có tham số query từ header)
    if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
        $search = trim($_GET['query']);
        $sql_where .= " AND (m.tenMH LIKE ? OR m.maMH LIKE ?)";
        $param_types .= "ss";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $filter_title = "Kết quả tìm kiếm cho: '" . htmlspecialchars($search) . "'";
    }

    // 2. XỬ LÝ LỌC THEO THƯƠNG HIỆU
    if (isset($_GET['maTH']) && !empty($_GET['maTH'])) {
        $maTH = $_GET['maTH'];
        $sql_where .= " AND m.maTH = ?";
        $param_types .= "s";
        $params[] = $maTH;

        $stmt_name = $conn->prepare("SELECT tenTH FROM thuonghieu WHERE maTH = ?");
        $stmt_name->bind_param("s", $maTH);
        $stmt_name->execute();
        $res = $stmt_name->get_result();
        if ($row = $res->fetch_assoc()) $filter_title = "Thương hiệu: " . $row['tenTH'];
        $stmt_name->close();
    } 
    
    // 3. XỬ LÝ LỌC THEO DANH MỤC
    if (isset($_GET['maDM']) && !empty($_GET['maDM'])) {
        $maDM = $_GET['maDM'];
        $sql_where .= " AND m.maDM = ?";
        $param_types .= "s";
        $params[] = $maDM;

        $stmt_name = $conn->prepare("SELECT tenDM FROM danhmucsp WHERE maDM = ?");
        $stmt_name->bind_param("s", $maDM);
        $stmt_name->execute();
        $res = $stmt_name->get_result();
        if ($row = $res->fetch_assoc()) $filter_title = "Danh mục: " . $row['tenDM'];
        $stmt_name->close();
    }

    // 4. TRUY VẤN SẢN PHẨM + KHUYẾN MÃI
    $sql = "SELECT m.maMH, m.tenMH, m.donGia, m.hinhAnh, m.maKM, 
                   km.phantramgiam, km.ngayBD, km.ngayKT 
            FROM mathang m 
            LEFT JOIN khuyenmai km ON m.maKM = km.maKM 
            $sql_where 
            ORDER BY m.tenMH ASC";

    $stmt = $conn->prepare($sql);
    if (!empty($param_types)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
:root {
    --accent: #E91E63;
    --text-main: #333;
    --gray: #888;
}

.products-container { max-width: 1200px; margin: 30px auto; padding: 0 15px; font-family: 'Times New Roman', Times, serif; }
.products-container h1 { font-size: 1.8rem; color: var(--accent); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid #ffe1ec; padding-bottom: 10px; }

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
}

.product-item {
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    text-decoration: none;
    color: var(--text-main);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    position: relative;
    display: flex;
    flex-direction: column;
}

.product-item:hover {
    box-shadow: 0 10px 20px rgba(233, 30, 99, 0.1);
    transform: translateY(-5px);
    border-color: #ffe1ec;
}

.discount-tag {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--accent);
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    z-index: 2;
}

.product-image-wrapper { height: 200px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; overflow: hidden; }
.product-image { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.5s ease; }
.product-item:hover .product-image { transform: scale(1.08); }

.product-name {
    font-weight: 600;
    font-size: 1rem;
    height: 2.8em;
    overflow: hidden;
    margin-bottom: 10px;
    line-height: 1.4;
    color: #444;
}

.price-block { margin-top: auto; padding-top: 10px; }
.new-price { color: var(--accent); font-weight: 700; font-size: 1.2rem; display: block; }
.old-price { color: var(--gray); text-decoration: line-through; font-size: 0.9rem; margin-right: 5px; }

.no-results { text-align: center; padding: 100px 0; grid-column: 1 / -1; }
.no-results i { font-size: 4rem; color: #ddd; margin-bottom: 20px; }
</style>

<div class="products-container">
    <h1><i class="fa-solid fa-magnifying-glass"></i> <?php echo $filter_title; ?></h1>
    
    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): 
                $is_sale = false;
                $current_price = $p['donGia'];
                
                if ($p['maKM'] && $today >= $p['ngayBD'] && $today <= $p['ngayKT']) {
                    $is_sale = true;
                    $percent = (float)$p['phantramgiam'];
                    $current_price = $p['donGia'] - ($p['donGia'] * ($percent / 100));
                }
            ?>
                <a href="product_detail.php?id=<?php echo htmlspecialchars($p['maMH']); ?>" class="product-item">
                    
                    <?php if ($is_sale): ?>
                        <div class="discount-tag">-<?php echo (float)$p['phantramgiam']; ?>%</div>
                    <?php endif; ?>

                    <div class="product-image-wrapper">
                        <img src="<?php echo htmlspecialchars($p['hinhAnh']); ?>" 
                             alt="<?php echo htmlspecialchars($p['tenMH']); ?>" 
                             class="product-image"
                             onerror="this.src='https://via.placeholder.com/200?text=Beauty+Product'">
                    </div>

                    <p class="product-name"><?php echo htmlspecialchars($p['tenMH']); ?></p>

                    <div class="price-block">
                        <?php if ($is_sale): ?>
                            <span class="old-price"><?php echo number_format($p['donGia'], 0, ',', '.'); ?>₫</span>
                            <span class="new-price"><?php echo number_format($current_price, 0, ',', '.'); ?>₫</span>
                        <?php else: ?>
                            <span class="new-price"><?php echo number_format($p['donGia'], 0, ',', '.'); ?>₫</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fa-solid fa-box-open"></i>
                <p style="color: #999; font-size: 1.2rem;">Rất tiếc, không tìm thấy sản phẩm nào phù hợp.</p>
                <a href="products.php" style="color: var(--accent); text-decoration: none; font-weight: bold;">Quay lại danh sách sản phẩm</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require("footer.php"); ?>