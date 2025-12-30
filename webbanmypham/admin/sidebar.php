<?php
// BẮT BUỘC: Đặt ở dòng 1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Giả định logic kiểm tra đăng nhập/Admin được thực hiện ở đây hoặc file khác

// Kiểm tra tên file hiện tại để đặt trạng thái active
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* ================== ADMIN SIDEBAR STYLES ================== */

/* 1. Thiết lập Sidebar cố định */
.sidebar {
    width: 250px;
    height: 100vh; /* Chiếm toàn bộ chiều cao màn hình */
    position: fixed; /* Cố định vị trí */
    top: 0;
    left: 0;
    background-color: #ffffff; /* Nền trắng */
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    z-index: 1000;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
}

/* 2. Style cho Logo/Brand */
.sidebar-brand {
    padding: 0 20px 20px 20px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #e91e63; /* Màu hồng chủ đạo */
    border-bottom: 1px solid #ffe1ec; /* Đường kẻ nhẹ */
    margin-bottom: 15px;
    letter-spacing: 0.5px;
}

.sidebar-brand span {
    color: #444; /* Phần chữ Admin màu tối hơn */
    font-weight: 500;
    font-size: 1.3rem;
}

/* 3. Style cho Menu */
.sidebar-menu {
    flex-grow: 1; /* Cho phép menu chiếm phần còn lại của sidebar */
    overflow-y: auto; /* Thêm scroll nếu menu quá dài */
}

/* Tùy chỉnh thanh cuộn cho đẹp (Chrome/Safari/Edge) */
.sidebar-menu::-webkit-scrollbar {
    width: 5px;
}
.sidebar-menu::-webkit-scrollbar-thumb {
    background: #ffd1dc; 
    border-radius: 5px;
}

.sidebar-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu ul li a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    text-decoration: none;
    color: #555;
    font-weight: 500;
    transition: 0.3s;
    border-left: 5px solid transparent; /* Đường viền để highlight */
}

.sidebar-menu ul li a:hover {
    background-color: #fff0f5; /* Màu nền hồng nhạt khi hover */
    color: #e91e63; /* Chữ màu hồng đậm */
}

.sidebar-menu ul li a i {
    font-size: 1.1rem;
    width: 35px; /* Đảm bảo icon có khoảng cách đồng nhất */
    text-align: center;
}

.sidebar-menu ul li a span {
    font-size: 0.95rem;
}

/* 4. Style cho liên kết đang hoạt động (active) */
.sidebar-menu ul li a.active {
    background-color: #f9b6c7; /* Màu nền hồng đậm hơn */
    color: white;
    border-left: 5px solid #e91e63; /* Highlight mạnh hơn */
}

.sidebar-menu ul li a.active i {
    color: white;
}

/* 5. Style riêng cho nút Đăng xuất */
.logout-btn {
    border-top: 1px solid #eee;
    margin-top: auto; /* Đẩy xuống dưới cùng nếu dùng flex column */
    color: #ff3f3f !important; /* Màu đỏ cho Đăng xuất */
}

.logout-btn:hover {
    background-color: #ffeded;
}

/* ================== MAIN CONTENT LAYOUT ================== */
.main-content {
    margin-left: 250px; /* Bằng chiều rộng của sidebar */
    padding: 20px;
}
</style>


<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-spa"></i> Huongg<span>Admin</span>
    </div>
    
    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>Tổng quan</span>
                </a>
            </li>

            <li>
                <a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group"></i> <span>Danh mục</span>
                </a>
            </li>

            <li>
                <a href="brands.php" class="<?php echo ($current_page == 'brands.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tags"></i> <span>Thương hiệu</span>
                </a>
            </li>

            <li>
                <a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-bottle-droplet"></i>
                    <span>Mặt hàng</span>
                </a>
            </li>

            <li>
                <a href="promotions.php" class="<?php echo ($current_page == 'promotions.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Khuyến mãi</span>
                </a>
            </li>

            <li>
                <a href="reviews.php" class="<?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-star"></i>
                    <span>Đánh giá</span>
                </a>
            </li>

            <li>
                <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Đơn hàng</span>
                </a>
            </li>

            <li>
                <a href="users.php" class="<?php echo ($current_page == 'users.php' || $current_page == 'user_add.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i>
                    <span>Người dùng</span>
                </a>
            </li>

            <li>
                <a href="contacts.php" class="<?php echo ($current_page == 'contacts.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Liên hệ</span>
                </a>
            </li>
            
            <li>
                <a href="../index.php">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Website</span>
                </a>
            </li>
            
            <li>
                <a href="../logout.php" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Đăng xuất</span>
                </a>
            </li>
        </ul>
    </div>
</div>