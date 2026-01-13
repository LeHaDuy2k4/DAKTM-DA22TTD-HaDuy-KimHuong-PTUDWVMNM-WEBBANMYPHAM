<?php
// Tên file: index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("config.php"); 
require("header.php"); 

$today = date('Y-m-d');

// 1. TRUY VẤN SLIDESHOW
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

// 2. TRUY VẤN SẢN PHẨM NỔI BẬT (BÁN CHẠY)
$featured_products = [];
if (isset($conn) && $conn->connect_error === null) {
    $sql_featured = "SELECT m.maMH, m.tenMH, m.donGia, m.hinhAnh, m.maKM, 
                            km.phantramgiam, km.ngayBD, km.ngayKT,
                            IFNULL(SUM(ct.soLuong), 0) as tongBan
                     FROM mathang m
                     LEFT JOIN chitietdathang ct ON m.maMH = ct.maMH
                     LEFT JOIN khuyenmai km ON m.maKM = km.maKM
                     WHERE m.trangThai = 1
                     GROUP BY m.maMH
                     ORDER BY tongBan DESC, m.maMH DESC
                     LIMIT 8";
    $result_featured = $conn->query($sql_featured);
    if ($result_featured) {
        while($row = $result_featured->fetch_assoc()) $featured_products[] = $row;
    }
}

// 3. TRUY VẤN HÀNG MỚI VỀ (NEW ARRIVALS) - MỚI THÊM
// Logic: Sắp xếp theo ngày nhập mới nhất
$new_products = [];
if (isset($conn) && $conn->connect_error === null) {
    $sql_new = "SELECT m.maMH, m.tenMH, m.donGia, m.hinhAnh, m.maKM, 
                       km.phantramgiam, km.ngayBD, km.ngayKT
                FROM mathang m
                LEFT JOIN khuyenmai km ON m.maKM = km.maKM
                WHERE m.trangThai = 1
                ORDER BY m.ngayNhap DESC, m.maMH DESC
                LIMIT 8";
    $result_new = $conn->query($sql_new);
    if ($result_new) {
        while($row = $result_new->fetch_assoc()) $new_products[] = $row;
    }
}

