<?php
require_once("dbconfig.php");

$data = json_decode(file_get_contents("php://input"), true);
$seatIDs = $data['seatIDs'] ?? [];
$startDate = $data['startDate'] ?? '';
$endDate = $data['endDate'] ?? '';

if (!$startDate || !$endDate || empty($seatIDs)) {
    http_response_code(400);
    echo "缺少必要資料";
    exit;
}

// 插入封鎖資料
$stmt = $conn->prepare("INSERT INTO block (seatID, startDate, endDate) VALUES (?, ?, ?)");

foreach ($seatIDs as $seatID) {
    $stmt->bind_param("iss", $seatID, $startDate, $endDate);
    $stmt->execute();
}

$stmt->close();
echo "不開放日期儲存完成";
