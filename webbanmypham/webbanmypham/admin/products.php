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

/* --- 1. TOOLBAR PANEL (Tận dụng style của card-box nhưng bỏ flex space-between) --- */
.toolbar-panel {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between; /* Đẩy 2 bên */
    gap: 15px;
}

.search-group {
    display: flex;
    align-items: center;
    border: 1px solid #ffd6e5;
    border-radius: 30px; /* Bo tròn mềm mại */
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

.btn-add-new {
    background: #20c997; /* Màu xanh ngọc giống card Orders */
    color: white;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(32, 201, 151, 0.3);
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-add-new:hover { background: #17a57a; transform: translateY(-2px); }

/* --- 2. TABLE PANEL (Tận dụng style của activity-panel) --- */
.table-panel {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.product-table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0 15px; /* Tạo khoảng cách giữa các dòng */
    margin-top: -15px;
}

.product-table th {
    color: #e91e63;
    font-weight: 700;
    padding: 10px 15px;
    text-align: left;
    border-bottom: 2px solid #fff0f5;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.product-table tbody tr {
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02); /* Đổ bóng nhẹ cho từng dòng */
    transition: 0.2s;
}

.product-table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08);
}

.product-table td {
    padding: 15px;
    vertical-align: middle;
    color: #555;
    border-top: 1px solid #fcfcfc;
    border-bottom: 1px solid #fcfcfc;
}

/* Bo tròn 2 đầu của dòng */
.product-table td:first-child { border-left: 1px solid #fcfcfc; border-radius: 10px 0 0 10px; }
.product-table td:last-child { border-right: 1px solid #fcfcfc; border-radius: 0 10px 10px 0; }

/* --- CUSTOM ELEMENTS CHO SẢN PHẨM --- */
.sku-code {
    font-family: 'Consolas', monospace;
    background: #fff0f5;
    color: #d81b60;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px dashed #e91e63;
}

.product-thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #eee;
}

.product-info h4 { margin: 0; font-size: 1rem; color: #333; }
.product-info span { font-size: 0.85rem; color: #888; }

.price-tag {
    color: #e91e63;
    font-weight: 700;
    font-size: 1rem;
}

/* Badge trạng thái kho (Tái sử dụng class status-badge) */
.stock-in { background: #d4edda; color: #155724; } /* Xanh */
.stock-out { background: #f8d7da; color: #721c24; } /* Đỏ */
.stock-low { background: #fff3cd; color: #856404; } /* Vàng */

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

.btn-edit { background: #e3f2fd; color: #1976d2; }
.btn-edit:hover { background: #1976d2; color: white; }

.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #c62828; color: white; }

</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Mặt Hàng</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm theo Mã hàng, Tên sản phẩm..." value="">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <a href="product_add.php" class="btn-add-new">
            <i class="fa-solid fa-plus"></i> Thêm Mặt Hàng
        </a>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="product-table">
                <thead>
                    <tr>
                        <th width="15%">Mã Hàng</th>
                        <th width="10%">Hình ảnh</th>
                        <th width="30%">Tên & Danh mục</th>
                        <th width="15%">Giá bán</th>
                        <th width="15%">Tồn kho</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="sku-code">SP001</span></td>
                        <td>
                            <img src="https://via.placeholder.com/150/FFD1DC/e91e63?text=Son" alt="Son" class="product-thumb">
                        </td>
                        <td class="product-info">
                            <h4>Son Black Rouge A12</h4>
                            <span>Danh mục: Son môi</span>
                        </td>
                        <td class="price-tag">180.000 đ</td>
                        <td><span class="status-badge stock-in">Còn hàng (50)</span></td>
                        <td>
                            <a href="#" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="action-btn btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="sku-code">MH-02</span></td>
                        <td>
                            <img src="https://via.placeholder.com/150/E1F5FE/0288d1?text=Kem" alt="Kem" class="product-thumb">
                        </td>
                        <td class="product-info">
                            <h4>Kem Dưỡng Vitamin C</h4>
                            <span>Danh mục: Chăm sóc da</span>
                        </td>
                        <td class="price-tag">350.000 đ</td>
                        <td><span class="status-badge stock-low">Sắp hết (3)</span></td>
                        <td>
                            <a href="#" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="action-btn btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>

                    <tr>
                        <td><span class="sku-code">MH-03</span></td>
                        <td>
                            <img src="https://via.placeholder.com/150/FFF3E0/ef6c00?text=Phan" alt="Phấn" class="product-thumb">
                        </td>
                        <td class="product-info">
                            <h4>Phấn Phủ Kiềm Dầu</h4>
                            <span>Danh mục: Trang điểm</span>
                        </td>
                        <td class="price-tag">120.000 đ</td>
                        <td><span class="status-badge stock-out">Hết hàng</span></td>
                        <td>
                            <a href="#" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="action-btn btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        
    </div>
</div>