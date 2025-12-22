<?php        
    require("sidebar.php");         
?>

<style>
/* --- GIỮ NGUYÊN CSS KHUNG SƯỜN --- */
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

.btn-add-new {
    background: #20c997; 
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

/* --- TABLE --- */
.table-panel {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.custom-table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0 15px; 
    margin-top: -15px;
}

.custom-table th {
    color: #e91e63;
    font-weight: 700;
    padding: 10px 15px;
    text-align: left;
    border-bottom: 2px solid #fff0f5;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.custom-table tbody tr {
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    transition: 0.2s;
}

.custom-table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08);
}

.custom-table td {
    padding: 15px;
    vertical-align: middle;
    color: #555;
    border-top: 1px solid #fcfcfc;
    border-bottom: 1px solid #fcfcfc;
}

.custom-table td:first-child { border-left: 1px solid #fcfcfc; border-radius: 10px 0 0 10px; }
.custom-table td:last-child { border-right: 1px solid #fcfcfc; border-radius: 0 10px 10px 0; }

/* --- USER ELEMENTS --- */

/* Style cho Tên đăng nhập (giống mã SKU) */
.username-badge {
    font-family: 'Consolas', monospace;
    background: #fff0f5;
    color: #d81b60;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px dashed #e91e63;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff0f5;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-text h4 { margin: 0; font-size: 1rem; color: #333; font-weight: 600; }
.user-text span { font-size: 0.85rem; color: #888; } /* Màu nhạt hơn cho title phụ */

.email-text {
    color: #555;
    font-weight: 500;
}

/* Badge Vai trò */
.badge-pill {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
}
.role-admin { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
.role-customer { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }

/* Trạng thái */
.status-active { color: #2e7d32; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;}
.status-active::before { content:''; width: 8px; height: 8px; background: #2e7d32; border-radius: 50%; display: inline-block; }

.status-locked { color: #c62828; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;}
.status-locked::before { content:''; width: 8px; height: 8px; background: #c62828; border-radius: 50%; display: inline-block; }

/* Buttons */
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

.btn-edit { background: #fff8e1; color: #fbc02d; }
.btn-edit:hover { background: #fbc02d; color: white; }

.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #c62828; color: white; }

</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Người Dùng</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm tên đăng nhập, email..." value="">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <a href="user_add.php" class="btn-add-new">
            <i class="fa-solid fa-user-plus"></i> Thêm Người Dùng
        </a>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="15%">Tên đăng nhập</th>
                        <th width="25%">Họ và Tên</th>
                        <th width="25%">Email</th>
                        <th width="15%">Vai trò</th>
                        <th width="10%">Trạng thái</th>
                        <th width="10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="username-badge">admin_master</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <img src="https://ui-avatars.com/api/?name=Admin+Master&background=e91e63&color=fff" class="user-avatar" alt="Ava">
                                <div class="user-text">
                                    <h4>Admin Master</h4>
                                </div>
                            </div>
                        </td>
                        <td class="email-text">admin@mypham.com</td>
                        <td><span class="badge-pill role-admin">Quản trị viên</span></td>
                        <td><span class="status-active">Hoạt động</span></td>
                        <td>
                            <a href="user_add.php?id=1" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="username-badge">nguyena_99</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <img src="https://ui-avatars.com/api/?name=Nguyen+Van+A&background=random" class="user-avatar" alt="Ava">
                                <div class="user-text">
                                    <h4>Nguyễn Văn A</h4>
                                </div>
                            </div>
                        </td>
                        <td class="email-text">nguyena@gmail.com</td>
                        <td><span class="badge-pill role-customer">Khách hàng</span></td>
                        <td><span class="status-active">Hoạt động</span></td>
                        <td>
                            <a href="user_add.php?id=25" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="action-btn btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span class="username-badge">baby_girl</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <img src="https://ui-avatars.com/api/?name=Tran+Thi+B&background=random" class="user-avatar" alt="Ava">
                                <div class="user-text">
                                    <h4>Trần Thị B</h4>
                                </div>
                            </div>
                        </td>
                        <td class="email-text">tranthib@yahoo.com</td>
                        <td><span class="badge-pill role-customer">Khách hàng</span></td>
                        <td><span class="status-locked">Đã khóa</span></td>
                        <td>
                            <a href="user_add.php?id=30" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="action-btn btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
      
    </div>
</div>