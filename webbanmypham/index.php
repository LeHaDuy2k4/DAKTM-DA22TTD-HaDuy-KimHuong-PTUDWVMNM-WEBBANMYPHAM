<?php
// Tên file: index.php
session_start(); 
// Các biến logic PHP (như $isAdmin, $categories) sẽ được xử lý trong header.php
require("header.php"); 

// ==============================================================================
// LOGIC RIÊNG CỦA TRANG CHỦ
// ==============================================================================

// GIẢ LẬP DỮ LIỆU SẢN PHẨM NỔI BẬT HOẶC KHUYẾN MÃI
// TRONG THỰC TẾ, ĐOẠN NÀY SẼ LẤY DỮ LIỆU TỪ CSDL: SELECT * FROM sanpham WHERE status='featured' LIMIT 8
$featured_products = [
    ['id' => 101, 'name' => 'Kem Dưỡng Retinol Tái Tạo Da', 'price' => 750000, 'old_price' => 890000, 'img' => 'product_1.jpg', 'discount' => '15%'],
    ['id' => 102, 'name' => 'Serum Niacinamide 10% Kiềm Dầu', 'price' => 380000, 'old_price' => 450000, 'img' => 'product_2.jpg', 'discount' => '10%'],
    ['id' => 103, 'name' => 'Son Kem Lì Mịn Môi Màu Đỏ Gạch', 'price' => 299000, 'old_price' => 350000, 'img' => 'product_3.jpg', 'discount' => '10%'],
    ['id' => 104, 'name' => 'Kem Chống Nắng Phổ Rộng SPF50+', 'price' => 495000, 'old_price' => null, 'img' => 'product_4.jpg', 'discount' => 'Mới'],
    ['id' => 105, 'name' => 'Mặt Nạ Giấy Cấp Ẩm Tức Thì', 'price' => 55000, 'old_price' => 65000, 'img' => 'product_5.jpg', 'discount' => '15%'],
    ['id' => 106, 'name' => 'Tẩy Trang Dạng Dầu Hoa Hồng', 'price' => 320000, 'old_price' => 380000, 'img' => 'product_6.jpg', 'discount' => '15%'],
    ['id' => 107, 'name' => 'Phấn Nước Cushion Che Phủ Tốt', 'price' => 610000, 'old_price' => null, 'img' => 'product_7.jpg', 'discount' => 'Hot'],
    ['id' => 108, 'name' => 'Dầu Gội & Xả Keratin Phục Hồi', 'price' => 245000, 'old_price' => 290000, 'img' => 'product_8.jpg', 'discount' => '15%'],
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* ========================================================================= */
/* --- CSS RIÊNG CỦA TRANG INDEX --- */
/* (Tông màu hồng đã được định nghĩa trong header.php) */
/* ========================================================================= */

.main-content {
    max-width: 1300px;
    margin: 20px auto;
    padding: 0 20px;
}

/* --- Section Titles --- */
.section-title {
    text-align: center;
    font-size: 2rem;
    color: var(--cosmetics-accent-color);
    margin: 40px 0 30px;
    position: relative;
    font-weight: 700;
}

.section-title::after {
    content: '';
    display: block;
    width: 80px;
    height: 3px;
    background: var(--cosmetics-accent-color);
    margin: 10px auto 0;
    border-radius: 5px;
}

/* --- Banner (Slider) --- */
.main-slider {
    height: 350px;
    background: #FFD1DC; /* Màu nền hồng nhạt nổi bật */
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: var(--cosmetics-accent-color);
    font-weight: 800;
    text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
    background-image: url('images/main_banner_bg.jpg'); /* Thay thế bằng hình ảnh banner */
    background-size: cover;
    background-position: center;
    text-align: center;
    line-height: 1.2;
}

/* --- Product Grid & Card --- */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.product-card {
    position: relative;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: center;
    text-decoration: none;
    display: block;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(233, 30, 99, 0.1);
}

.product-img-wrapper {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fcfcfc;
    position: relative;
}

.product-discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--cosmetics-orange);
    color: white;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.9rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.product-img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
    transition: transform 0.3s;
}

.product-info {
    padding: 15px;
}

.product-name {
    font-size: 1rem;
    color: var(--cosmetics-text-dark);
    margin-bottom: 8px;
    height: 40px; /* Giữ chiều cao cố định */
    overflow: hidden;
    line-height: 1.2;
}

.price-container {
    margin-bottom: 10px;
}

.product-price {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--cosmetics-accent-color);
}

.product-old-price {
    font-size: 0.9rem;
    color: #999;
    text-decoration: line-through;
    margin-right: 8px;
}

