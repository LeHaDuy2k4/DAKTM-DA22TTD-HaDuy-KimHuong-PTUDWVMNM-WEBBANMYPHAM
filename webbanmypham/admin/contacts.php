<?php
    // Tên file: contacts.php
    require("sidebar.php"); 
    require("../config.php"); 

    $isAdmin = isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1;

    // --- XỬ LÝ HÀNH ĐỘNG ---
    if (isset($_GET['action']) && isset($_GET['id']) && isset($conn)) {
        $id = intval($_GET['id']);
        
        if ($_GET['action'] == 'delete') {
            $sql = "DELETE FROM lienhe WHERE maLH = $id";
            $conn->query($sql);
            echo "<script>window.location.href='contacts.php';</script>";
            exit();
        }
        
        if ($_GET['action'] == 'toggle') {
            $check = $conn->query("SELECT trangThai FROM lienhe WHERE maLH = $id")->fetch_assoc();
            $newStatus = ($check['trangThai'] == 0) ? 1 : 0;
            $sql = "UPDATE lienhe SET trangThai = $newStatus WHERE maLH = $id";
            $conn->query($sql);
            echo "<script>window.location.href='contacts.php';</script>";
            exit();
        }
    }

    // --- TRUY VẤN DỮ LIỆU ---
    $contacts = [];
    if (isset($conn) && $conn->connect_error === null) {
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
/* ĐỔI FONT CHỮ TOÀN BỘ TRANG THÀNH TIMES NEW ROMAN */
* {
    font-family: "Times New Roman", Times, serif;
}

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
    padding: 5px 15px; 
    width: 100%; 
    max-width: 400px; 
    background: #fff; 
}

.search-group input { 
    border: none; 
    outline: none; 
    flex: 1; 
    color: #555; 
    font-size: 1rem; /* Tăng nhẹ size chữ cho Times New Roman dễ đọc hơn */
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
    font-size: 0.95rem; 
}

.custom-table td { 
    padding: 15px; 
    vertical-align: middle; 
    color: #333; 
    background: white; 
    font-size: 1rem;
}

.custom-table tbody tr { transition: 0.2s; }
.custom-table tbody tr:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 5px 15px rgba(233, 30, 99, 0.08); 
}

/* Badge & Info */
.id-badge { 
    font-family: "Times New Roman", Times, serif !important; 
    background: #fff0f5; 
    color: #d81b60; 
    padding: 5px 10px; 
    border-radius: 6px; 
    border: 1px dashed #e91e63; 
    font-weight: bold;
}