// 4. TRUY VẤN SẢN PHẨM ĐANG GIẢM GIÁ
$sale_products = [];
if (isset($conn) && $conn->connect_error === null) {
    $sql_sale = "SELECT m.maMH, m.tenMH, m.donGia, m.hinhAnh, m.maKM, 
                        km.phantramgiam, km.ngayBD, km.ngayKT
                 FROM mathang m
                 JOIN khuyenmai km ON m.maKM = km.maKM
                 WHERE m.trangThai = 1 
                 AND km.ngayBD <= '$today' AND km.ngayKT >= '$today'
                 ORDER BY km.phantramgiam DESC, m.maMH DESC
                 LIMIT 8";
    $result_sale = $conn->query($sql_sale);
    if ($result_sale) {
        while($row = $result_sale->fetch_assoc()) $sale_products[] = $row;
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* --- GIỮ NGUYÊN CSS CŨ --- */
.swiper-notification, .swiper-pagination, .swiper-button-lock,
[aria-live="assertive"], [aria-live="polite"] { display: none !important; }

.main-slider-container {
    width: 100%; height: 480px; margin-bottom: 50px; border-radius: 20px;
    overflow: hidden; box-shadow: 0 10px 30px rgba(233, 30, 99, 0.15);
    position: relative; background: #fdfdfd;
}
.swiper { width: 100%; height: 100%; }
.swiper-slide a { display: block; width: 100%; height: 100%; text-decoration: none; }
.swiper-slide img { width: 100%; height: 100%; object-fit: cover; transition: transform 1.5s ease; }
.swiper-slide-active img { transform: scale(1.08); }
.swiper-button-next, .swiper-button-prev { 
    color: #fff; background: rgba(233, 30, 99, 0.25);
    width: 45px; height: 45px; border-radius: 50%;
    backdrop-filter: blur(4px); border: none;
}
.swiper-button-next:after, .swiper-button-prev:after { font-size: 18px; }

/* CSS Nội dung sản phẩm */
.main-content { max-width: 1300px; margin: 20px auto; padding: 0 20px; font-family: 'Times New Roman', Times, serif; }
.section-title { text-align: center; font-size: 2rem; color: #e91e63; margin: 60px 0 30px; font-weight: 700; border-bottom: 2px solid #ffe1ec; padding-bottom: 10px; display: inline-block; }
.section-wrapper { text-align: center; }

.product-grid { 
    display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; 
}
.product-card { 
    background: #fff; border-radius: 12px; border: 1px solid #f0f0f0; 
    overflow: hidden; transition: 0.3s; text-decoration: none; 
    text-align: center; padding-bottom: 15px;
    display: flex; flex-direction: column; position: relative;
}
.product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(233, 30, 99, 0.15); border-color: #ffe1ec; }

.product-img-box {
    height: 220px; display: flex; align-items: center; justify-content: center; padding: 10px; overflow: hidden;
}
.product-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s; }
.product-card:hover .product-img-box img { transform: scale(1.05); }

.product-name {
    font-weight: 600; color: #333; margin: 10px 10px; font-size: 1.1rem;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden; height: 2.8em;
}

.price-box { margin-top: auto; }
.current-price { color:#e91e63; font-weight:800; font-size: 1.2rem; }
.old-price { color: #999; text-decoration: line-through; font-size: 0.9rem; margin-right: 8px; }

/* Các loại nhãn (Badge) */
.tag-hot {
    position: absolute; top: 10px; left: 10px; background: #EF6C00; color: white;
    padding: 3px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; z-index: 2;
}
.tag-new {
    position: absolute; top: 10px; left: 10px; background: #00BCD4; color: white;
    padding: 3px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; z-index: 2;
}
.tag-sale {
    position: absolute; top: 10px; right: 10px; background: #E91E63; color: white;
    padding: 3px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; z-index: 2;
}
</style>

<div class="main-content">
    
    <div class="main-slider-container">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php if (!empty($slides)): ?>
                    <?php foreach ($slides as $slide): ?>
                        <div class="swiper-slide">
                            <a href="<?php echo htmlspecialchars($slide['linkAnh']); ?>">
                                <img src="uploads/<?php echo $slide['hinhAnh']; ?>" alt="Banner">
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

    <div class="section-wrapper">
        <h2 class="section-title">🔥 SẢN PHẨM NỔI BẬT</h2>
    </div>
    
    <div class="product-grid">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $p): 
                $is_sale = false; $final_price = $p['donGia'];
                if ($p['maKM'] && $today >= $p['ngayBD'] && $today <= $p['ngayKT']) {
                    $is_sale = true; $percent = (float)$p['phantramgiam'];
                    $final_price = $p['donGia'] - ($p['donGia'] * $percent / 100);
                }
            ?>
            <a href="product_detail.php?id=<?php echo $p['maMH']; ?>" class="product-card">
                <?php if ($p['tongBan'] > 0): ?>
                    <div class="tag-hot"><i class="fa-solid fa-fire"></i> HOT</div>
                <?php endif; ?>
                <?php if ($is_sale): ?>
                    <div class="tag-sale">-<?php echo (float)$p['phantramgiam']; ?>%</div>
                <?php endif; ?>

                <div class="product-img-box">
                    <img src="<?php echo htmlspecialchars($p['hinhAnh']); ?>" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                </div>
                <p class="product-name"><?php echo htmlspecialchars($p['tenMH']); ?></p>
                <div class="price-box">
                    <?php if ($is_sale): ?>
                        <span class="old-price"><?php echo number_format($p['donGia'], 0, ',', '.'); ?>đ</span>
                    <?php endif; ?>
                    <span class="current-price"><?php echo number_format($final_price, 0, ',', '.'); ?>đ</span>
                </div>
                <?php if ($p['tongBan'] > 0): ?>
                    <div style="font-size: 0.85rem; color: #777; margin-top: 5px;">Đã bán: <?php echo $p['tongBan']; ?></div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #888; grid-column: 1/-1;">Đang cập nhật sản phẩm nổi bật...</p>
        <?php endif; ?>
    </div>

    <div class="section-wrapper">
        <h2 class="section-title">✨ HÀNG MỚI VỀ</h2>
    </div>

    <div class="product-grid">
        <?php if (!empty($new_products)): ?>
            <?php foreach ($new_products as $p): 
                $is_sale = false; $final_price = $p['donGia'];
                if ($p['maKM'] && $today >= $p['ngayBD'] && $today <= $p['ngayKT']) {
                    $is_sale = true; $percent = (float)$p['phantramgiam'];
                    $final_price = $p['donGia'] - ($p['donGia'] * $percent / 100);
                }
            ?>
            <a href="product_detail.php?id=<?php echo $p['maMH']; ?>" class="product-card">
                <div class="tag-new"><i class="fa-solid fa-star"></i> NEW</div>
                
                <?php if ($is_sale): ?>
                    <div class="tag-sale">-<?php echo (float)$p['phantramgiam']; ?>%</div>
                <?php endif; ?>

                <div class="product-img-box">
                    <img src="<?php echo htmlspecialchars($p['hinhAnh']); ?>" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                </div>
                <p class="product-name"><?php echo htmlspecialchars($p['tenMH']); ?></p>
                <div class="price-box">
                    <?php if ($is_sale): ?>
                        <span class="old-price"><?php echo number_format($p['donGia'], 0, ',', '.'); ?>đ</span>
                    <?php endif; ?>
                    <span class="current-price"><?php echo number_format($final_price, 0, ',', '.'); ?>đ</span>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #888; grid-column: 1/-1;">Đang cập nhật sản phẩm mới...</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($sale_products)): ?>
    <div class="section-wrapper">
        <h2 class="section-title"><i class="fa-solid fa-tags"></i> SĂN DEAL GIÁ SỐC</h2>
    </div>

    <div class="product-grid">
        <?php foreach ($sale_products as $p): 
            $percent = (float)$p['phantramgiam'];
            $final_price = $p['donGia'] - ($p['donGia'] * $percent / 100);
        ?>
        <a href="product_detail.php?id=<?php echo $p['maMH']; ?>" class="product-card">
            <div class="tag-sale" style="background: #D32F2F;">-<?php echo $percent; ?>%</div>
            <div class="product-img-box">
                <img src="<?php echo htmlspecialchars($p['hinhAnh']); ?>" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
            </div>
            <p class="product-name"><?php echo htmlspecialchars($p['tenMH']); ?></p>
            <div class="price-box">
                <span class="old-price"><?php echo number_format($p['donGia'], 0, ',', '.'); ?>đ</span>
                <span class="current-price" style="color: #D32F2F;"><?php echo number_format($final_price, 0, ',', '.'); ?>đ</span>
            </div>
            <div style="font-size: 0.8rem; color: #E91E63; margin-top: 8px; font-weight: 500;">
                <i class="fa-regular fa-clock"></i> Kết thúc: <?php echo date('d/m', strtotime($p['ngayKT'])); ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  var swiper = new Swiper(".mySwiper", {
    loop: true, speed: 1200, 
    autoplay: { delay: 5000, disableOnInteraction: false },
    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    a11y: false, grabCursor: true,
  });
</script>

<?php require("footer.php"); ?>