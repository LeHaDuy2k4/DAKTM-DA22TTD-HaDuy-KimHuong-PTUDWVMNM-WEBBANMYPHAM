<?php        
    require("sidebar.php");         
?>

<style>
/* CSS Riêng cho Dashboard */
.main-content {
    /* Giả định sidebar rộng 250px */
    margin-left: 250px; 
    padding: 25px;
    background-color: #fff8fb; 
}

.dashboard-title {
    color: #e91e63;
    margin-bottom: 25px;
}

/* --- Metric Cards --- */
.metric-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.card-box {
    flex: 1;
    min-width: 250px;
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-info h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: #888;
    font-weight: 500;
}

.card-info h1 {
    margin: 0;
    font-size: 2rem;
    color: #444;
    font-weight: 700;
}

.card-icon {
    font-size: 2.5rem;
    padding: 15px;
    border-radius: 50%;
    color: white;
    opacity: 0.8;
}

/* Màu sắc cho từng loại Card */
.users-bg { background-color: #ffb7b7; }
.products-bg { background-color: #ffd1dc; }
.orders-bg { background-color: #20c997; }
.revenue-bg { background-color: #1976d2; }

/* --- Recent Activity Sections --- */
.recent-activity {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.activity-panel {
    flex: 1;
    min-width: 45%;
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.activity-panel h3 {
    color: #e91e63;
    border-bottom: 2px solid #fff0f5;
    padding-bottom: 10px;
    margin-top: 0;
}

.activity-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.activity-table th, .activity-table td {
    padding: 10px 0;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.activity-table th {
    color: #555;
    font-weight: 600;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Màu sắc cho Status */
.status-pending { background: #fff3cd; color: #ffc107; }
.status-shipping { background: #e3f2fd; color: #1976d2; }
.status-completed { background: #d4edda; color: #28a745; }
.status-canceled { background: #f8d7da; color: #dc3545; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Dashboard - Tổng quan Hệ thống</h1>

    <div class="metric-cards">
        
        <div class="card-box">
            <div class="card-info">
                <h3>Tổng số Người dùng</h3>
                <h1>1,250</h1>
            </div>
            <div class="card-icon users-bg">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="card-box">
            <div class="card-info">
                <h3>Tổng số Sản phẩm</h3>
                <h1>345</h1>
            </div>
            <div class="card-icon products-bg">
                <i class="fa-solid fa-bottle-droplet"></i>
            </div>
        </div>

        <div class="card-box">
            <div class="card-info">
                <h3>Đơn hàng Hoàn thành</h3>
                <h1>6,789</h1>
            </div>
            <div class="card-icon orders-bg">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        </div>

        <div class="card-box">
            <div class="card-info">
                <h3>Tổng Doanh thu</h3>
                <h1>1,500,000,000 VNĐ</h1>
            </div>
            <div class="card-icon revenue-bg">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
    </div>

    <div class="recent-activity">
        
        <div class="activity-panel">
            <h3>Đơn hàng Gần đây</h3>
            <div style="overflow-x: auto;">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="30%">Tổng tiền</th>
                            <th width="30%">Trạng thái</th>
                            <th width="30%">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#901</td>
                            <td>350,000 VNĐ</td>
                            <td><span class="status-badge status-pending">Chờ xác nhận</span></td>
                            <td>25/11/2025</td>
                        </tr>
                        <tr>
                            <td>#900</td>
                            <td>1,200,000 VNĐ</td>
                            <td><span class="status-badge status-shipping">Đang giao hàng</span></td>
                            <td>24/11/2025</td>
                        </tr>
                        <tr>
                            <td>#899</td>
                            <td>780,000 VNĐ</td>
                            <td><span class="status-badge status-completed">Hoàn thành</span></td>
                            <td>23/11/2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="activity-panel">
            <h3>Người dùng Mới nhất</h3>
            <div style="overflow-x: auto;">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="40%">Tên đăng nhập</th>
                            <th width="50%">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1250</td>
                            <td>NgocAnh</td>
                            <td>ngocanh@gmail.com</td>
                        </tr>
                        <tr>
                            <td>#1249</td>
                            <td>TuanKiet</td>
                            <td>kiet.t@yahoo.com</td>
                        </tr>
                        <tr>
                            <td>#1248</td>
                            <td>HaPhuong</td>
                            <td>phuongha@example.com</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>