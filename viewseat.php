<?php
require_once("dbconfig.php");
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

// 抓出所有 room
$room_sql = "SELECT DISTINCT room FROM seat";
$room_result = mysqli_query($conn, $room_sql);
$rooms = [];
while ($row = mysqli_fetch_assoc($room_result)) {
    $rooms[] = $row['room'];
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>查詢修改座位表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image:url("image/back3.png");
            background-repeat: no-repeat;            /* 不重複 */
            background-position: top center;         /* 貼齊上方置中 */
            background-attachment: fixed;            /* 固定背景位置 */
            background-size: 1500px; 
        }

        .grid-btn {
            width: 60px; height: 60px; margin: 7px;
            position: relative; font-size: 14px;
            color: white; border: none;
            border-radius:7px;
        }
        /* 滑鼠懸停 */
        .state-0:hover {
        background-color: #0a58ca; /* 深藍色 */
        }
        .state-2:hover {
        background-color: rgb(62, 96, 126); /* 深藍色 */
        }

        /* 按下按鈕 */
        .state-0:active {
        background-color: #06357a; /* 更深藍色 */
        }
        .state-0 { background-color: rgb(11, 152, 218); cursor: pointer; }
        .state-1 { background-color:rgb(219, 98, 92); cursor: not-allowed; }
        .state-2 { background-color:rgb(119, 144, 167); cursor: not-allowed; }
        .empty { background-color: white; } 
        .top-left {
            position: absolute; top: 2px; left: 5px; font-size: 14px; color: white;
        }
        
        .lightning {
            position: absolute;
            bottom: 5px;
            right: 12px;
            font-size: 20px;
            color: rgb(239, 255, 98);
            pointer-events: none; /* 避免干擾點擊事件 */
        }

        .t1{ border-radius:5px; border: 4px rgb(255, 231, 98)  solid;}
        .t1:hover{ border-radius:5px; border:4px rgb(255, 186, 126) solid;  outline:none}

        .b1{width:80px;height:35px; border:none; border-radius:10px;background-color: rgb(53, 159, 136) ; color:aliceblue;}
        .b1:hover{width:82px;height:37px; border:none; border-radius:10px;background-color: rgb(31, 116, 98) ; color:aliceblue;}
        
