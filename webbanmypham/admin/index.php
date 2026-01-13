<?php
    require("sidebar.php");
    require("../config.php"); 

    // 1. LẤY THỐNG KÊ CHO CÁC THẺ METRIC
    $sql_users = "SELECT COUNT(*) as total FROM nguoidung";
    $res_users = $conn->query($sql_users);
    $total_users = $res_users->fetch_assoc()['total'];

    $sql_products = "SELECT COUNT(*) as total FROM mathang";
    $res_products = $conn->query($sql_products);
    $total_products = $res_products->fetch_assoc()['total'];

    $sql_orders_done = "SELECT COUNT(*) as total FROM dondathang WHERE trangthai = 'Đã hoàn thành'";
    $res_orders_done = $conn->query($sql_orders_done);
    $total_orders_done = $res_orders_done->fetch_assoc()['total'];

    $sql_revenue = "SELECT SUM(tongTien) as total FROM dondathang WHERE trangthai = 'Đã hoàn thành'";
    $res_revenue = $conn->query($sql_revenue);
    $total_revenue = $res_revenue->fetch_assoc()['total'] ?? 0;

    // 2. LẤY DANH SÁCH ĐƠN HÀNG MỚI NHẤT (Top 5)
    $sql_recent_orders = "SELECT * FROM dondathang ORDER BY ngayDat DESC LIMIT 5";
    $result_orders = $conn->query($sql_recent_orders);

    // 3. TRUY VẤN DỮ LIỆU BIỂU ĐỒ DOANH THU (6 tháng gần đây)
    $chart_labels = [];
    $chart_data = [];
    $sql_chart = "SELECT DATE_FORMAT(ngayDat, '%m/%Y') as month, SUM(tongTien) as subtotal 
                  FROM dondathang 
                  WHERE trangthai = 'Đã hoàn thành' 
                  GROUP BY month 
                  ORDER BY ngayDat ASC LIMIT 6";
    $res_chart = $conn->query($sql_chart);
    while($row_c = $res_chart->fetch_assoc()){
        $chart_labels[] = $row_c['month'];
        $chart_data[] = $row_c['subtotal'];
    }
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.main-content { margin-left: 250px; padding: 25px; background-color: #fff8fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.dashboard-title { color: #e91e63; margin-bottom: 25px; font-weight: 700; }
.metric-cards { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
.card-box { flex: 1; min-width: 250px; background: #ffffff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
.card-info h3 { margin: 0 0 5px 0; font-size: 1rem; color: #888; font-weight: 500; }
.card-info h1 { margin: 0; font-size: 1.8rem; color: #444; font-weight: 700; }
.card-icon { font-size: 2rem; padding: 15px; border-radius: 50%; color: white; }
.users-bg { background-color: #ffb7b7; }
.products-bg { background-color: #ffd1dc; }
.orders-bg { background-color: #20c997; }
.revenue-bg { background-color: #1976d2; }

.recent-activity { display: flex; gap: 20px; flex-wrap: wrap; }
.activity-panel { flex: 1; min-width: 45%; background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.activity-panel h3 { color: #e91e63; border-bottom: 2px solid #fff0f5; padding-bottom: 10px; margin-bottom: 15px; }
.activity-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.activity-table th, .activity-table td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #f5f5f5; }
.status-badge { padding: 4px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; }

.status-pending { background: #fff3cd; color: #856404; }
.status-shipping { background: #e3f2fd; color: #0c5460; }
.status-completed { background: #d4edda; color: #155724; }
.status-canceled { background: #f8d7da; color: #721c24; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Dashboard - Tổng quan Hệ thống</h1>

    <div class="metric-cards">
        <div class="card-box">
            <div class="card-info"><h3>Người dùng</h3><h1><?php echo number_format($total_users); ?></h1></div>
            <div class="card-icon users-bg"><i class="fa-solid fa-users"></i></div>
        </div>
        <div class="card-box">
            <div class="card-info"><h3>Mặt hàng</h3><h1><?php echo number_format($total_products); ?></h1></div>
            <div class="card-icon products-bg"><i class="fa-solid fa-bottle-droplet"></i></div>
        </div>
        <div class="card-box">
            <div class="card-info"><h3>Đơn hoàn thành</h3><h1><?php echo number_format($total_orders_done); ?></h1></div>
            <div class="card-icon orders-bg"><i class="fa-solid fa-bag-shopping"></i></div>
        </div>
        <div class="card-box">
            <div class="card-info"><h3>Doanh thu</h3><h1 style="font-size: 1.4rem;"><?php echo number_format($total_revenue, 0, ',', '.'); ?> ₫</h1></div>
            <div class="card-icon revenue-bg"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>
    </div>

    <div class="recent-activity">
        <div class="activity-panel">
            <h3>Đơn hàng mới nhất</h3>
            <div style="overflow-x: auto;">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_orders->fetch_assoc()): 
                            $status = $row['trangthai'];
                            $status_class = ($status == 'Chờ xác nhận') ? 'status-pending' : 
                                            (($status == 'Đã hoàn thành') ? 'status-completed' : 'status-canceled');
                        ?>
                        <tr>
                            <td>#<?php echo $row['maDonhang']; ?></td>
                            <td><?php echo number_format($row['tongTien'], 0, ',', '.'); ?> ₫</td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngayDat'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="activity-panel">
            <h3>Thống kê doanh thu gần đây</h3>
            <canvas id="revenueChart" style="width: 100%; height: 250px;"></canvas>
        </div>
    </div>
</div>

<script>
    // Nhận dữ liệu từ PHP sang JavaScript
    const labels = <?php echo json_encode($chart_labels); ?>;
    const data = <?php echo json_encode($chart_data); ?>;

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line', // Kiểu biểu đồ đường (line) hoặc cột (bar)
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (₫)',
                data: data,
                borderColor: '#e91e63',
                backgroundColor: 'rgba(233, 30, 99, 0.1)',
                borderWidth: 3,
                tension: 0.3, // Độ cong của đường
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' ₫';
                        }
                    }
                }
            }
        }
    });
</script>

<?php $conn->close(); ?>