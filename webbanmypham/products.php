<?php
// BẮT BUỘC: Khởi động session nếu chưa chạy
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("header.php");
require_once("config.php");

// --- Khởi tạo biến
$products = [];
$filter_title = "Tất cả sản phẩm";
$sql_where = "";
$param_type = "";
$param_value = "";
$is_filtered = false;

// --- Xử lý lọc & truy vấn
if (isset($conn) && $conn->connect_error === null) {

    // Lọc theo thương hiệu
    if (isset($_GET['maTH']) && !empty($_GET['maTH'])) {
        $maTH = $_GET['maTH'];
        $sql_where = "WHERE maTH = ?";
        $param_type = "s";
        $param_value = $maTH;
        $is_filtered = true;

        // Lấy tên thương hiệu để hiển thị tiêu đề
        $stmt_name = $conn->prepare("SELECT tenTH FROM thuonghieu WHERE maTH = ?");
        if ($stmt_name === false) {
            die("Lỗi SQL chuẩn bị truy vấn Tên Thương hiệu: " . $conn->error);
        }
        $stmt_name->bind_param("s", $maTH);
        $stmt_name->execute();
        $result_name = $stmt_name->get_result();
        if ($result_name->num_rows > 0) {
            $filter_title = "Sản phẩm của Thương hiệu: " . htmlspecialchars($result_name->fetch_assoc()['tenTH']);
        } else {
            $filter_title = "Thương hiệu không tồn tại";
        }
        $stmt_name->close();

    }
    // Lọc theo danh mục
    elseif (isset($_GET['maDM']) && !empty($_GET['maDM'])) {
        $maDM = $_GET['maDM'];
        $sql_where = "WHERE maDM = ?";
        $param_type = "s";
        $param_value = $maDM;
        $is_filtered = true;

        // Lấy tên danh mục
        $stmt_name = $conn->prepare("SELECT tenDM FROM danhmucsp WHERE maDM = ?");
        if ($stmt_name === false) {
            die("Lỗi SQL chuẩn bị truy vấn Tên Danh mục: " . $conn->error);
        }
        $stmt_name->bind_param("s", $maDM);
        $stmt_name->execute();
        $result_name = $stmt_name->get_result();
        if ($result_name->num_rows > 0) {
            $filter_title = "Sản phẩm thuộc Danh mục: " . htmlspecialchars($result_name->fetch_assoc()['tenDM']);
        } else {
            $filter_title = "Danh mục không tồn tại";
        }
        $stmt_name->close();
    }

    // Câu truy vấn lấy sản phẩm (chỉ lấy những cột cần thiết)
    $sql_base = "SELECT maMH, tenMH, donGia, soluongTon, moTa, hinhAnh FROM mathang ";
    $sql_full = $sql_base . $sql_where . " ORDER BY tenMH ASC";

    if ($is_filtered) {
        $stmt_products = $conn->prepare($sql_full);
        if ($stmt_products === false) {
            die("<h1>Lỗi cơ sở dữ liệu</h1>
                <p>Kiểm tra lại tên bảng/cột trong CSDL.</p>
                <p><b>Lỗi MySQL:</b> " . $conn->error . "</p>
                <p><b>Câu lệnh SQL:</b> " . htmlspecialchars($sql_full) . "</p>");
        }
        $stmt_products->bind_param($param_type, $param_value);
        $stmt_products->execute();
        $result_products = $stmt_products->get_result();
    } else {
        $result_products = $conn->query($sql_full);
        if ($result_products === false) {
            die("<h1>Lỗi cơ sở dữ liệu</h1>
                <p>Kiểm tra lại tên bảng/cột trong CSDL.</p>
                <p><b>Lỗi MySQL:</b> " . $conn->error . "</p>
                <p><b>Câu lệnh SQL:</b> " . htmlspecialchars($sql_full) . "</p>");
        }
    }

    // Lấy dữ liệu vào mảng
    if ($result_products && $result_products->num_rows > 0) {
        while ($row = $result_products->fetch_assoc()) {
            $products[] = $row;
        }
    }

    if (isset($stmt_products) && $stmt_products !== false) {
        $stmt_products->close();
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
:root {
    --cosmetics-accent-color: #E91E63;
    --cosmetics-text-dark: #333;
}

.products-container {
    max-width: 1300px;
    margin: 20px auto;
    padding: 0 20px;
}

.products-container h1 {
    font-size: 2rem;
    color: var(--cosmetics-accent-color);
    font-weight: 700;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.product-item {
    display: block;
    border: 1px solid #eee;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    color: var(--cosmetics-text-dark);
    transition: box-shadow 0.3s, transform 0.3s;
    background: white;
    overflow: hidden;
}

.product-item:hover {
    box-shadow: 0 6px 15px rgba(233, 30, 99, 0.15);
    transform: translateY(-5px);
}

.product-image-wrapper {
    width: 100%;
    height: 200px;
    margin-bottom: 10px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}

.product-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s;
}

.product-item:hover .product-image {
    transform: scale(1.05);
}

.product-name {
    font-weight: 600;
    font-size: 1rem;
    margin: 5px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.4em;
}

.product-price {
    color: var(--cosmetics-accent-color);
    font-weight: 700;
    font-size: 1.15rem;
    margin-top: 10px;
}
</style>

<div class="products-container">
    <h1><?php echo $filter_title; ?></h1>
    <hr style="border-color: #eee;">

    <?php if (!empty($products)): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <a href="product_detail.php?id=<?php echo htmlspecialchars($product['maMH']); ?>" class="product-item">

                    <div class="product-image-wrapper">
                        <img src="<?php echo htmlspecialchars($product['hinhAnh'] ?? 'default.jpg'); ?>"
                             alt="<?php echo htmlspecialchars($product['tenMH']); ?>"
                             class="product-image">
                    </div>

                    <p class="product-name"><?php echo htmlspecialchars($product['tenMH']); ?></p>

                    <p class="product-price">
                        <?php echo number_format($product['donGia'], 0, ',', '.'); ?> VNĐ
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 8px; margin-top: 20px;">
            <i class="fa-solid fa-face-sad-tear" style="font-size: 2rem; color: #ccc; margin-bottom: 15px;"></i>
            <p style="color: #888; font-size: 1.1rem;">
                Rất tiếc, hiện tại không tìm thấy sản phẩm nào trong bộ lọc này.
            </p>
        </div>
    <?php endif; ?>

</div>

<?php require("footer.php"); ?>
