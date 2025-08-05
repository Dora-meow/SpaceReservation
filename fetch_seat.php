<?php
require_once("dbconfig.php");

$room = $_GET['room'] ?? '';
//用現在時間
date_default_timezone_set('Asia/Taipei');
$date = date('Y-m-d');
$time = date('H') . ':00';

//把想看的東西輸出到debug.txt
//file_put_contents("debug.txt", "Time is: $time\n", FILE_APPEND);
//file_put_contents("debug.txt", "date is: $date\n", FILE_APPEND);


if (!$room || !$date || !$time) exit;

$sql = "SELECT * FROM seat WHERE room = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $room);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$seats = [];
$map = [];
$maxRow = 0;
$maxCol = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $key = $row['seatr'] . '_' . $row['seatc'];
    $map[$key] = [
        'seatID' => $row['seatID'],
        'seatr' => $row['seatr'],
        'seatc' => $row['seatc'],
        'state' => 0,  // 初始為資料庫中的狀態
        'socket' => $row['socket']
    ];
    if ($row['seatr'] > $maxRow) $maxRow = $row['seatr'];
    if ($row['seatc'] > $maxCol) $maxCol = $row['seatc'];
}

// 封鎖狀態（block） - date 落在 startDate~endDate 之間
$block_sql = "SELECT seatID FROM block WHERE ? BETWEEN startDate AND endDate";
$stmt = $conn->prepare($block_sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    foreach ($map as $key => $seat) {
        if ($seat['seatID'] == $row['seatID']) {
            $map[$key]['state'] = 2;  // '2' 表示封鎖
        }
    }
}
$stmt->close();

// 預約狀態（reserve）- date, time 完全匹配，且尚未封鎖
$reserve_sql = "SELECT seatID FROM reserve WHERE date = ? AND time = ?";
$stmt = $conn->prepare($reserve_sql);
$stmt->bind_param("ss", $date, $time);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    foreach ($map as $key => $seat) {
        if ($seat['seatID'] == $row['seatID'] && $map[$key]['state'] == 0) {
            $map[$key]['state'] = 1;  // '1' 表示已預約
        }
    }
}
$stmt->close();

$seats = array_values($map);

echo json_encode([
    'rows' => $maxRow,
    'cols' => $maxCol,
    'seats' => $seats
]);
