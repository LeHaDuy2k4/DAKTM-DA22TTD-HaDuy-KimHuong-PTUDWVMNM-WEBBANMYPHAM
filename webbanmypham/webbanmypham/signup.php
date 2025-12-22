<?php

  require_once("config.php");
?>

<script language='javascript'>
function kiemtra(){
	var pass1 = document.f1.txtMatkhau.value;
	var pass2 = document.f1.txtreMatkhau.value;
	if(pass1 != pass2){
		alert("Mật khẩu không trung khớp");
		return false;		
	}
}
</script>

<?php
    if(isset($_SESSION['emailNguoidung'])){
        echo "<script> alert('Bạn đã đăng nhập');";
        echo "window.location.assign('index.php');";
        echo "</script>";
    }

    if(isset($_REQUEST['sbDangky'])){
        $tenDangnhap = $_REQUEST['txtTendangnhap'];
        $matKhau = md5($_REQUEST['txtMatkhau']); // mã hóa mật khẩu
        $hoTen = $_REQUEST['txtTendaydu'];
        $gioiTinh = intval($_REQUEST['rdGt']);
        $email = $_REQUEST['txtEmail'];
        $dienThoai = isset($_REQUEST['txtDienThoai']) ? $_REQUEST['txtDienThoai'] : '';
        $diaChi = isset($_REQUEST['txtDiaChi']) ? $_REQUEST['txtDiaChi'] : '';
        $quyen = 0;      // mặc định quyền user
        $trangThai = 0;  // mặc định trạng thái

        // Kiểm tra tồn tại username hoặc email
        $sqlcheck = "SELECT * FROM nguoidung WHERE tenDangnhap='$tenDangnhap' OR email='$email'";
        $result = $conn->query($sqlcheck);
        if($result->num_rows > 0){
            echo "<script>alert('Tên đăng nhập hoặc Email đã tồn tại');</script>";
        } else {
            // Insert dữ liệu
            $sql = "INSERT INTO nguoidung (tenDangnhap, matKhau, hoTen, gioiTinh, email, quyen, trangThai, dienThoai, diaChi) VALUES (
                '$tenDangnhap', 
                '$matKhau', 
                '$hoTen', 
                $gioiTinh, 
                '$email', 
                $quyen, 
                $trangThai, 
                '$dienThoai', 
                '$diaChi'
            )";

            if($conn->query($sql)){
                echo "<script>alert('Đăng ký thành công!'); window.location.assign('login.php');</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra: " . $conn->error . "');</script>";
            }
        }
    }
?>

<style>
  body {
    background: #f4f4f4;
    font-family: Arial, sans-serif;
    margin: 0; padding: 0;
  }

  .register-wrap {
    max-width: 420px;
    margin: 60px auto;
    background: #fff;
    padding: 28px 26px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }

  .register-wrap h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #d63384;
    font-weight: 600;
  }

  .form-group {
    margin-bottom: 18px;
  }

  label {
    font-size: 14px;
    display: block;
    margin-bottom: 6px;
    color: #444;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="tel"] {
    width: 100%;
    padding: 11px;
    border: 1px solid #d9d9d9;
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box;
  }

  .gender-box {
    display: flex;
    gap: 15px;
    margin-bottom: 18px;
  }

  .gender-box label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
  }

  .btn-default {
    width: 100%;
    padding: 12px;
    background: #d63384;
    color: white;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: opacity 0.3s;
  }

  .btn-default:hover {
    opacity: 0.9;
  }
</style>

<div class="register-wrap">
  <h3>Đăng ký</h3>

  <form action="" method="post" name="f1" onsubmit="return kiemtra();" enctype="multipart/form-data">

    <div class="form-group">
      <label>Tên đăng nhập:</label>
      <input type="text" name="txtTendangnhap" placeholder="Nhập username..." required 
             value="<?php echo @$_REQUEST['txtTendangnhap']; ?>">
    </div>

    <div class="form-group">
      <label>Mật khẩu:</label>
      <input type="password" name="txtMatkhau" required>
    </div>

    <div class="form-group">
      <label>Nhắc lại mật khẩu:</label>
      <input type="password" name="txtreMatkhau" required>
    </div>

    <div class="form-group">
      <label>Tên đầy đủ:</label>
      <input type="text" name="txtTendaydu" 
             value="<?php echo @$_REQUEST['txtTendaydu']; ?>">
    </div>

    <div class="form-group">
      <label>Giới tính:</label>
      <div class="gender-box">
        <label><input type="radio" name="rdGt" value="0" <?php if(!isset($_REQUEST['rdGt']) || $_REQUEST['rdGt']==0) echo 'checked'; ?>> Nam</label>
        <label><input type="radio" name="rdGt" value="1" <?php if(@$_REQUEST['rdGt']==1) echo 'checked'; ?>> Nữ</label>
      </div>
    </div>

    <div class="form-group">
      <label>Email:</label>
      <input type="email" name="txtEmail" required 
             value="<?php echo @$_REQUEST['txtEmail']; ?>">
    </div>

    <div class="form-group">
      <label>Điện thoại:</label>
      <input type="tel" name="txtDienThoai" placeholder="Nhập số điện thoại"
             value="<?php echo @$_REQUEST['txtDienThoai']; ?>">
    </div>

    <div class="form-group">
      <label>Địa chỉ:</label>
      <input type="text" name="txtDiaChi" placeholder="Nhập địa chỉ"
             value="<?php echo @$_REQUEST['txtDiaChi']; ?>">
    </div>

    <button type="submit" class="btn-default" name="sbDangky">Đăng ký</button>
  </form>
</div>
