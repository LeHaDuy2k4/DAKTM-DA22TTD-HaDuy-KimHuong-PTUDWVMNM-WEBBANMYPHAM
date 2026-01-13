<?php
// Tên file: policy.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("config.php"); 
require("header.php"); 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* --- GIỮ NGUYÊN LAYOUT CHUNG --- */
.main-content { max-width: 1300px; margin: 20px auto; padding: 0 20px; font-family: 'Times New Roman', Times, serif; }
.section-title { text-align: center; font-size: 2rem; color: #e91e63; margin: 40px 0 30px; font-weight: 700; border-bottom: 2px solid #ffe1ec; padding-bottom: 10px; display: inline-block; }
.section-wrapper { text-align: center; margin-bottom: 40px; }

/* --- KHỐI CHÍNH SÁCH --- */
.policy-container {
    background: #fff; border-radius: 15px; padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f0f0f0;
}

.policy-alert {
    background: #fff3cd; color: #856404; padding: 20px; border-radius: 8px;
    border: 1px solid #ffeeba; margin-bottom: 30px; display: flex; align-items: center; gap: 15px;
}
.policy-alert i { font-size: 2rem; }

.policy-content h3 { color: #e91e63; margin-top: 25px; margin-bottom: 15px; font-size: 1.3rem; border-left: 4px solid #e91e63; padding-left: 10px; }
.policy-content p { line-height: 1.6; color: #555; margin-bottom: 15px; font-size: 1.05rem; }
.policy-content ul { padding-left: 20px; color: #555; margin-bottom: 20px; }
.policy-content li { margin-bottom: 8px; }

.note-box {
    background: #f8f9fa; border-left: 4px solid #333; padding: 15px;
    font-style: italic; color: #666; margin-top: 30px;
}
</style>

<div class="main-content">
    
    <div class="section-wrapper">
        <h2 class="section-title"><i class="fa-solid fa-file-shield"></i> CHÍNH SÁCH ĐỔI TRẢ</h2>
    </div>

    <div class="policy-container">
        <div class="policy-alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>LƯU Ý QUAN TRỌNG:</strong> Hiện tại, HuonggCosmetics <strong>KHÔNG HỖ TRỢ</strong> chính sách đổi trả hàng hóa dưới mọi hình thức, trừ trường hợp lỗi phát sinh từ phía chúng tôi (giao sai hàng, hàng lỗi kỹ thuật).
            </div>
        </div>

        <div class="policy-content">
            <h3>1. Quy định chung</h3>
            <p>Để đảm bảo chất lượng sản phẩm và vệ sinh an toàn cho khách hàng, chúng tôi áp dụng chính sách <strong>KHÔNG ĐỔI TRẢ</strong> đối với tất cả các sản phẩm mỹ phẩm đã bán ra. Quý khách vui lòng kiểm tra kỹ sản phẩm trước khi thanh toán và nhận hàng.</p>

            <h3>2. Các trường hợp được hỗ trợ xử lý (Ngoại lệ)</h3>
            <p>Chúng tôi chỉ xem xét đổi hàng (1 đổi 1) trong vòng <strong>24 giờ</strong> kể từ khi nhận hàng đối với các trường hợp sau:</p>
            <ul>
                <li>Sản phẩm bị giao sai so với đơn đặt hàng (sai loại, sai màu sắc).</li>
                <li>Sản phẩm bị hư hỏng, bể vỡ trong quá trình vận chuyển (Cần có video quay lại quá trình mở hộp).</li>
                <li>Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất (vòi bơm hỏng, kết cấu sản phẩm bị biến đổi...).</li>
            </ul>

            <h3>3. Điều kiện tiếp nhận khiếu nại</h3>
            <p>Để được hỗ trợ xử lý trong các trường hợp ngoại lệ nêu trên, Quý khách cần đảm bảo:</p>
            <ul>
                <li>Sản phẩm còn nguyên tem mác, chưa qua sử dụng.</li>
                <li>Có video clip quay lại quá trình mở hộp (unbox) rõ nét, không cắt ghép.</li>
                <li>Thông báo cho bộ phận CSKH ngay trong vòng 24h sau khi nhận hàng.</li>
            </ul>

            <div class="note-box">
                Mọi thắc mắc hoặc cần hỗ trợ, xin vui lòng liên hệ Hotline: <strong> 0865 456 789</strong> hoặc gửi email về: <strong>huonggcosmestics@gmail.com</strong>.
            </div>
        </div>
    </div>

</div>

<?php require("footer.php"); ?>