.btn-buy {
    display: block;
    width: 90%;
    padding: 10px 0;
    margin: 0 auto;
    background: var(--cosmetics-accent-color);
    color: white;
    border: none;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-buy:hover {
    background: #C2185B;
}

/* --- Brand Logos --- */
.brand-section {
    padding: 30px 0;
}

.brand-logos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr); /* 6 logo trên 1 hàng */
    gap: 15px;
    max-width: 1000px;
    margin: 0 auto;
}

.brand-logo-item {
    padding: 15px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 6px;
    text-align: center;
    transition: box-shadow 0.3s, border-color 0.3s;
    text-decoration: none;
    color: #555;
    font-weight: 600;
}
.brand-logo-item:hover {
    box-shadow: 0 4px 10px rgba(233, 30, 99, 0.1);
    border-color: var(--cosmetics-accent-color);
}
</style>


<?php 
// Ghi chú: Tôi đã đặt logic header trong file index này để đảm bảo style và biến được tải,
// nhưng trong môi trường thực tế, bạn chỉ cần require("header.php");

// Để file chạy độc lập trong môi trường test:
// Nếu bạn đã có file header.php riêng, hãy bỏ comment dòng dưới:
// require("header.php"); 
?>


<div class="main-content">
    
    <div class="main-slider">
        <div>
            MÙA LỄ HỘI RỰC RỠ 💖<br>
            **GIẢM SỐC TỚI 50%** CÁC DÒNG MỸ PHẨM CAO CẤP!
        </div>
    </div>

    <h2 class="section-title">🔥 SẢN PHẨM NỔI BẬT TRONG TUẦN</h2>
    <div class="product-grid">
        <?php foreach ($featured_products as $product): ?>
        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-img-wrapper">
                <img src="images/placeholder.jpg" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                <span class="product-discount-badge"><?php echo $product['discount']; ?></span>
            </div>
            <div class="product-info">
                <p class="product-name"><?php echo htmlspecialchars($product['name']); ?></p>
                <div class="price-container">
                    <?php if ($product['old_price']): ?>
                        <span class="product-old-price"><?php echo number_format($product['old_price'], 0, ',', '.'); ?>đ</span>
                    <?php endif; ?>
                    <span class="product-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                </div>
                <button class="btn-buy">THÊM VÀO GIỎ</button>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    
    <h2 class="section-title">⭐ MUA SẮM THEO THƯƠNG HIỆU</h2>
    <div class="brand-section">
        <div class="brand-logos-grid">
            <a href="brands.php?id=1" class="brand-logo-item">LANEIGE</a>
            <a href="brands.php?id=2" class="brand-logo-item">INNISFREE</a>
            <a href="brands.php?id=3" class="brand-logo-item">MAC</a>
            <a href="brands.php?id=4" class="brand-logo-item">SKINCEUTICALS</a>
            <a href="brands.php?id=5" class="brand-logo-item">COSRX</a>
            <a href="brands.php?id=6" class="brand-logo-item">LA ROCHE</a>
        </div>
    </div>
    
    <h2 class="section-title">💖 KHÁM PHÁ DANH MỤC CHĂM SÓC DA</h2>
    <div class="product-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="product-card" style="background: #FFF0F5;">
             <div class="product-info" style="padding: 30px;">
                <h3 style="color: var(--cosmetics-accent-color);">SERUM & ĐẶC TRỊ</h3>
                <p style="color: #555;">Công nghệ tiên tiến nhất cho làn da hoàn hảo.</p>
                <a href="products.php?maDM=1" class="btn-buy" style="background: var(--cosmetics-accent-color);">XEM NGAY</a>
            </div>
        </div>
        <div class="product-card" style="background: #F0F5F2;">
             <div class="product-info" style="padding: 30px;">
                <h3 style="color: #17A57A;">SẢN PHẨM THUẦN CHAY</h3>
                <p style="color: #555;">Thiên nhiên và lành tính cho mọi loại da.</p>
                <a href="products.php?tag=vegan" class="btn-buy" style="background: #20c997;">XEM NGAY</a>
            </div>
        </div>
        <div class="product-card" style="background: #FFD1DC;">
             <div class="product-info" style="padding: 30px;">
                <h3 style="color: var(--cosmetics-accent-color);">CHỐNG NẮNG CAO CẤP</h3>
                <p style="color: #555;">Bảo vệ da tuyệt đối khỏi tia UV.</p>
                <a href="products.php?tag=sunscreen" class="btn-buy" style="background: var(--cosmetics-orange);">XEM NGAY</a>
            </div>
        </div>
    </div>

</div>

<?php
 require("footer.php");
// Ghi chú: Nếu bạn muốn hiển thị footer, bạn cần bỏ comment dòng trên
?>