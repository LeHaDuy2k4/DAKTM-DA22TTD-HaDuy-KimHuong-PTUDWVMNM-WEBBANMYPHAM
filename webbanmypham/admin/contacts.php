<?php
    // Tên file: contacts.php

    // 1. Yêu cầu file sidebar (chứa session_start) và config
    require("sidebar.php"); 
    require("../config.php"); 

    // KHAI BÁO BIẾN KIỂM TRA QUYỀN ADMIN (nếu cần)
    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    // ====================================================================
    // 2. XỬ LÝ HÀNH ĐỘNG (XÓA / ĐỔI TRẠNG THÁI)
    // ====================================================================
    if (isset($_GET['action']) && isset($_GET['id']) && isset($conn)) {
        $id = intval($_GET['id']);
        
        // Xử lý Xóa
        if ($_GET['action'] == 'delete') {
            $sql = "DELETE FROM lienhe WHERE maLH = $id";
            $conn->query($sql);
            echo "<script>window.location.href='contacts.php';</script>";
            exit();
        }
        
        // Xử lý Đổi trạng thái (0: Mới -> 1: Đã xử lý)
        if ($_GET['action'] == 'toggle') {
            $check = $conn->query("SELECT trangThai FROM lienhe WHERE maLH = $id")->fetch_assoc();
            $newStatus = ($check['trangThai'] == 0) ? 1 : 0;
            
            $sql = "UPDATE lienhe SET trangThai = $newStatus WHERE maLH = $id";
            $conn->query($sql);
            echo "<script>window.location.href='contacts.php';</script>";
            exit();
        }
    }

    // ====================================================================
    // 3. TRUY VẤN DỮ LIỆU
    // ====================================================================
    $contacts = [];
    
    if (isset($conn) && $conn->connect_error === null) {
        // Lấy danh sách liên hệ, mới nhất lên đầu
        $sql = "SELECT * FROM lienhe ORDER BY trangThai ASC, ngayGui DESC"; 
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $contacts[] = $row;
            }
        }
    }
?>

<style>
/* --- CSS ĐỒNG BỘ VỚI USER PAGE --- */
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
}

.search-group {
    display: flex;
    align-items: center;
    border: 1px solid #ffd6e5;
    border-radius: 30px; 
    padding: 5px 5px 5px 15px;
    width: 100%;
    max-width: 400px;
    background: #fff;
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
    display: flex;
    align-items: center;
    justify-content: center;
}

/* --- TABLE STYLES --- */
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

/* --- CUSTOM ELEMENTS CHO LIÊN HỆ --- */
.id-badge {
    font-family: 'Consolas', monospace;
    background: #fff0f5;
    color: #d81b60;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px dashed #e91e63;
}

