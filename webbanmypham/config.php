<?php
$servername = "localhost";  // thường là localhost
$username = "root";         // user mysql
$password = "";             // pass mysql (mặc định xampp là rỗng)
$dbname = "web_mypham";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}


define('GOOLE_CLIENT_ID','');
define('GOOLE_CLIENT_SECRET','');
define('GOOLE_CLIENT_URI','http://localhost/webbanmypham/google-lcallback.php');
// Thiết lập charset nếu cần
$conn->set_charset("utf8mb4");
?>
