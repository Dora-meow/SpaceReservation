<?php
require_once('dbconfig.php');
require_once('func_sendemail_sample.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$userID = $_SESSION["memberId"];
$seatID = $data["seatID"];
$date = $data["date"];
$time = $data["time"];

// 檢查使用者該時段是否預約
$check_sql = "SELECT * FROM reserve WHERE userID = ? AND date = ? AND time = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("iss", $userID, $date, $time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'exists']);
    exit;
}

// 插入預約
$insert_sql = "INSERT INTO reserve (userID, seatID, date, time) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("iiss", $userID, $seatID, $date, $time);
if ($stmt->execute()) {
    // 查詢使用者 Email & 姓名
    $user_sql = "SELECT username, email FROM member WHERE userID=?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $user_result = $stmt->get_result()->fetch_assoc();

    $recipient_email = $user_result['email'];
    $recipient_name = $user_result['username'];

    // 查詢座位資訊
    $seat_sql = "SELECT room, seatr, seatc FROM seat WHERE seatID=?";
    $stmt = $conn->prepare($seat_sql);
    $stmt->bind_param("i", $seatID);
    $stmt->execute();
    $seat_info = $stmt->get_result()->fetch_assoc();

    // 處理時段格式
    $endTime = date("H:i", strtotime($time . " +1 hour"));
    $formattedTime = $time . '~' . $endTime;
    
    // 寄件人資訊
    $fromEmail = 'b112040007@student.nsysu.edu.tw'; // 學號
    $fromName = "Meow預約系統";

    // 寄信
    $subject = "【自習室預約成功通知】";
    $body = "
    <p><strong>{$recipient_name}</strong> 您好，</p>
    <p>您已成功預約！<br>
    教室：{$seat_info['room']}<br>
    座位：R{$seat_info['seatr']}C{$seat_info['seatc']}<br>
    日期：{$date}<br>
    時間：{$formattedTime}</p>
    <p>請準時使用，謝謝！</p>
    <br>
    <p>Meow預約系統</p>
    ";

    sendemail_sample($fromEmail, $fromName, $recipient_email, $recipient_name, $subject, $body);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'fail']);
}
?>
