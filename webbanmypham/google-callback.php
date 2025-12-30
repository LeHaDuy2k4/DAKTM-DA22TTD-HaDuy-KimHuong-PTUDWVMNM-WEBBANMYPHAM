<?php
session_start();
require 'vendor/autoload.php';
require 'config.php';

$client = new Google_Client();
$client->setClientId("672995869836-j6fftq3lnioi879o3f4ut2pqa0su4e0m.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/webbanmypham/google-callback.php");

if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $gUser = $oauth->userinfo->get();

    $email = $gUser->email;
    $name = $gUser->name;

    // Kiểm tra user tồn tại qua email
    $sql = "SELECT * FROM nguoidung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Nếu chưa có user → tạo mới
    if ($result->num_rows == 0) {
        $insert = $conn->prepare("
            INSERT INTO nguoidung (tenDangnhap, matKhau, hoTen, email, quyen, trangThai) 
            VALUES (?, '', ?, ?, 1, 1)
        ");
        $insert->bind_param("sss", $email, $name, $email);
        $insert->execute();
    }

    // Lấy lại user để tạo session
    $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $_SESSION['tenDangnhap'] = $user['tenDangnhap'];
    $_SESSION['emailNguoidung'] = $user['email'];
    $_SESSION['quyen'] = $user['quyen'];
    $_SESSION['trangThai'] = $user['trangThai'];

    header("Location: index.php");
    exit();
}

echo "Không nhận được code từ Google!";
