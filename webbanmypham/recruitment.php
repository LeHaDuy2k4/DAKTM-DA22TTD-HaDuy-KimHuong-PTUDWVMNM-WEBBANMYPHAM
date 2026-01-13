<?php
// Tên file: recruitment.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("config.php"); 
require("header.php"); 

// DỮ LIỆU TUYỂN DỤNG MẪU
$jobs = [
    [
        'id' => 1,
        'title' => 'Nhân viên Tư vấn Bán hàng',
        'salary' => '7.000.000 - 10.000.000đ',
        'location' => 'Phường Trà Vinh, Tỉnh Vĩnh Long',
        'deadline' => '30/12/2025',
        'desc' => 'Tư vấn sản phẩm mỹ phẩm cho khách hàng tại cửa hàng. Chăm sóc khách hàng qua Fanpage.'
    ],
    [
        'id' => 2,
        'title' => 'Content Marketing',
        'salary' => '9.000.000 - 12.000.000đ',
        'location' => 'Phường Trà Vinh, Tỉnh Vĩnh Long',
        'deadline' => '15/01/2026',
        'desc' => 'Viết bài PR sản phẩm, quản lý nội dung Fanpage và Website. Có khả năng chụp ảnh cơ bản.'
    ],
    [
        'id' => 3,
        'title' => 'Nhân viên Kho & Vận đơn',
        'salary' => '6.500.000 - 8.000.000đ',
        'location' => 'Phường Trà Vinh, Tỉnh Vĩnh Long',
        'deadline' => '30/12/2025',
        'desc' => 'Kiểm kê hàng hóa nhập xuất. Đóng gói đơn hàng online và bàn giao cho đơn vị vận chuyển.'
    ]
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* --- GIỮ NGUYÊN LAYOUT CHUNG --- */
.main-content { max-width: 1300px; margin: 20px auto; padding: 0 20px; font-family: 'Times New Roman', Times, serif; }
.section-title { text-align: center; font-size: 2rem; color: #e91e63; margin: 40px 0 30px; font-weight: 700; border-bottom: 2px solid #ffe1ec; padding-bottom: 10px; display: inline-block; }
.section-wrapper { text-align: center; margin-bottom: 40px; }

/* --- DANH SÁCH VIỆC LÀM --- */
.job-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }

.job-card {
    background: #fff; border-radius: 12px; border: 1px solid #f0f0f0; padding: 25px;
    transition: 0.3s; position: relative; display: flex; flex-direction: column; height: 100%;
}
.job-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(233, 30, 99, 0.1); border-color: #ffe1ec; }

.job-title { font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 10px; }
.job-info { list-style: none; padding: 0; margin: 0 0 20px 0; font-size: 0.95rem; color: #666; }
.job-info li { margin-bottom: 8px; display: flex; align-items: center; gap: 10px; }
.job-info i { color: #e91e63; width: 20px; text-align: center; }

.job-desc { font-size: 0.9rem; color: #555; line-height: 1.5; margin-bottom: 20px; flex-grow: 1; }

.btn-apply {
    background: #e91e63; color: white; text-decoration: none; padding: 10px 20px;
    border-radius: 6px; text-align: center; font-weight: 600; display: block;
    transition: 0.3s;
}
.btn-apply:hover { background: #c2185b; box-shadow: 0 4px 10px rgba(233, 30, 99, 0.3); }

/* --- KHỐI LIÊN HỆ --- */
.contact-recruitment {
    background: #fff; border-radius: 12px; padding: 40px; margin-top: 60px;
    text-align: center; border: 1px dashed #e91e63;
}
.contact-recruitment h3 { color: #e91e63; margin-bottom: 15px; }
.contact-recruitment p { font-size: 1.1rem; color: #555; }
.mail-link { color: #e91e63; font-weight: 700; text-decoration: none; }
</style>

<div class="main-content">
    
    <div class="section-wrapper">
        <h2 class="section-title"><i class="fa-solid fa-briefcase"></i> VỊ TRÍ ĐANG TUYỂN</h2>
    </div>

    <div class="job-grid">
        <?php foreach ($jobs as $job): ?>
        <div class="job-card">
            <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
            
            <ul class="job-info">
                <li><i class="fa-solid fa-money-bill-wave"></i> <strong>Lương:</strong> <?php echo $job['salary']; ?></li>
                <li><i class="fa-solid fa-location-dot"></i> <strong>Địa điểm:</strong> <?php echo $job['location']; ?></li>
                <li><i class="fa-regular fa-clock"></i> <strong>Hạn nộp:</strong> <?php echo $job['deadline']; ?></li>
            </ul>

            <div class="job-desc">
                <?php echo htmlspecialchars($job['desc']); ?>
            </div>

            <a href="mailto:hr@huonggcosmetics.com?subject=Ứng tuyển vị trí <?php echo $job['title']; ?>" class="btn-apply">
                ỨNG TUYỂN NGAY
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="contact-recruitment">
        <h3><i class="fa-solid fa-envelope-open-text"></i> Bạn chưa tìm thấy vị trí phù hợp?</h3>
        <p>Đừng ngần ngại gửi CV của bạn về địa chỉ email: <a href="mailto:huonggcosmeticshr@gmail.com" class="mail-link">huonggcosmeticshr@gmail.com</a></p>
        <p>Chúng tôi sẽ liên hệ lại ngay khi có vị trí phù hợp với năng lực của bạn.</p>
    </div>

</div>

<?php require("footer.php"); ?>