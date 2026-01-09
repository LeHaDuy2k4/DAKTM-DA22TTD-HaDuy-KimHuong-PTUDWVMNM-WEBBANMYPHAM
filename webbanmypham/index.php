<?php
// Tên file: index.php
session_start(); 
require("config.php"); 
require("header.php"); 

// 1. TRUY VẤN DỮ LIỆU SLIDESHOW (Chỉ lấy hinhAnh và linkAnh)
$slides = [];
if (isset($conn) && $conn->connect_error === null) {
    $sql_slide = "SELECT hinhAnh, linkAnh FROM trinhchieu WHERE trangThai = 1 ORDER BY thuTu ASC";
    $result_slide = $conn->query($sql_slide);

    if ($result_slide && $result_slide->num_rows > 0) {
        while($row = $result_slide->fetch_assoc()) {
            $slides[] = $row;
        }
    }
}

// DỮ LIỆU SẢN PHẨM MẪU
$featured_products = [
    ['id' => 101, 'name' => 'Kem Dưỡng Retinol Tái Tạo Da', 'price' => 750000, 'img' => 'product_1.jpg'],
    ['id' => 102, 'name' => 'Serum Niacinamide 10% Kiềm Dầu', 'price' => 380000, 'img' => 'product_2.jpg'],
];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
/* --- KHỐI CHẶN LỖI HIỆN SỐ --- */
.swiper-notification, .swiper-pagination, .swiper-button-lock,
[aria-live="assertive"], [aria-live="polite"] {
    display: none !important;
}

/* --- GIAO DIỆN SLIDER --- */
.main-slider-container {
    width: 100%;
    height: 480px;
    margin-bottom: 50px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(233, 30, 99, 0.15);
    position: relative;
    background: #fdfdfd;
}

.swiper { width: 100%; height: 100%; }

.swiper-slide a {
    display: block;
    width: 100%;
    height: 100%;
    text-decoration: none;
}

.swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 1.5s ease;
}

/* Hiệu ứng zoom nhẹ khi slide hoạt động */
.swiper-slide-active img { transform: scale(1.08); }

/* Nút mũi tên điều hướng */
.swiper-button-next, .swiper-button-prev { 
    color: #fff; 
    background: rgba(233, 30, 99, 0.25);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    backdrop-filter: blur(4px);
    border: none;
}
.swiper-button-next:after, .swiper-button-prev:after { font-size: 18px; }

/* CSS Nội dung sản phẩm */
.main-content { max-width: 1300px; margin: 20px auto; padding: 0 20px; }
.section-title { text-align: center; font-size: 2rem; color: #e91e63; margin: 40px 0 30px; font-weight: 700; }
.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
.product-card { background: #fff; border-radius: 12px; border: 1px solid #f0f0f0; overflow: hidden; transition: 0.3s; text-decoration: none; text-align: center; padding-bottom: 15px; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(233, 30, 99, 0.15); }
</style>

<div class="main-content">
    <div class="main-slider-container">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php if (!empty($slides)): ?>
                    <?php foreach ($slides as $slide): ?>
                        <div class="swiper-slide">
                            <a href="<?php echo htmlspecialchars($slide['linkAnh']); ?>">
                                <img src="uploads/<?php echo $slide['hinhAnh']; ?>" alt="Banner Image">
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swiper-slide" style="background: #FFD1DC; display: flex; align-items: center; justify-content: center;">
                        <h2 style="color: #e91e63;">HUONGG BEAUTY</h2>
                    </div>
                <?php endif; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>

    <h2 class="section-title">🔥 SẢN PHẨM NỔI BẬT</h2>
    <div class="product-grid">
        <?php foreach ($featured_products as $product): ?>
        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div style="height:220px; display:flex; align-items:center; justify-content:center;">
                <img src="images/placeholder.jpg" style="max-height: 80%;">
            </div>
            <p style="font-weight: 600; color: #333; margin: 10px 0;"><?php echo $product['name']; ?></p>
            <span style="color:#e91e63; font-weight:800;"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
  var swiper = new Swiper(".mySwiper", {
    loop: true,
    speed: 1200, 
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    a11y: false,
    grabCursor: true,
  });
</script>

<?php require("footer.php"); ?>