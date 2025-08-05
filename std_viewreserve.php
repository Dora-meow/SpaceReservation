<?php
session_start();
require_once("dbconfig.php");
require_once("func_sendemail_sample.php"); //寄mail

if (!isset($_SESSION["loggedin"])) {
    header("location: ./login.php");
    exit;
}
if(!$_SESSION["memberManager"]){
    include("navbar_std.php");
}else{
    include("navbar.php");
}

$userID = $_SESSION["memberId"];

// 取消預約
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_id'])) {
    $cancelID = intval($_POST['cancel_id']);
    $stmtCancel = $conn->prepare("DELETE FROM reserve WHERE reID = ? AND userID = ?");
    $stmtCancel->bind_param("ii", $cancelID, $userID);
    $stmtCancel->execute();
    $stmtCancel->close();

    // 寄信通知
    $stmtUser = $conn->prepare("SELECT email, username FROM member WHERE userID = ?");
    $stmtUser->bind_param("i", $userID);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $user = $resultUser->fetch_assoc();
    $stmtUser->close();

    if ($user) {
        sendemail_sample(
            'b112040007@student.nsysu.edu.tw', "Meow預約系統",
            $user['email'], $user['username'],
            "【預約取消通知】",
            "<p><strong>{$user['username']}</strong> 您好，</p>
            <p>您已成功取消一筆座位預約。</p>
            <p>若此操作非本人操作，請與管理員聯繫，謝謝！</p>
            <br><p>Meow預約系統</p>"
        );
    }
}

// 處理搜尋條件
$room = $_GET['room'] ?? '';
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

// 教室清單
$roomOptions = [];
$roomRes = $conn->query("SELECT DISTINCT room FROM seat ORDER BY room");
while ($r = $roomRes->fetch_assoc()) $roomOptions[] = $r['room'];

// 查詢預約資料
$sql = "SELECT r.*, s.room, s.seatr, s.seatc, s.socket 
        FROM reserve r
        JOIN seat s ON r.seatID = s.seatID
        WHERE r.userID = ?";
$types = "i";
$params = [$userID];

if ($room !== '') {
    $sql .= " AND s.room = ?";
    $types .= "s";
    $params[] = $room;
}
if ($date !== '') {
    $sql .= " AND r.date = ?";
    $types .= "s";
    $params[] = $date;
}
if ($time !== '') {
    $sql .= " AND r.time = ?";
    $types .= "s";
    $params[] = $time;
}

$sql .= " ORDER BY r.date DESC, r.time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>預約紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-image: url("image/back3.png");
            background-repeat: no-repeat;
            background-position: top center;
            background-attachment: fixed;
            background-size: 1500px;
        }
        .record-container { display: flex; justify-content: center; flex-wrap: wrap; }
        .zi_box_1, .zi_box_2 {
            border-radius: 15px;
            margin: 10px 30px;
            padding: 20px;
            position: relative;
            z-index: 0;
            width: 61%;
        }
        .zi_box_1:before, .zi_box_2:before {
            border-radius: 15px;
            content: '';
            position: absolute;
            top: 0; bottom: 0; left: 0; right: 0;
            z-index: -2;
        }
        .zi_box_1:before {
            background: repeating-linear-gradient(-45deg, #a6d3bf, #a6d3bf 5px, #d2f9e8 0, #d2f9e8 10px);
        }
        .zi_box_1:after {
            background: #fff;
        }
        .zi_box_2:before {
            background: repeating-linear-gradient(-45deg,rgb(156, 169, 164),rgb(156, 169, 164) 5px, rgb(188, 214, 204) 0, rgb(188, 214, 204) 10px);
        }
        .zi_box_2:after {
            background: rgb(218, 228, 231);
        }
        .zi_box_1:after, .zi_box_2:after {
            border-radius: 15px;
            content: '';
            position: absolute;
            top: 10px; bottom: 10px; left: 10px; right: 10px;
            z-index: -1;
        }
    </style>
</head>
<body class="text-center">
<br><br><br><br>
<h2>預約紀錄</h2>
<br><br>

<!-- 篩選表單 -->
<form method="GET" class="d-flex justify-content-center mb-4">
    <div class="mx-2" style='width: 170px;'>
        <select name="room" class="form-select">
            <option value="">全部教室</option>
            <?php foreach ($roomOptions as $opt): ?>
                <option value="<?= $opt ?>" <?= $room === $opt ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mx-2" style='width: 170px;'>
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
    </div>
    <div class="mx-2" style='width: 170px;'>
        <select name="time" class="form-select">
            <option value="">全部時段</option>
            <?php
            $times = ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00",
                      "14:00", "15:00", "16:00", "17:00", "18:00"];
            foreach ($times as $t) {
                $label = "$t~" . date("H:i", strtotime("$t +1 hour"));
                echo "<option value='$t'" . ($time === $t ? ' selected' : '') . ">$label</option>";
            }
            ?>
        </select>
    </div>
    <button class="btn btn-primary mx-2" type="submit">查詢</button>
</form>

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
            <div class="<?= $isFuture ? 'zi_box_1' : 'zi_box_2' ?>">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 17%;"><i class="bi bi-door-open"></i><strong> 教室：</strong><?= htmlspecialchars($row['room']) ?></td>
                        <td style="width: 24%;"><strong>座位：</strong>R<?= $row['seatr'] ?>C<?= $row['seatc'] ?> <?= $socketText ?></td>
                        <td style="width: 22%;"><i class="bi bi-calendar2-week"></i><strong> 日期：</strong><?= $row['date'] ?></td>
                        <td style="width: 22%;"><i class='bi bi-clock'></i><strong> 時段：</strong><?= $formattedTime ?></td>
                        <td style="width: 14%; vertical-align: middle; text-align: center;">
                            <?php if ($isFuture): ?>
                            <form method="post" onsubmit="return confirm('確定要取消這筆預約嗎？');" class="d-flex justify-content-center align-items-center" style='height: 30px; margin: 5px;'>
                                <input type="hidden" name="cancel_id" value="<?= $row['reID'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" >取消預約</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <img src="image/nodata.png" style="width:450px;" />
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
</body>
</html>