.user-info { display: flex; align-items: center; gap: 12px; }
.user-avatar { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fff0f5; }
.user-text h4 { margin: 0; font-size: 1.05rem; color: #333; font-weight: 700; }
.user-text span { font-size: 0.9rem; color: #666; }

/* Trạng thái */
.status-new { color: #c62828; font-weight: 700; display: flex; align-items: center; gap: 5px;}
.status-new::before { content:''; width: 8px; height: 8px; background: #c62828; border-radius: 50%; }
.status-done { color: #2e7d32; font-weight: 700; display: flex; align-items: center; gap: 5px;}
.status-done::before { content:''; width: 8px; height: 8px; background: #2e7d32; border-radius: 50%; }

/* Nút nằm ngang */
.action-wrapper {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.action-btn {
    padding: 6px 14px; 
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 700;
    transition: 0.2s;
    border: 1px solid transparent;
}

.btn-view { background: #e3f2fd; color: #1565c0; border-color: #bbdefb; }
.btn-view:hover { background: #1565c0; color: white; }
.btn-toggle { background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9; }
.btn-toggle:hover { background: #2e7d32; color: white; }
.btn-delete { background: #ffeded; color: #dc3545; border-color: #ffcccc; }
.btn-delete:hover { background: #dc3545; color: white; }

/* Modal */
.modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
.modal-content { background: white; padding: 30px; border-radius: 12px; width: 500px; max-width: 90%; position: relative; animation: slideIn 0.3s ease; }
.close-btn { position: absolute; top: 10px; right: 15px; font-size: 1.8rem; cursor: pointer; color: #999; font-weight: bold; }
</style>

<div class="main-content">
    <h1 class="dashboard-title">Quản Lý Liên Hệ</h1>

    <div class="toolbar-panel">
        <form method="GET" action="" style="flex: 1;">
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm tên, email người gửi..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <div class="table-panel">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="8%">Mã LH</th>
                        <th width="30%">Người gửi</th>
                        <th width="15%">Thành viên</th>
                        <th width="12%">Ngày gửi</th>
                        <th width="10%">Trạng thái</th>
                        <th width="25%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($contacts)) {
                            foreach ($contacts as $row) {
                                $avatarName = urlencode($row['hoTen']);
                                $memberStatus = !empty($row['tenDangNhap']) ? '<span style="color:#e91e63; font-weight:700">'.$row['tenDangNhap'].'</span>' : '<span style="color:#999">Vãng lai</span>';
                                $statusClass = $row['trangThai'] == 1 ? 'status-done' : 'status-new';
                                $statusText = $row['trangThai'] == 1 ? 'Đã xử lý' : 'Mới';
                                $toggleText = $row['trangThai'] == 1 ? 'Mở lại' : 'Duyệt';

                                echo '<tr>';
                                echo '<td><span class="id-badge">#' . $row['maLH'] . '</span></td>';
                                echo '<td>
                                        <div class="user-info">
                                            <img src="https://ui-avatars.com/api/?name=' . $avatarName . '&background=random&color=fff" class="user-avatar">
                                            <div class="user-text">
                                                <h4>' . htmlspecialchars($row['hoTen']) . '</h4>
                                                <span>' . htmlspecialchars($row['email']) . '</span>
                                            </div>
                                        </div>
                                      </td>';
                                echo '<td>' . $memberStatus . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['ngayGui'])) . '</td>';
                                echo '<td><span class="' . $statusClass . '">' . $statusText . '</span></td>';
                                echo '<td>
                                        <div class="action-wrapper">
                                            <a href="javascript:void(0)" onclick=\'showModal('.json_encode($row).')\' class="action-btn btn-view">Xem</a>
                                            <a href="contacts.php?action=toggle&id=' . $row['maLH'] . '" class="action-btn btn-toggle">' . $toggleText . '</a>
                                            <a href="contacts.php?action=delete&id=' . $row['maLH'] . '" class="action-btn btn-delete" onclick="return confirm(\'Xóa liên hệ này?\')">Xóa</a>
                                        </div>
                                      </td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">Chưa có liên hệ nào.</td></tr>';
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
        <h3 style="color: #e91e63; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top:0; font-weight: 700;">Nội Dung Liên Hệ</h3>
        <div style="margin-top:15px; font-size: 1.05rem;">
            <p><strong>Người gửi:</strong> <span id="mName"></span></p>
            <p><strong>Email:</strong> <span id="mEmail"></span></p>
            <p><strong>Nội dung:</strong></p>
            <div id="mContent" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #eee; min-height:100px; white-space: pre-wrap;"></div>
        </div>
        <div style="text-align:right; margin-top:20px;">
            <a href="#" id="mMailTo" class="action-btn btn-view" style="padding:10px 20px;">Trả lời Email</a>
        </div>
    </div>
</div>

<script>
    function showModal(data) {
        document.getElementById('mName').innerText = data.hoTen;
        document.getElementById('mEmail').innerText = data.email;
        document.getElementById('mContent').innerText = data.noiDung;
        document.getElementById('mMailTo').href = "mailto:" + data.email;
        document.getElementById('viewModal').style.display = "flex";
    }
    function closeModal() { document.getElementById('viewModal').style.display = "none"; }
    window.onclick = function(e) { if (e.target == document.getElementById('viewModal')) closeModal(); }
</script>