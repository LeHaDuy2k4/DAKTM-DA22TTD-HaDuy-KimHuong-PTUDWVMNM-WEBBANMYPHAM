<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HuonggCosmetics</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ===== Cài đặt chung ===== */
        body {
            margin: 0;
            /* Đổi font chữ sang Times New Roman */
            font-family: 'Times New Roman', Times, serif; 
        }

        /* ===== FOOTER ===== */
        footer {
            /* GIỮ LẠI BACKGROUND HỒNG GRADIENT BAN ĐẦU */
            background: linear-gradient(90deg, #FFD1DC, #FFDFE8); 
            color: #333;
            padding: 40px 20px 15px 20px;
            /* Đổi font chữ sang Times New Roman */
            font-family: 'Times New Roman', Times, serif; 
            box-shadow: 0 -3px 15px rgba(0, 0, 0, 0.05); 
            /* Xóa border-top để trở về thiết kế ban đầu */
            border-top: none; 
        }

        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; 
            max-width: 1300px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1 1 240px; 
            padding: 0 10px; 
            margin-bottom: 25px;
            box-sizing: border-box;
        }

        .footer-section h3 {
            margin-bottom: 20px;
            font-size: 1.25rem; /* Tăng kích thước tiêu đề cho Times New Roman */
            font-weight: 700;
            color: #e91e63; /* Màu hồng chủ đạo cho tiêu đề */
            /* Giảm nhẹ border-bottom để hợp với nền hồng */
            border-bottom: 1px solid #ffb7b7; 
            padding-bottom: 5px;
        }
        
        /* --- About/Logo Section --- */
        .footer-section.about h3 {
            font-size: 1.8rem;
            color: #e91e63;
            font-weight: 800;
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 15px;
        }


        .footer-section p,
        .footer-section ul,
        .footer-section li {
            color: #333; /* Màu chữ tối, rõ ràng */
            font-size: 1rem; /* Tăng kích thước chữ thường */
            line-height: 1.8;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .footer-section ul li a:hover {
            color: #ffffff; /* Màu trắng nổi bật trên nền hồng */
            text-decoration: underline;
        }
        
        /* --- Social Icons Style --- */
        .social-icons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .social-icons a {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 38px;
            height: 38px;
            background: #e91e63; /* Nền hồng đậm */
            color: white; /* Icon màu trắng */
            font-size: 18px;
            border-radius: 50%;
            transition: background 0.3s ease, color 0.3s ease;
            border: 1px solid #d63384;
        }

        .social-icons a:hover {
            background: #d63384; /* Màu đậm hơn khi hover */
            color: white; 
        }

        /* --- Footer Bottom --- */
        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #555;
            border-top: 1px solid #ffb7b7; /* Đường kẻ hồng nhạt */
            padding-top: 15px;
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
                padding: 0 20px;
            }
            
            .footer-section h3 {
                border-bottom: none;
            }

            .social-icons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<footer>
    <div class="footer-container">
        <div class="footer-section about">
            <h3>HuonggCosmestics</h3>
            <p>Chúng tôi cung cấp các sản phẩm chăm sóc sắc đẹp chất lượng, an toàn và uy tín được chọn lọc kỹ lưỡng, phù hợp với mọi loại da.</p>
        </div>

        <div class="footer-section links">
            <h3>Danh Mục</h3>
            <ul>
                <li><a href="#"><i class="fas fa-arrow-right" style="font-size: 0.8em; margin-right: 5px;"></i> Trang Chủ</a></li>
                <li><a href="#"><i class="fas fa-arrow-right" style="font-size: 0.8em; margin-right: 5px;"></i> Sản Phẩm Hot</a></li>
                <li><a href="#"><i class="fas fa-arrow-right" style="font-size: 0.8em; margin-right: 5px;"></i> Khuyến Mãi</a></li>
                <li><a href="#"><i class="fas fa-arrow-right" style="font-size: 0.8em; margin-right: 5px;"></i> Chính Sách Đổi Trả</a></li>
                <li><a href="#"><i class="fas fa-arrow-right" style="font-size: 0.8em; margin-right: 5px;"></i> Tuyển Dụng</a></li>
            </ul>
        </div>

        <div class="footer-section contact">
            <h3>Thông Tin Liên Hệ</h3>
            <p><i class="fas fa-envelope" style="margin-right: 8px;"></i> Email: HuonggCosmestics@gmail.com</p>
            <p><i class="fas fa-phone-alt" style="margin-right: 8px;"></i> Hotline: 0123 456 789</p>    
            <p><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Địa chỉ: 123 Đường Làm Đẹp, TP. HCM</p>
        </div>

        <div class="footer-section social">
            <h3>Kết Nối Với Chúng Tôi</h3>
            <div class="social-icons">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="Youtube"><i class="fab fa-youtube"></i></a>
                <a href="#" title="TikTok"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; 2025 HuonggCosmestics. Đã đăng ký bản quyền.
    </div>
</footer>

</body>
</html>