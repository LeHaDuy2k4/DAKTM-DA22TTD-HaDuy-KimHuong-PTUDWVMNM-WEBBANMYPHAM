<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer - MyPhamShop</title>

    <!-- Font Awesome để dùng icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ===== FOOTER ===== */
       footer {
    background: linear-gradient(90deg, #FFD1DC, #FFDFE8);
    color: #333;
    padding: 40px 20px 20px 20px;
    font-family: Arial, sans-serif;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}


        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly; /* Canh đều các section */
            max-width: 1300px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1 1 220px; /* Mỗi section tối thiểu 220px, co dãn đều */
            padding: 0 15px; /* Khoảng cách nội dung bên trong */
            box-sizing: border-box;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .footer-section p,
        .footer-section ul,
        .footer-section li,
        .footer-section a {
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            text-decoration: none;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a:hover {
            color: #ffffff;
        }

        .social-icons a {
            display: inline-block;
            margin-right: 10px;
            color: #333;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            border-top: 1px solid #adadad;
            padding-top: 10px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .footer-container {
                flex-direction: column;
                align-items: center;
            }

            .footer-section {
                margin: 15px 0;
                text-align: center;
            }

            .social-icons a {
                margin: 0 8px;
            }
        }
    </style>
</head>
<body>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="footer-container">
        <!-- GIỚI THIỆU -->
        <div class="footer-section about">
            <h3>MyPhamShop</h3>
            <p>Chúng tôi cung cấp các sản phẩm chăm sóc sắc đẹp chất lượng, an toàn và uy tín cho mọi khách hàng.</p>
        </div>

        <!-- LIÊN KẾT NHANH -->
        <div class="footer-section links">
            <h3>Liên Kết Nhanh</h3>
            <ul>
                <li><a href="#">Trang Chủ</a></li>
                <li><a href="#">Sản Phẩm</a></li>
                <li><a href="#">Khuyến Mãi</a></li>
                <li><a href="#">Giới Thiệu</a></li>
                <li><a href="#">Liên Hệ</a></li>
            </ul>
        </div>

        <!-- LIÊN HỆ -->
        <div class="footer-section contact">
            <h3>Liên Hệ</h3>
            <p>Email: support@myphamshop.com</p>
            <p>Hotline: 0123 456 789</p>
            <p>Địa chỉ: 123 Đường Làm Đẹp, TP. HCM</p>
        </div>

        <!-- MẠNG XÃ HỘI -->
        <div class="footer-section social">
            <h3>Mạng Xã Hội</h3>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; 2025 MyPhamShop. All rights reserved.
    </div>
</footer>

</body>
</html>