.user-info { display: flex; align-items: center; gap: 15px; }
.user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #fff0f5; }
.user-text h4 { margin: 0; font-size: 1rem; color: #333; font-weight: 600; }
.user-text span { font-size: 0.85rem; color: #888; display: flex; align-items: center; gap: 5px; }

/* Trạng thái */
.status-new { color: #c62828; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;}
.status-new::before { content:''; width: 8px; height: 8px; background: #c62828; border-radius: 50%; display: inline-block; }

.status-done { color: #2e7d32; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;}
.status-done::before { content:''; width: 8px; height: 8px; background: #2e7d32; border-radius: 50%; display: inline-block; }

/* Nút bấm hành động (Text styles) */
.action-btn {
    padding: 6px 12px; 
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    margin-right: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
}

.btn-view { background: #e3f2fd; color: #1565c0; border-color: #bbdefb; }
.btn-view:hover { background: #1565c0; color: white; transform: translateY(-1px); }

.btn-toggle { background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9; }
.btn-toggle:hover { background: #2e7d32; color: white; transform: translateY(-1px); }

.btn-delete { background: #ffeded; color: #dc3545; border-color: #ffcccc; }
.btn-delete:hover { background: #dc3545; color: white; transform: translateY(-1px); }

/* --- MODAL (POPUP) STYLES --- */
.modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
.modal-content { background: white; padding: 30px; border-radius: 12px; width: 500px; max-width: 90%; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.1); animation: slideIn 0.3s ease; }
@keyframes slideIn { from {transform: translateY(-20px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
.close-btn { position: absolute; top: 15px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #999; }
.modal-row { margin-bottom: 15px; }
.modal-label { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
.modal-text { background: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #eee; color: #555; word-wrap: break-word; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Liên Hệ</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm tên, email người gửi..." value="">
                <button class="btn-search" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="10%">Mã LH</th>
                        <th width="35%">Người gửi</th>
                        <th width="15%">Thành viên</th>
                        <th width="15%">Ngày gửi</th>
                        <th width="10%">Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($contacts)) {
                            foreach ($contacts as $row) {
                                // Xử lý dữ liệu hiển thị
                                $avatarName = urlencode($row['hoTen']);
                                $memberStatus = !empty($row['tenDangNhap']) ? '<span style="color:#e91e63; font-weight:600">'.$row['tenDangNhap'].'</span>' : '<span style="color:#999">Vãng lai</span>';
                                
                                $statusClass = $row['trangThai'] == 1 ? 'status-done' : 'status-new';
                                $statusText = $row['trangThai'] == 1 ? 'Đã xử lý' : 'Mới';
                                $toggleText = $row['trangThai'] == 1 ? 'Mở lại' : 'Duyệt';

                                echo '<tr>';
                                // Cột Mã
                                echo '<td><span class="id-badge">#' . $row['maLH'] . '</span></td>';

                                // Cột Thông tin người gửi (Avatar + Tên + Email)
                                echo '<td>
                                        <div class="user-info">
                                            <img src="https://ui-avatars.com/api/?name=' . $avatarName . '&background=random&color=fff&size=64" class="user-avatar" alt="Ava">
                                            <div class="user-text">
                                                <h4>' . htmlspecialchars($row['hoTen']) . '</h4>
                                                <span><i class="fa-regular fa-envelope"></i> ' . htmlspecialchars($row['email']) . '</span>
                                            </div>
                                        </div>
                                      </td>';

                                // Cột Thành viên
                                echo '<td>' . $memberStatus . '</td>';

                                // Cột Ngày gửi
                                echo '<td style="color: #666; font-size: 0.9rem">' . date('d/m/Y', strtotime($row['ngayGui'])) . '</td>';

                                // Cột Trạng thái
                                echo '<td><span class="' . $statusClass . '">' . $statusText . '</span></td>';

                                // Cột Hành động
                                echo '<td>';
                                // Nút Xem (Mở Modal)
                                echo '<a href="javascript:void(0)" onclick=\'showModal('.json_encode($row).')\' class="action-btn btn-view">Xem</a>';
                                
                                // Nút Duyệt/Đổi trạng thái
                                echo '<a href="contacts.php?action=toggle&id=' . $row['maLH'] . '" class="action-btn btn-toggle">' . $toggleText . '</a>';

                                // Nút Xóa
                                echo '<a href="contacts.php?action=delete&id=' . $row['maLH'] . '" class="action-btn btn-delete" onclick="return confirm(\'Bạn có chắc muốn xóa liên hệ này?\')">Xóa</a>';
                                echo '</td>';

                                echo '</tr>';
                            }
                        } else {
                             echo '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Chưa có liên hệ nào từ khách hàng.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 style="color: #e91e63; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top:0;">Nội Dung Liên Hệ</h3>
        
        <div class="modal-row">
            <span class="modal-label">Người gửi:</span>
            <div class="modal-text" id="mName"></div>
        </div>
        
        <div class="modal-row" style="display: flex; gap: 10px;">
            <div style="flex:1;">
                <span class="modal-label">Email:</span>
                <div class="modal-text" id="mEmail"></div>
            </div>
            <div style="flex:1;">
                <span class="modal-label">SĐT:</span>
                <div class="modal-text" id="mPhone"></div>
            </div>
        </div>

        <div class="modal-row">
            <span class="modal-label">Nội dung tin nhắn:</span>
            <div class="modal-text" id="mContent" style="min-height: 80px; background: #fffde7;"></div>
        </div>

        <div class="modal-row" style="text-align: right; margin-top: 20px;">
            <a href="#" id="mMailTo" class="action-btn btn-view" style="padding: 10px 20px; font-size: 1rem;">
                <i class="fa-solid fa-reply"></i> Trả lời qua Email
            </a>
        </div>
    </div>
</div>

<script>
    function showModal(data) {
        document.getElementById('mName').innerText = data.hoTen;
        document.getElementById('mEmail').innerText = data.email;
        document.getElementById('mPhone').innerText = data.dienThoai;
        document.getElementById('mContent').innerText = data.noiDung;
        document.getElementById('mMailTo').href = "mailto:" + data.email;
        document.getElementById('viewModal').style.display = "flex";
    }

    function closeModal() {
        document.getElementById('viewModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('viewModal')) {
            closeModal();
        }
    }
</script>