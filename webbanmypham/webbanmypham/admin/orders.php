<?php        
    require("sidebar.php");         
?>

<style>
/* --- GIỮ NGUYÊN CSS KHUNG SƯỜN TỪ DASHBOARD --- */
.main-content {
    margin-left: 250px; 
    padding: 25px;
    background-color: #fff8fb; 
    min-height: 100vh;
}

.dashboard-title {
    color: #e91e63;
    margin-bottom: 25px;
    font-weight: 700;
}

/* --- TOOLBAR PANEL --- */
.toolbar-panel {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.search-group {
    display: flex;
    align-items: center;
    border: 1px solid #ffd6e5;
    border-radius: 30px; 
    padding: 5px 5px 5px 15px;
    width: 100%;
    max-width: 400px;
    transition: 0.3s;
    background: #fff;
}

.search-group:focus-within {
    border-color: #e91e63;
    box-shadow: 0 0 5px rgba(233, 30, 99, 0.2);
}

.search-group input {
    border: none;
    outline: none;
    flex: 1;
    color: #555;
    font-size: 0.95rem;
}

.btn-search {
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-search:hover { background: #c2185b; }

/* Nút Export thay vì Add New */
.btn-export {
    background: #fff;
    color: #e91e63;
    border: 1px solid #e91e63;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-export:hover { background: #ffeef4; }

/* --- TABLE PANEL --- */
.table-panel {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.order-table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0 15px; 
    margin-top: -15px;
}

.order-table th {
    color: #e91e63;
    font-weight: 700;
    padding: 10px 15px;
    text-align: left;
    border-bottom: 2px solid #fff0f5;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.order-table tbody tr {
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    transition: 0.2s;
}

.order-table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08);
}

.order-table td {
    padding: 15px;
    vertical-align: middle;
    color: #555;
    border-top: 1px solid #fcfcfc;
    border-bottom: 1px solid #fcfcfc;
}

.order-table td:first-child { border-left: 1px solid #fcfcfc; border-radius: 10px 0 0 10px; }
.order-table td:last-child { border-right: 1px solid #fcfcfc; border-radius: 0 10px 10px 0; }

/* --- CUSTOM ELEMENTS CHO ĐƠN HÀNG --- */
.order-id {
    font-family: 'Consolas', monospace;
    color: #333;
    font-weight: 700;
    background: #eee;
    padding: 4px 8px;
    border-radius: 4px;
}

.customer-info h4 { margin: 0; font-size: 1rem; color: #333; font-weight: 600; }
.customer-info span { font-size: 0.85rem; color: #888; }

.total-price {
    color: #e91e63;
    font-weight: 700;
    font-size: 1rem;
}

/* Badge Trạng thái Đơn hàng */
.status-badge {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
}

/* Các màu trạng thái */
.st-pending { background: #fff3cd; color: #856404; }   /* Chờ xử lý - Vàng */
.st-shipping { background: #e3f2fd; color: #0d47a1; }  /* Đang giao - Xanh dương */
.st-done { background: #d4edda; color: #155724; }      /* Hoàn thành - Xanh lá */
.st-cancel { background: #f8d7da; color: #721c24; }    /* Đã hủy - Đỏ */

/* Nút thao tác */
.action-btn {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    margin-right: 5px;
    transition: 0.2s;
}

.btn-view { background: #e3f2fd; color: #1976d2; }
.btn-view:hover { background: #1976d2; color: white; }

.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #c62828; color: white; }

</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Đơn Hàng</h1>

    <!-- 1. TOOLBAR -->
    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm mã đơn, tên khách hàng..." value="">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <!-- Thay nút "Thêm mới" bằng "Xuất Excel" vì đơn hàng thường không tạo thủ công -->
        <a href="#" class="btn-export">
            <i class="fa-solid fa-file-export"></i> Xuất Excel
        </a>
    </div>

    <!-- 2. DANH SÁCH ĐƠN HÀNG -->
    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="order-table">
                <thead>
                    <tr>
                        <th width="10%">Mã Đơn</th>
                        <th width="25%">Khách hàng</th>
                        <th width="15%">Tổng tiền</th>
                        <th width="15%">Ngày đặt</th>
                        <th width="15%">Trạng thái</th>
                        <th width="10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Đơn hàng 1: Mới đặt -->
                    <tr>
                        <td><span class="order-id">#ORD-901</span></td>
                        <td class="customer-info">
                            <h4>Nguyễn Văn A</h4>
                            <span>0901.234.567</span>
                        </td>
                        <td class="total-price">350.000 đ</td>
                        <td>25/11/2025<br><small style="color:#999">10:30 AM</small></td>
                        <td><span class="status-badge st-pending">Chờ xử lý</span></td>
                        <td>
                            <a href="order_detail.php?id=901" class="action-btn btn-view" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                            <a href="#" class="action-btn btn-delete" title="Hủy đơn"><i class="fa-solid fa-xmark"></i></a>
                        </td>
                    </tr>

                    <!-- Đơn hàng 2: Đang giao -->
                    <tr>
                        <td><span class="order-id">#ORD-899</span></td>
                        <td class="customer-info">
                            <h4>Trần Thị B</h4>
                            <span>b.tran@gmail.com</span>
                        </td>
                        <td class="total-price">1.250.000 đ</td>
                        <td>24/11/2025<br><small style="color:#999">14:15 PM</small></td>
                        <td><span class="status-badge st-shipping">Đang giao hàng</span></td>
                        <td>
                            <a href="order_detail.php?id=899" class="action-btn btn-view" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>

                    <!-- Đơn hàng 3: Hoàn thành -->
                    <tr>
                        <td><span class="order-id">#ORD-880</span></td>
                        <td class="customer-info">
                            <h4>Lê Văn C</h4>
                            <span>0988.777.666</span>
                        </td>
                        <td class="total-price">890.000 đ</td>
                        <td>20/11/2025<br><small style="color:#999">09:00 AM</small></td>
                        <td><span class="status-badge st-done">Hoàn thành</span></td>
                        <td>
                            <a href="order_detail.php?id=880" class="action-btn btn-view" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>

                    <!-- Đơn hàng 4: Đã hủy -->
                    <tr>
                        <td><span class="order-id">#ORD-875</span></td>
                        <td class="customer-info">
                            <h4>Phạm Thị D</h4>
                            <span>dpham@yahoo.com</span>
                        </td>
                        <td class="total-price">120.000 đ</td>
                        <td>19/11/2025<br><small style="color:#999">16:45 PM</small></td>
                        <td><span class="status-badge st-cancel">Đã hủy</span></td>
                        <td>
                            <a href="order_detail.php?id=875" class="action-btn btn-view" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                            <a href="#" class="action-btn btn-delete" onclick="return confirm('Xóa lịch sử đơn này?')" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        
        <!-- Phân trang -->
        
    </div>
</div>