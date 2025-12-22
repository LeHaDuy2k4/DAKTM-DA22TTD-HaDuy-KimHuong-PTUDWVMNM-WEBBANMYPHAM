<?php
// BẮT BUỘC: Đặt ở dòng 1, không có khoảng trắng phía trên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- ================= HEADER (SAFE FOR REQUIRE) ================= -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #fff8fb;
    }

    .main-header {
        background: linear-gradient(90deg, #FFD1DC, #FFDFE8);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        padding: 12px 0;
        width: 100%;
    }

    .header-container {
        max-width: 1300px;
        margin: auto;
        padding: 0 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .logo {
        font-size: 23px;
        font-weight: 700;
        color: #6b6b6b;
        text-decoration: none;
    }

    .main-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 15px;
    }

    .main-menu ul li a {
        text-decoration: none;
        color: #444;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        transition: .25s;
    }

    .main-menu ul li a:hover {
        background: #f9b6c7;
        color: white;
    }

    .header-center {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .search-box {
        width: 260px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        height: 32px;
        border-radius: 20px;
        border: 1px solid #e4b4c2;
        padding: 6px 35px 6px 12px;
        outline: none;
    }

    .search-box button {
        position: absolute;
        top: 4px;
        right: 6px;
        height: 24px;
        width: 24px;
        background: #e91e63;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-right a, .header-right span {
        text-decoration: none;
        font-weight: 600;
        color: #444;
    }

    .header-right a:hover {
        color: #e91e63;
    }

    .cart-icon {
        font-size: 22px;
        position: relative;
    }

    .cart-icon::after {
        content: attr(data-count);
        position: absolute;
        top: -8px;
        right: -12px;
        width: 18px;
        height: 18px;
        background: #ff3f3f;
        color: white;
        font-size: 12px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* ===== CATEGORY BAR ===== */
    .category-bar {
        background: #fff;
        padding: 10px 0;
        border-bottom: 1px solid #ffd6e5;
        width: 100%;
    }

    .category-wrapper {
        max-width: 1300px;
        margin: auto;
        padding: 0 10px;
    }

    .cat-menu {
        position: relative;
        display: inline-block;
    }

    .cat-btn {
        padding: 10px 16px;
        background: #e91e63;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .cat-dropdown {
        display: none;
        position: absolute;
        width: 220px;
        background: white;
        margin-top: 8px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        overflow: hidden;
        z-index: 100;
    }

    .cat-dropdown a {
        display: block;
        padding: 12px 15px;
        color: #444;
        font-weight: 600;
        text-decoration: none;
    }

    .cat-dropdown a:hover {
        background: #ffe1ec;
        color: #e91e63;
    }

    .cat-menu:hover .cat-dropdown {
        display: block;
    }
</style>

<header class="main-header">
    <div class="header-container">

        <div class="header-left">
            <a class="logo" href="index.php">MyPhamShop</a>

            <nav class="main-menu">
                <ul>
                    <li><a href="index.php">Trang Chủ</a></li>
                    <li><a href="#">Sản Phẩm</a></li>
                    <li><a href="#">Khuyến Mãi</a></li>
                    <li><a href="#">Giới Thiệu</a></li>
                    <li><a href="#">Liên Hệ</a></li>
                </ul>
            </nav>
        </div>

        <div class="header-center">
            <div class="search-box">
                <input type="text" placeholder="Tìm kiếm sản phẩm...">
                <button><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>

        <div class="header-right">

            <?php if(isset($_SESSION['tenDangnhap'])): ?>
                <span style="color:#d63384;">Xin chào, <?php echo $_SESSION['tenDangnhap']; ?></span>
                <a href="logout.php" style="color:#e91e63;">Đăng Xuất</a>
            <?php else: ?>
                <a href="login.php">Đăng Nhập</a>
                <a href="signup.php">Đăng Ký</a>
            <?php endif; ?>

            <a href="#" class="cart-icon" data-count="0">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
        </div>

    </div>
</header>

<div class="category-bar">
    <div class="category-wrapper">

        <div class="cat-menu">
            <button class="cat-btn">
                <i class="fa-solid fa-list"></i> Danh Mục
            </button>

            <div class="cat-dropdown">
                <a href="#">Trang Điểm</a>
                <a href="#">Chăm Sóc Da</a>
                <a href="#">Chăm Sóc Tóc</a>
                <a href="#">Dụng Cụ Làm Đẹp</a>
            </div>
        </div>

    </div>
</div>