</style>
</head>
<body class="text-center">
<br><br><br><br>
<h2>查詢及修改<br>座位表</h2>
    <div class="d-flex justify-content-center">
    <div style="margin-right: auto; margin-left: 15%;">
        <select id="roomSelect" class="t1 form-select  mt-3" style='width: 140px;'>
            <option value="">請選擇教室</option>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= htmlspecialchars($room) ?>"><?= htmlspecialchars($room) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    </div>
    <br>
    <p id='explainlable' style='visibility:hidden;'>
    - 點一下可新增/刪除插座，再點一次即可復原 -
    </p>
    <div id="seatContainer" class="mt-4"></div>
    <br>
    <button id="editBtn" class="b1" style="display:none;">修改</button>
    <br><br>
    <script>
    document.getElementById('roomSelect').addEventListener('change', function () {
        //取得選到的房間值，若為空值（例如未選擇），就直接中止不做任何事
        const room = this.value; 
        if (!room) return;

        //透過 AJAX 向 fetch_seat.php 發送 GET 請求取得該房間的座位資料，並將回應解析為 JSON
        fetch('fetch_seat.php?room=' + room)
            .then(res => res.json())
            .then(data => { //處理解析後的 JSON 資料（data 物件）
                //找到座位顯示區塊並清空內容
                const container = document.getElementById('seatContainer');
                container.innerHTML = '';

                //從 JSON 中取出行數、列數與座位資訊
                const rows = data.rows;
                const cols = data.cols;
                const seats = data.seats;

                //建立一個座位查找表，key 為 row_col 格式，值為該座位的詳細資料
                const seatMap = {}; // key: r_c
                seats.forEach(seat => {
                    seatMap[`${seat.seatr}_${seat.seatc}`] = seat;
                });

                for (let r = 1; r <= rows; r++) {
                    //建立每一列的 <div> 容器
                    const rowDiv = document.createElement('div');
                    for (let c = 1; c <= cols; c++) {
                        //建立每個座位按鈕並套用CSS格式
                        const key = `${r}_${c}`;
                        const btn = document.createElement('button');
                        btn.classList.add('grid-btn');

                        const label = document.createElement('div');
                        label.className = 'top-left';
                        label.textContent = `R${r}C${c}`;
                        btn.appendChild(label);

                        if (seatMap[key]) { //如果該座位在資料庫中存在
                            //取得該座位的狀態與 socket 資訊，並設為按鈕的 data-* 屬性
                            const state = seatMap[key].state;
                            const socket = seatMap[key].socket;
                            btn.dataset.r = r;
                            btn.dataset.c = c;
                            btn.dataset.state = state;
                            btn.dataset.socket = socket; //?
                            btn.dataset.originalSocket = socket;
                            btn.dataset.lightning = '0';

                            //依照狀態值設定不同顏色樣式
                            if (state === 0) btn.classList.add('state-0');
                            else if (state === 1) btn.classList.add('state-1');
                            else btn.classList.add('state-2');

                            //如果 socket 是 1，就在按鈕上顯示閃電圖示
                            if (socket == 1) {
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

                            if (state === 0 || state === 2) {
                                btn.addEventListener('click', function () {
                                    const bolt = btn.querySelector('.lightning');
                                    if (bolt) {
                                        bolt.remove();
                                        btn.dataset.socket = '0';
                                    } else {
                                        const newBolt = document.createElement('div');
                                        newBolt.className = 'lightning';
                                        const img = document.createElement('img');
                                        img.src = 'image/socket.png';
                                        img.alt = 'Socket';
                                        img.style.width = '40px';  // 或你想要的尺寸
                                        img.style.height = 'auto';       // 高度自動根據圖片比例調整
                                        img.style.objectFit = 'contain'; // 防止變形，完整顯示圖片
                                        newBolt.appendChild(img);
                                        btn.appendChild(newBolt);
                                        btn.dataset.socket = '1';
                                    }
                                });
                            }
                        } else { //若該座位在資料庫中不存在（沒人坐），就套用空白樣式
                            btn.classList.add('empty');
                        }

                        //把所有的座位按鈕加到頁面上
                        rowDiv.appendChild(btn);
                    }
                    container.appendChild(rowDiv);
                }

                document.getElementById('editBtn').style.display = 'inline-block';
                document.getElementById('explainlable').style.visibility = 'visible';
            });
    });

    //比對哪些座位的插座狀態有改變，並送到後端更新
    document.getElementById('editBtn').addEventListener('click', function () {
        if (!confirm("是否確認修改？")) return; //若使用者按「取消」，則中止整個函式（return），不執行後續動作

        //找出所有藍色狀態（可修改）的座位按鈕（即 class 為 state-0 的元素），並存進 buttons 變數中
        const buttons = document.querySelectorAll('.state-0');
        const updates = []; //存所有需要更新的座位資料

        buttons.forEach(btn => { //遍歷每一個藍色座位按鈕，取按鈕的data-屬性
            const r = btn.dataset.r;
            const c = btn.dataset.c;
            const socket = btn.dataset.socket;
            const orig = btn.dataset.originalSocket;

            //如果插座狀態與原本不同，表示使用者有改變，就將該座位的資料（row、col、socket）加入 updates 陣列中
            if (socket !== orig) {
                updates.push({ r, c, socket });
            }
        });

        //如果沒有變，就顯示「無變更」的提示，並停止執行，不進行更新
        if (updates.length === 0) {
            alert("無變更");
            return;
        }

        //取得目前選擇的房間代碼，用來一起送到後端
        const room = document.getElementById('roomSelect').value;

        //使用 fetch API 發送一個 HTTP 請求到 update_socket.php
        fetch('update_socket.php', {
            method: 'POST', //使用 POST 方法來送出資料
            headers: { 'Content-Type': 'application/json' }, //告訴伺服器這是 JSON 格式的資料
            body: JSON.stringify({ room, updates }) //把房間代碼與所有要更新的座位資料組成一個 JSON 字串，當作請求主體（body）送出
        })
        .then(res => res.text()) //伺服器回傳後，將回應轉為純文字（例如：「更新成功」或錯誤訊息）
        .then(msg => { //顯示伺服器的回應訊息（例如：「3 個座位已更新」）給使用者看
            alert(msg);
            //主動觸發房間選單的 change 事件，重新載入座位資料，以便畫面更新
            document.getElementById('roomSelect').dispatchEvent(new Event('change'));
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>
</html>
