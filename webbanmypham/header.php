<?php
// BẮT BUỘC: Đặt ở dòng 1, không có khoảng trắng phía trên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// LOGIC MỚI: Kiểm tra nếu biến session 'quyen' tồn tại và có giá trị bằng 1
$isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

// Yêu cầu file kết nối database
require_once("config.php"); 

$categories = [];
$cart_count = 0; // Khởi tạo số lượng giỏ hàng mặc định

// Nếu có database, sẽ chạy đoạn code này
if (isset($conn) && $conn->connect_error === null) {
    // 1. TRUY VẤN DANH MỤC
    $sql_categories = "SELECT maDM, tenDM FROM danhmucsp ORDER BY tenDM ASC";
    $result_categories = $conn->query($sql_categories);

    if ($result_categories && $result_categories->num_rows > 0) {
        while($row = $result_categories->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // 2. LOGIC MỚI: TRUY VẤN SỐ LƯỢNG GIỎ HÀNG THỰC TẾ
    if (isset($_SESSION['tenDangnhap'])) {
        $tenDangNhap = $_SESSION['tenDangnhap'];
        // Truy vấn tổng số lượng (SUM) sản phẩm trong giỏ hàng đang hoạt động (trangthai = 0)
        $sql_cart = "SELECT SUM(ct.soluong) as total 
                     FROM chitietgiohang ct
                     JOIN giohang g ON ct.giohang_id = g.id
                     WHERE g.tenDangNhap = '$tenDangNhap' AND g.trangthai = 0";
        
        $result_cart = $conn->query($sql_cart);
        if ($result_cart && $row_cart = $result_cart->fetch_assoc()) {
            $cart_count = $row_cart['total'] ? $row_cart['total'] : 0;
        }
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    /* ===== GIỮ NGUYÊN TOÀN BỘ CSS CỦA BẠN ===== */
    :root {
        --cosmetics-header-gradient: linear-gradient(90deg, #FFD1DC, #FFDFE8); 
        --cosmetics-accent-color: #E91E63; 
        --cosmetics-light-bg: #fff8fb; 
        --cosmetics-text-dark: #333;
        --cosmetics-orange: #EF6C00; 
    }

    body { margin: 0; font-family: 'Times New Roman', Times, serif; background: #fff8fb; }
    .header-container, .nav-wrapper { max-width: 1300px; margin: auto; padding: 0 20px; }
    .main-header { background: var(--cosmetics-header-gradient); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 8px 0; width: 100%; position: relative; z-index: 30; }
    .header-container { display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap; height: 60px; }
    .header-main-content { display: flex; align-items: center; flex-grow: 1; gap: 30px; min-width: 0; }
    .logo-wrapper { display: flex; flex-direction: column; align-items: center; line-height: 1; text-decoration: none; flex-shrink: 0; }
    .logo { font-size: 30px; font-weight: 800; color: var(--cosmetics-accent-color); letter-spacing: 1.5px; }
    .logo-caption { font-size: 11px; color: #666; font-weight: 400; margin-top: 2px; opacity: 0.9; }
    .search-container-1 { flex-grow: 1; margin: 0; }
    .search-box-1 { width: 100%; position: relative; display: flex; border: 1px solid #ddd; border-radius: 50px; background: #fff; height: 40px; overflow: hidden; }
    .search-box-1 input { border: none; background: transparent; width: 100%; height: 100%; padding: 6px 45px 6px 15px; outline: none; font-size: 0.95rem; font-family: 'Times New Roman', Times, serif; }
    .search-box-1:focus-within { border-color: var(--cosmetics-accent-color); box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.1); }
    .search-box-1 button { position: absolute; top: 0; right: 0; height: 40px; width: 45px; background: transparent; color: #888; border: none; cursor: pointer; transition: color .3s; font-size: 1.1rem; z-index: 2; }
    .search-box-1 button:hover { color: var(--cosmetics-accent-color); }
    .header-actions { display: flex; align-items: center; gap: 25px; flex-shrink: 0; margin-left: 20px; }
    .action-item { text-decoration: none; color: var(--cosmetics-accent-color); display: flex; flex-direction: column; align-items: center; padding: 5px; font-size: 0.8rem; font-weight: 500; cursor: pointer; transition: color .3s; white-space: nowrap; }
    .action-item i { font-size: 20px; margin-bottom: 3px; }
    .action-item:hover { color: #FF69B4; }
    .cart-icon-wrapper { position: relative; color: var(--cosmetics-accent-color); }
    .cart-count { position: absolute; top: -8px; right: -10px; min-width: 18px; height: 18px; background: var(--cosmetics-orange); color: white; font-size: 11px; border-radius: 50%; display: flex; justify-content: center; align-items: center; padding: 1px; font-weight: 700; border: 2px solid #FFDFE8; }
    .admin-link { padding: 6px 10px; border: 1px solid var(--cosmetics-accent-color); border-radius: 6px; background: #fff; color: var(--cosmetics-accent-color) !important; font-weight: 600 !important; display: flex; align-items: center; gap: 5px; transition: all 0.2s ease; }
    .admin-link:hover { background: var(--cosmetics-accent-color); color: white !important; }
    .nav-bar { background: var(--cosmetics-light-bg); border-bottom: 1px solid #e0e0e0; padding: 5px 0; z-index: 20; }
    .nav-wrapper { display: flex; justify-content: flex-start; align-items: center; gap: 20px; }
    .cat-menu { position: relative; display: inline-block; flex-shrink: 0; }
    .cat-btn { padding: 8px 12px; background: transparent; color: var(--cosmetics-text-dark); font-weight: 700; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-family: 'Times New Roman', Times, serif; }
    .cat-btn:hover { background: rgba(233, 30, 99, 0.1); }
    .cat-dropdown { display: none; position: absolute; width: 250px; background: white; margin-top: 8px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; z-index: 100; border: 1px solid #f0f0f0; left: 0; }
    .cat-dropdown a { display: block; padding: 10px 15px; color: var(--cosmetics-text-dark); font-weight: 500; text-decoration: none; border-bottom: 1px solid #f9f9f9; transition: background .2s; font-size: 0.9rem; font-family: 'Times New Roman', Times, serif; }
    .cat-dropdown a:hover { background: #FFD1DC; color: var(--cosmetics-accent-color); }
    .cat-menu.active .cat-dropdown { display: block; }
    .sub-menu ul { list-style: none; padding: 0; margin: 0; display: flex; gap: 5px; }
    .sub-menu ul li a { text-decoration: none; color: var(--cosmetics-text-dark); padding: 8px 12px; border-radius: 4px; font-weight: 500; transition: .3s ease; font-size: 0.95rem; white-space: nowrap; }
    .sub-menu ul li a:hover { background: rgba(233, 30, 99, 0.1); color: var(--cosmetics-accent-color); }
</style>

<header class="main-header">
    <div class="header-container">
        <div class="header-main-content">
            <a class="logo-wrapper" href="index.php">
                <span class="logo">HuonggCosmetics</span>
                <span class="logo-caption">Chất lượng thật - Giá trị thật</span>
            </a>
            
            <div class="search-container-1">
                <form action="products.php" method="GET" class="search-box-1">
                    <input type="text" name="query" placeholder="Tìm sản phẩm, thương hiệu bạn mong muốn..." >
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
        
        <div class="header-actions">
            <?php if(isset($_SESSION['tenDangnhap'])): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin/index.php" class="admin-link" title="Quản trị Hệ thống">
                        <i class="fa-solid fa-user-gear"></i> Admin
                    </a>
                <?php endif; ?>
                
                <div class="action-item" onclick="window.location.href='user_profile.php'">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Xin chào</span>
                </div>
                <a href="logout.php" class="action-item" title="Đăng Xuất">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Đăng Xuất</span>
                </a>
                
            <?php else: ?>
                <a href="login.php" class="action-item">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Đăng nhập | Đăng ký</span>
                </a>
            <?php endif; ?>

            <a href="contact.php" class="action-item" title="Hỗ trợ Khách hàng">
                <i class="fa-solid fa-headset"></i>
                <span>Liên hệ</span>
            </a>
            
            <a href="cart.php" class="action-item cart-icon-wrapper" title="Giỏ hàng">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Giỏ hàng</span>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>
        </div>
    </div>
</header>

<div class="nav-bar">
    <div class="nav-wrapper">
        <div class="cat-menu">
            <button class="cat-btn">
                <i class="fa-solid fa-list"></i> DANH MỤC
            </button>
            <div class="cat-dropdown">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="products.php?maDM=<?php echo htmlspecialchars($cat['maDM']); ?>">
                            <?php echo htmlspecialchars($cat['tenDM']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a href="#" style="text-align: center; color: #888;">Không có danh mục nào.</a>
                <?php endif; ?>
            </div>
        </div>
        
        <nav class="sub-menu">
            <ul>
                <li><a href="brands.php">THƯƠNG HIỆU</a></li>
            </ul>
        </nav>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catMenu = document.querySelector('.cat-menu');
        const catButton = document.querySelector('.cat-btn');

        catButton.addEventListener('click', function(event) {
            event.stopPropagation();
            catMenu.classList.toggle('active');
        });

        document.addEventListener('click', function(event) {
            if (catMenu.classList.contains('active') && !catMenu.contains(event.target)) {
                catMenu.classList.remove('active');
            }
        });
    });
</script>