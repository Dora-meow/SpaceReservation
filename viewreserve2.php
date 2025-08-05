<?php
session_start();
require_once("dbconfig.php");
include("navbar.php");

if (!isset($_SESSION["loggedin"])) {
    header("location: ./login.php");
    exit;
}
if(!$_SESSION["memberManager"]){
    echo "<script>alert('權限不足！'); location.href='welcome.php';</script>";
    exit;
}

// 取得所有教室選項
$roomOptions = [];
$roomSql = "SELECT DISTINCT room FROM seat ORDER BY room ASC";
$roomResult = $conn->query($roomSql);
while ($roomRow = $roomResult->fetch_assoc()) {
    $roomOptions[] = $roomRow['room'];
}

// 取得所有時段選項
$timeOptions = [
    "08:00", "09:00", "10:00", "11:00", "12:00",
    "13:00", "14:00", "15:00", "16:00", "17:00",
    "18:00"
];

// 處理搜尋條件
$where = "1=1";
if (!empty($_GET['username'])) {
    $username = $conn->real_escape_string($_GET['username']);
    $where .= " AND m.username LIKE '%$username%'";
}
if (!empty($_GET['room'])) {
    $room = $conn->real_escape_string($_GET['room']);
    $where .= " AND s.room = '$room'";
}
if (!empty($_GET['date'])) {
    $date = $conn->real_escape_string($_GET['date']);
    $where .= " AND r.date = '$date'";
}
if (!empty($_GET['time'])) {
    $time = $conn->real_escape_string($_GET['time']);
    $where .= " AND r.time = '$time'";
}

// 查詢預約紀錄
$sql = "SELECT r.*, s.room, s.seatr, s.seatc, s.socket, m.username
        FROM reserve r
        JOIN seat s ON r.seatID = s.seatID
        JOIN member m ON r.userID = m.userID
        WHERE $where
        ORDER BY r.date DESC, r.time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>預約紀錄</title>
    <style>
        body {
            background-image: url("image/back3.png");
            background-repeat: no-repeat;
            background-position: top center;
            background-attachment: fixed;
            background-size: 1500px;
        }
        .record-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .zi_box_1, .zi_box_2 {
            border-radius: 15px;
            margin: 10px 30px;
            padding: 20px;
            position: relative;
            z-index: 0;
        }
        .zi_box_1:before {
            background: repeating-linear-gradient(-45deg, #a6d3bf, #a6d3bf 5px, #d2f9e8 0, #d2f9e8 10px);
        }
        .zi_box_2:before {
            background: repeating-linear-gradient(-45deg,rgb(156, 169, 164),rgb(156, 169, 164) 5px, rgb(188, 214, 204) 0, rgb(188, 214, 204) 10px);
        }
        .zi_box_1:before, .zi_box_2:before,
        .zi_box_1:after, .zi_box_2:after {
            border-radius: 15px;
            content: '';
            position: absolute;
            top: 0; bottom: 0; left: 0; right: 0;
            z-index: -2;
        }
        .zi_box_1:after { background: #fff; top: 10px; bottom: 10px; left: 10px; right: 10px; z-index: -1; }
        .zi_box_2:after { background: rgb(218, 228, 231); top: 10px; bottom: 10px; left: 10px; right: 10px; z-index: -1; }
    </style>
</head>
<body class="text-center">
<br><br><br><br>
<h2>預約紀錄查詢</h2>
<br><br>
<!-- 查詢表單 -->
<div class="container mt-4 mb-5">
    <form method="get" class="row g-2 justify-content-center">
        <div class="col-md-2">
            <input type="text" class="form-control" name="username" placeholder="使用者名稱" value="<?= isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '' ?>">
        </div>
        <div class="col-md-2">
            <select class="form-select" name="room">
                <option value="">全部教室</option>
                <?php foreach ($roomOptions as $r): ?>
                    <option value="<?= htmlspecialchars($r) ?>" <?= (isset($_GET['room']) && $_GET['room'] == $r) ? 'selected' : '' ?>><?= htmlspecialchars($r) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
        </div>
        <div class="col-md-2">
            <select class="form-select" name="time">
                <option value="">全部時段</option>
                <?php foreach ($timeOptions as $t): ?>
                    <?php
                        $endTime = date("H:i", strtotime($t . " +1 hour"));
                        $formattedTime = $t . '~' . $endTime;
                    ?>
                    <option value="<?= $t ?>" <?= (isset($_GET['time']) && $_GET['time'] == $t) ? 'selected' : '' ?>><?= $formattedTime ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary">查詢</button>
        </div>
    </form>
</div>

<div class="record-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $socketText = ($row['socket'] == 1) ? '（有插座）' : '';
                $startTime = $row['time'];
                $endTime = date("H:i", strtotime($startTime . " +1 hour"));
                $formattedTime = $startTime . '~' . $endTime;
                $now = new DateTime();
                $reserveDateTime = DateTime::createFromFormat('Y-m-d H:i', $row['date'] . ' ' . $row['time']);
                $isFuture = $reserveDateTime > $now;
            ?>
            <div class="<?= $isFuture ? 'zi_box_1' : 'zi_box_2' ?>" style="width: 66%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 16%;"><i class="bi bi-person-circle"></i> <strong>使用者：</strong><?= htmlspecialchars($row['username']) ?></td>
                        <td style="width: 17%;"><i class="bi bi-door-open-fill"></i> <strong>教室：</strong><?= htmlspecialchars($row['room']) ?></td>
                        <td style="width: 25%;"><img src="image/seaticon6.png" style="width: 18px;"> <strong>座位：</strong>R<?= $row['seatr'] ?>C<?= $row['seatc'] ?> <?= $socketText ?></td>
                        <td style="width: 21%;"><i class="bi bi-calendar-week-fill"></i> <strong>日期：</strong><?= $row['date'] ?></td>
                        <td style="width: 21%;"><i class="bi bi-clock-fill"></i> <strong>時段：</strong><?= $formattedTime ?></td>
                    </tr>
                </table>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <img src="image/nodata3.png" style="width:400px;" />
        <br>
    <?php endif; ?>
</div>
<br><br>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
