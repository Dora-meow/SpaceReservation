<?php
require_once("dbconfig.php");

$room = $_GET['room'] ?? '';
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$room || !$date || !$time) {
    echo json_encode(['error' => '缺少參數']);
    exit;
}

// 查詢該教室所有座位
$seat_sql = "SELECT * FROM seat WHERE room = ?";
$stmt = $conn->prepare($seat_sql);
$stmt->bind_param("s", $room);
$stmt->execute();
$result = $stmt->get_result();

$map = [];
$maxRow = 0;
$maxCol = 0;

while ($row = $result->fetch_assoc()) {
    $seatID = $row['seatID'];
    $r = $row['seatr'];
    $c = $row['seatc'];
    $socket = $row['socket'];
    $mapKey = "{$r}_{$c}";
    $map[$mapKey] = [
        'seatID' => $seatID,
        'socket' => $socket,
        'status' => 'available'  // 預設為可用
    ];
    if ($r > $maxRow) $maxRow = $r;
    if ($c > $maxCol) $maxCol = $c;
}
$stmt->close();

// 封鎖狀態（block） - date 落在 start~end 之間
$block_sql = "SELECT seatID FROM block WHERE ? BETWEEN startDate AND endDate";
$stmt = $conn->prepare($block_sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    foreach ($map as $key => $seat) {
        if ($seat['seatID'] == $row['seatID']) {
            $map[$key]['status'] = 'blocked';
        }
    }
}
$stmt->close();

// 預約狀態（reserve）- date, time 完全匹配
$reserve_sql = "SELECT seatID FROM reserve WHERE date = ? AND time = ?";
$stmt = $conn->prepare($reserve_sql);
$stmt->bind_param("ss", $date, $time);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    foreach ($map as $key => $seat) {
        if ($seat['seatID'] == $row['seatID'] && $map[$key]['status'] == 'available') {
            $map[$key]['status'] = 'reserved';
        }
    }
}
$stmt->close();

echo json_encode([
    'rows' => $maxRow,
    'cols' => $maxCol,
    'map' => $map
]);
?>
