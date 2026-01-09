<?php
session_start();
require_once("config.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ctgh'])) {
    $id_ctgh = (int)$_POST['id_ctgh'];
    $soLuongReq = (int)$_POST['soLuong'];

    // Lấy tồn kho thực tế từ DB để đối soát
    $sql_check = "SELECT m.soLuongTon, m.tenMH 
                  FROM chitietgiohang ct 
                  JOIN mathang m ON ct.maMH = m.maMH 
                  WHERE ct.id = $id_ctgh";
    $res = $conn->query($sql_check);
    $data = $res->fetch_assoc();

    if ($soLuongReq > $data['soLuongTon']) {
        echo json_encode([
            'status' => 'error', 
            'message' => "Sản phẩm {$data['tenMH']} không đủ tồn kho.",
            'current_stock' => $data['soLuongTon']
        ]);
        exit();
    }

    if ($soLuongReq > 0) {
        $sql = "UPDATE chitietgiohang SET soluong = $soLuongReq WHERE id = $id_ctgh";
        if($conn->query($sql)) {
            echo json_encode(['status' => 'success']);
        }
    }
}