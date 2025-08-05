<?php
require_once("dbconfig.php");

$room = $_GET['room'] ?? '';
$startDate = $_GET['start'] ?? '';
$endDate = $_GET['end'] ?? '';

if (!$room || !$startDate || !$endDate) {
    echo json_encode(['error' => '缺少參數']);
    exit;
}

// 查詢所有該 room 的座位
$seat_sql = "SELECT * FROM seat WHERE room = ?";
$stmt = $conn->prepare($seat_sql);
$stmt->bind_param("s", $room);
$stmt->execute();
$result = $stmt->get_result();

$map = [];
$maxRow = 0;
$maxCol = 0;
$rowCount = 0;
$colCount = 0;

while ($row = $result->fetch_assoc()) {
    $seatID = $row['seatID'];
    $r = $row['seatr'];   // 修正這裡
    $c = $row['seatc'];   // 修正這裡
    $mapKey = "{$r}_{$c}";
    $map[$mapKey] = [
        'seatID' => $seatID,
        'blocked' => false,
        'reserved' => false,
    ];
    if ($r > $maxRow) $maxRow = $r;
    if ($c > $maxCol) $maxCol = $c;

    $rowCount = $row['row'];  // 從資料中拿總列數
    $colCount = $row['col'];  // 總欄數
}
$stmt->close();

// 查 block
$block_sql = "SELECT seatID FROM block WHERE startDate <= ? AND endDate >= ?";
$stmt = $conn->prepare($block_sql);
$stmt->bind_param("ss", $endDate, $startDate);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    foreach ($map as $key => $seat) {
        if ($seat['seatID'] == $row['seatID']) {
            $map[$key]['blocked'] = true;
        }
    }
}
$stmt->close();

// 查 reserve
$reserve_sql = "SELECT seatID FROM reserve WHERE date BETWEEN ? AND ?";
$stmt = $conn->prepare($reserve_sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    foreach ($map as $key => $seat) {
        if ($seat['seatID'] == $row['seatID']) {
            $map[$key]['reserved'] = true;
        }
    }
}
$stmt->close();

echo json_encode([
    'rows' => $rowCount ?: $maxRow,
    'cols' => $colCount ?: $maxCol,
    'map' => $map
]);

?>
