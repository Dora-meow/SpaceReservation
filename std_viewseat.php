<?php
require_once('dbconfig.php');
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: ./login.php");
    exit;
}
if(!$_SESSION["memberManager"]){
    include("navbar_std.php");
}else{
    include("navbar.php");
}

$room_sql = "SELECT DISTINCT room FROM seat";
$rooms = mysqli_query($conn, $room_sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>座位狀態查詢</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("image/back3.png");
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
            color: white;
            border: none !important;
            border-radius: 7px;
        }
        .top-left {
            position: absolute; top: 2px; left: 5px; font-size: 14px; color: white; z-index: 10;  /* 確保文字顯示在最上層 */
        }
        .available { background-color: rgb(11, 152, 218); }
        .blocked { background-color: rgb(119, 144, 167); cursor: not-allowed; }
        .reserved { background-color: rgb(219, 98, 92); cursor: not-allowed; }
        .empty { background-color: white;  }
        .lightning {
            position: absolute;
            bottom: 5px;
            right: 12px;
            pointer-events: none; /* 避免干擾點擊事件 */
        }
        .t2 { height: 32px; border-radius: 5px; border: 1px #aeb6bf solid; }
        .t2:focus { border: 3px #3498db solid; outline: none; }
        .t2:hover { border: 3px #3498db solid; outline: none; }

        .b1 { width: 100px; height: 35px; border: none; border-radius: 10px; background-color: #5d6d7e; color: aliceblue; }
        .b1:hover { background-color: #2e4053; }
        
        .t1{ border-radius:5px; border: 1px #aeb6bf solid;}
        .t1:hover{ border-radius:5px; border:4px #3498db solid;  outline:none}
    </style>
</head>
<body class="text-center">
<br><br><br><br>
<h2>查詢自習室座位</h2>
<br><br>

<div class="d-flex justify-content-center">
<div style="margin-right: auto; margin-left: 15%;">
    <form id="statusForm" class="text-start">
        選擇日期： <input type="date" id="dateSelect" class="t2 mb-3"  required> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        選擇時段： 
        <select id="timeSelect" class="t2 mb-3" style='width: 120px;' required>
            <option value="">請選擇時段</option>
            <option value="08:00">08:00~9:00</option>
            <option value="09:00">09:00~10:00</option>
            <option value="10:00">10:00~11:00</option>
            <option value="11:00">11:00~12:00</option>
            <option value="12:00">12:00~13:00</option>
            <option value="13:00">13:00~14:00</option>
            <option value="14:00">14:00~15:00</option>
            <option value="15:00">15:00~16:00</option>
            <option value="16:00">16:00~17:00</option>
            <option value="17:00">17:00~18:00</option>
            <option value="18:00">18:00~19:00</option>
        </select>
        <br><br>

        <select id="roomSelect" class="form-select mb-3 t1" style='width: 140px;' required>
            <option value="">請選擇教室</option>
            <?php while ($row = mysqli_fetch_assoc($rooms)) {
                echo "<option value='{$row['room']}'>{$row['room']}</option>";
            } ?>
        </select>
    </form>
    
</div>
</div>

<br>
<div id="seatContainer" class="text-center"></div>

<br><br>
<script>
document.getElementById('roomSelect').addEventListener('change', loadSeats);
document.getElementById('dateSelect').addEventListener('change', loadSeats);
document.getElementById('timeSelect').addEventListener('change', loadSeats);

async function loadSeats() {
    const room = document.getElementById('roomSelect').value;
    const date = document.getElementById('dateSelect').value;
    const time = document.getElementById('timeSelect').value;

    if (!room || !date || !time) return;

    const res = await fetch(`std_load_seats.php?room=${room}&date=${date}&time=${time}`);
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
            btn.style.position = 'relative';  // 使按鈕相對定位
            
            if (!seat) {
                btn.classList.add('empty');
            } else {
                //btn.textContent = `R${r}C${c}`;
                btn.title = seat.status;
                btn.classList.add(seat.status);
                
                //如果 socket 是 1，就在按鈕上顯示閃電圖示
                if (seat.socket == 1) {
                    const bolt = document.createElement('div');
                    bolt.className = 'lightning';
                    const img = document.createElement('img');
                    img.src = 'image/socket.png';
                    img.alt = 'Socket';
                    img.style.width = '40px';  // 或你想要的尺寸
                    img.style.height = 'auto';       // 高度自動根據圖片比例調整
                    img.style.objectFit = 'contain'; // 防止變形，完整顯示圖片
                    bolt.appendChild(img);
                    btn.appendChild(bolt);
                }
            }

            //顯示左上角的座標編號
            const label = document.createElement('div');
            label.className = 'top-left';
            label.textContent = `R${r}C${c}`;
            btn.appendChild(label);
            rowDiv.appendChild(btn);
        }
        container.appendChild(rowDiv);
    }

}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
</body>
</html>
