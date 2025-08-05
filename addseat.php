<?php
require_once(__DIR__ . '/dbconfig.php');
include("navbar.php");
session_start();
if(!isset($_SESSION["loggedin"])){
    header("location: ./login.php");
    exit;
}
if(!$_SESSION["memberManager"]){
    echo "<script>alert('權限不足！'); location.href='welcome.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room = $_POST["room"];
    $seats = json_decode($_POST['seats'], true);
    $total_rows = $_POST['total_rows'];
    $total_cols = $_POST['total_cols'];

    if (!empty($room)) {
        $check_sql = "SELECT 1 FROM seat WHERE room = ? LIMIT 1";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $room);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (!mysqli_stmt_fetch($check_stmt)) {
            $insert_sql = "INSERT INTO seat(room, row, col, seatr, seatc, socket) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);

            if (!$insert_stmt) {
                die("SQL 準備失敗：" . mysqli_error($conn));
            }

            foreach ($seats as $seat) {
                $r = intval($seat['r']);
                $c = intval($seat['c']);
                $s = intval($seat['socket']);
                mysqli_stmt_bind_param($insert_stmt, "siiiii", $room, $total_rows, $total_cols, $r, $c, $s);
                mysqli_stmt_execute($insert_stmt);
            }

            echo "<script>alert('座位資料儲存成功！'); location.href='welcome.php';</script>";
            exit;
        }
        else{
            echo "<script>alert('此教室編號已存在，請使用其他編號'); history.back();</script>";
            exit;
        }
    }
    else{
        echo "<script>alert('請輸入自習室教室編號'); history.back();</script>";
        exit;
    }
    mysqli_stmt_close($insert_stmt);
}
mysqli_close($conn);
?>

<!doctype html>
<html lang="zh-hant">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background-image: url("image/back3.png");
            background-repeat: no-repeat;
            background-position: top center;
            background-attachment: fixed;
            background-size: 1500px; 
        }
        html, body {
            height: 100%;
            margin: 0;
            overflow: auto;
        }

        table {
            width: 100%;
            margin: 0 auto;
        }

        .grid-btn {
            width: 60px;
            height: 60px;
            margin: 7px;
            font-size: 14px;
            background-color:rgb(11, 152, 218);
            color: white;
            border: none !important;
            position: relative;
            border-radius: 7px;
        }
        .top-left {
            position: absolute;
            top: 2px;
            left: 5px;
            font-size: 14px;
            color: white;
        }
        .lightning {
            position: absolute;
            bottom: 5px;
            right: 12px;
            font-size: 20px;
            color:rgb(239, 255, 98);
            pointer-events: none;
        }
        .disabled-btn {
            background-color: white !important;
            border: 2px dashed #ccc !important;
            color: white;
        }
        .grid-btn:hover {
            background-color: #0a58ca;
        }
        .grid-btn:active {
            background-color: #06357a;
        }
        .b1 {
            width:80px;
            height:35px;
            border:none;
            border-radius:10px;
            background-color: #5d6d7e;
            color:aliceblue;
        }
        .b1:hover {
            width:82px;
            height:37px;
            background-color:#2e4053;
        }
        .b2 {
            width:100px;
            height:33px;
            border:none;
            border-radius:10px;
            background-color: #206af5;
            color:aliceblue;
        }
        .b2:hover {
            width:103px;
            height:35px;
            background-color:#cd5757;
        }
        .t2 {
            height:30px;
            border-radius:5px;
            border-width:thin;
            border: 1px #aeb6bf solid;
        }
        .t2:focus {
            border:3px #3498db solid;
            outline:none
        }
    </style>
    <title>新增座位</title>
</head>

<body class="text-center">
<br><br><br><br>
<h2>新增座位表</h2>
<div class="container justify-content-center">
    <br><br>
    自習室教室編號：<input id="room" class='t2' required>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    列數：<input type="number" id="rows" min="1" value="1" class='t2' style='width:80px;'>&nbsp;&nbsp;&nbsp;
    行數：<input type="number" id="cols" min="1" value="1" class='t2' style='width:80px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button onclick="generateGrid()" class='b2'>產生座位</button>

    <form id="seatForm" method="POST" action="addseat.php">
        <input type="hidden" name="room" id="roomInput" required>  
        <input type="hidden" name="total_rows" id="totalRows">
        <input type="hidden" name="total_cols" id="totalCols">
        <br>
        <p id='explainlable' style='visibility:hidden;'>- 每次點擊可變更座位狀態 -</p>
        <div id="gridContainer" style="margin-top:20px;"></div>
        <input type="hidden" name="seats" id="seatsInput">
        <br>
        <button type="submit" class='b1'>提交</button>
    </form>

    <script>
        function generateGrid() {
            document.getElementById('roomInput').value = document.getElementById('room').value;
            const rows = parseInt(document.getElementById('rows').value);
            const cols = parseInt(document.getElementById('cols').value);
            const container = document.getElementById('gridContainer');
            document.getElementById('totalRows').value = rows;
            document.getElementById('totalCols').value = cols;
            container.innerHTML = '';

            for (let r = 1; r <= rows; r++) {
                const rowDiv = document.createElement('div');
                for (let c = 1; c <= cols; c++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'grid-btn btn btn-secondary';
                    btn.dataset.row = r;
                    btn.dataset.col = c;
                    btn.dataset.state = 'normal';
                    btn.onclick = handleClick;

                    const label = document.createElement('div');
                    label.className = 'top-left';
                    label.textContent = `R${r}C${c}`;

                    btn.appendChild(label);
                    rowDiv.appendChild(btn);
                }
                container.appendChild(rowDiv);
            }
            document.getElementById('explainlable').style.visibility = 'visible';
        }

        function handleClick(e) {
            const btn = e.currentTarget;
            const state = btn.dataset.state;
            const label = btn.querySelector('.top-left');

            btn.innerHTML = '';
            btn.appendChild(label);

            if (state === 'normal') {
                const bolt = document.createElement('div');
                bolt.className = 'lightning';
                const img = document.createElement('img');
                img.src = 'image/socket.png';
                img.alt = 'Socket';
                img.style.width = '40px';
                img.style.height = 'auto';
                img.style.objectFit = 'contain';
                bolt.appendChild(img);
                btn.appendChild(bolt);
                btn.dataset.state = 'lightning';
            } else if (state === 'lightning') {
                btn.classList.add('disabled-btn');
                btn.innerHTML = '';
                btn.appendChild(label);
                btn.dataset.state = 'disabled';
            } else {
                btn.classList.remove('disabled-btn');
                btn.innerHTML = '';
                btn.appendChild(label);
                btn.dataset.state = 'normal';
            }
        }

        document.getElementById('seatForm').addEventListener('submit', function(e) {
            const seats = [];
            document.querySelectorAll('.grid-btn').forEach(btn => {
                if (btn.dataset.state !== 'disabled') {
                    seats.push({
                        r: btn.dataset.row,
                        c: btn.dataset.col,
                        socket: btn.dataset.state === 'lightning' ? 1 : 0
                    });
                }
            });
            document.getElementById('seatsInput').value = JSON.stringify(seats);
        });
    </script>
</div>
<br><br>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
</body>
</html>
