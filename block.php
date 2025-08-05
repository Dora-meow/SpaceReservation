<?php
require_once('dbconfig.php');
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

// 取得所有出現過的 room 值
$room_sql = "SELECT DISTINCT room FROM seat";
$rooms = mysqli_query($conn, $room_sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>設定不開放日期</title>
    <style>
        body {
            background-image:url("image/back3.png");
            background-repeat: no-repeat;
            background-position: top center;
            background-attachment: fixed;
            background-size: 1500px; 
        }
        .seat { 
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
        /* 滑鼠懸停 */
        .available:hover {
        background-color: #0a58ca; /* 深藍色 */
        }
        .empty { background: white;}
        .blocked { background-color:rgb(119, 144, 167); cursor: not-allowed; color: white; }
        .reserved { background-color:rgb(219, 98, 92); cursor: not-allowed; color: white; }
        .available { background: rgb(11, 152, 218); color: white; cursor: pointer; }
        .selected { background: #06357a; color: white; }
        
        .t1{ border-radius:5px; border: 4px rgba(242, 145, 208, 0.78)  solid;}
        .t1:hover{ border-radius:5px; border:4px rgb(174, 90, 239) solid;  outline:none}

        .t2{height:32px; border-radius:5px; border: 1px #aeb6bf  solid;}
        .t2:focus{height:32px; border-radius:5px; border:3px rgb(174, 90, 239)  solid;  outline:none}
        .t2:hover{height:32px; border-radius:5px; border:3px rgb(174, 90, 239)  solid;  outline:none}

        .b1{width:100px;height:35px; border:none; border-radius:10px;background-color: #5d6d7e ; color:aliceblue;}
        .b1:hover{width:103px;height:37px; border:none; border-radius:10px;background-color: #2e4053 ; color:aliceblue;}

    </style>
</head>
<body class="text-center">
<br><br><br><br>
<h2>設定座位<br>不開放日期</h2>
<br><br>
<div class="d-flex justify-content-center">
    <div style="margin-right: auto; margin-left: 15%;">
        <form id="seatForm">
            起始日期：<input type="date" class='t2' id="startDate" name="startDate" required> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            結束日期：<input type="date" class='t2' id="endDate" name="endDate" required>
            <br><br>
            
            <select id="roomSelect" name="room" class="t1 form-select  mt-3" style='width: 140px;' required>
                <option value="">請選擇教室</option>
                <?php while($row = mysqli_fetch_assoc($rooms)) {
                    echo "<option value='{$row['room']}'>{$row['room']}</option>";
                } ?>
            </select>
        </form>
    </div>
</div>

<br>
<p id='explainlable' style='visibility:hidden;'>
- 請選擇不開放之座位 -
</p>
<div id="seatContainer"></div>
<br>
<br>
<button onclick="submitBlocks()" class='b1'>儲存設定</button>
<br><br>

<script>
document.getElementById('roomSelect').addEventListener('change', loadSeats);
document.getElementById('startDate').addEventListener('change', loadSeats);
document.getElementById('endDate').addEventListener('change', loadSeats);
async function loadSeats() {
    const room = document.getElementById('roomSelect').value;
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    if (!room || !start || !end) return;

    if (start > end) {
        alert("開始日期不能晚於結束日期！");
        return;
    }

    const res = await fetch(`load_seats.php?room=${room}&start=${start}&end=${end}`);
    const data = await res.json();

    const container = document.getElementById('seatContainer');
    container.innerHTML = '';

    for (let r = 1; r <= data.rows; r++) {
        const rowDiv = document.createElement('div');
        for (let c = 1; c <= data.cols; c++) {
            const key = `${r}_${c}`;
            const seat = data.map[key];
            const btn = document.createElement('button');
            btn.classList.add('seat');

            if (!seat) {
                btn.classList.add('empty');
            } else if (seat.blocked) {
                btn.classList.add('blocked');
            } else if (seat.reserved) {
                btn.classList.add('reserved');
            } else {
                btn.classList.add('available');
                btn.onclick = () => {
                    btn.classList.toggle('selected');
                };
            }

            btn.textContent = `R${r}C${c}`;
            btn.dataset.seatID = seat?.seatID ?? '';
            rowDiv.appendChild(btn);
        }
        container.appendChild(rowDiv);
    }
    document.getElementById('explainlable').style.visibility = 'visible';
}

async function submitBlocks() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    const room = document.getElementById('roomSelect').value;

    if (!room || !start || !end) {
        alert("請輸入完整資料！");
        return;
    }

    if (start > end) {
        alert("開始日期不能晚於結束日期！");
        return;
    }
    
    const selected = document.querySelectorAll('.selected');
    const seatIDs = Array.from(selected).map(btn => btn.dataset.seatID).filter(Boolean);
    if (seatIDs.length === 0) {
        alert("請選擇座位！");
        return;
    }

    const res = await fetch('save_blocks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ seatIDs, startDate: start, endDate: end })
    });
    const result = await res.text();
    alert(result);
    loadSeats();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
</body>
</html>
