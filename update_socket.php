<?php
require_once("dbconfig.php");

$data = json_decode(file_get_contents("php://input"), true);
$room = $data['room'] ?? '';
$updates = $data['updates'] ?? [];

if (!$room || empty($updates)) {
    http_response_code(400);
    echo "無效請求";
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE seat SET socket=? WHERE room=? AND seatr=? AND seatc=?");
$count = 0;

foreach ($updates as $u) {
    mysqli_stmt_bind_param($stmt, "isii", $u['socket'], $room, $u['r'], $u['c']);
    if (mysqli_stmt_execute($stmt)) {
        $count++;
    }
}

echo "成功更新 $count 筆座位的插座狀態";
?>
