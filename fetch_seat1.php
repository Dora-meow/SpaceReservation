<?php
require_once("dbconfig.php");

$room = $_GET['room'] ?? '';
if (!$room) exit;

$sql = "SELECT * FROM seat WHERE room = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $room);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$seats = [];
$maxRow = 0;
$maxCol = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $seats[] = [
        'seatr' => $row['seatr'],
        'seatc' => $row['seatc'],
        'state' => $row['state'],
        'socket' => $row['socket']
    ];
    if ($row['seatr'] > $maxRow) $maxRow = $row['seatr'];
    if ($row['seatc'] > $maxCol) $maxCol = $row['seatc'];
}

echo json_encode([
    'rows' => $maxRow,
    'cols' => $maxCol,
    'seats' => $seats
]);
