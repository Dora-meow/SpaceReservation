<?php
require_once(__DIR__.'/dbconfig.php');

session_start();
if(!isset($_SESSION["loggedin"])){
    header("location: ./login.php");
    exit;
}
if(!$_SESSION["memberManager"]){
    include("navbar_std.php");
}else{
    include("navbar.php");
}

?>

<html>

<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=1, minimum-scale=1.0, maximum-scale=3.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>首頁</title>
    <style>
        body {
            background-image:url("image/back3.png");
            background-repeat: no-repeat;            /* 不重複 */
            background-position: top center;         /* 貼齊上方置中 */
            background-attachment: fixed;            /* 固定背景位置 */
            background-size: 1500px; 
        }

        .zi_box_1 {
            border: 8px solid #eee;
            padding: 15px;
            position: relative;
            z-index: 0;
            border-radius: 40px;
            /*width: 420px;*/
            background-color: white; /* 背景讓內容更清晰可讀 */
        }
        .zi_box_1:before {
            border-top: 8px solid #ffc98a;
            border-left: 8px solid #bc9678;
            content: '';
            display: block;
            position: absolute;
            top: -8px;
            left: -8px;
            width: 60px;
            height: 60px;
            z-index: 1;
            border-radius: 40px 0px;
        }

        .grid-btn {
            width: 25px; height: 25px; margin-right: 5px;
            position: relative; font-size: 14px;
            color: white; border: none;
            border-radius:7px;
        }
        .state-0 { background-color: rgb(11, 152, 218); cursor: pointer; }
        .state-1 { background-color:rgb(219, 98, 92); cursor: not-allowed; }
        .state-2 { background-color:rgb(119, 144, 167); cursor: not-allowed; }
        .disabled {
        background-color: white !important;
        border: 2px dashed #ccc !important;
        color: white;
        }
        .text{
            display: flex; /* ★ */
            align-items: center; /* ★ */
        }
    </style>
</head>

<body class="text-center">
<br><br><br><br>
<h1 style="color:rgb(127, 98, 58);">WELCOME！</h1>
    <div class="d-flex justify-content-center">
    <div style="width: 65%;">
        <br><br>
        <?php if ($_SESSION["memberManager"] == 1): ?>
            <div id='ma' align='left'>
                <strong>編號：</strong><?= $_SESSION["memberId"]; ?>
            </div>
        <?php endif; ?>
        <div align='left'><strong>帳號：</strong><?= $_SESSION["memberName"]; ?></div>
        <div align='left'><strong>Email：</strong><?= $_SESSION["memberEmail"]; ?></div>
        <br>
        <div class="card zi_box_1" align='left'>
            <div class="card-header h5"  style="background-color:white;">
                <strong>圖示</strong>
            </div>
            <div class="card-body">
                <div class='text'>
                    <button class='grid-btn state-0'></button>無人預約（空座位）
                    &emsp;&emsp;&emsp;
                    <button class='grid-btn state-2'></button>不開放預約
                    &emsp;&emsp;&emsp;
                    <button class='grid-btn state-1'></button>已有人預約
                    &emsp;&emsp;&emsp;
                    <?php if ($_SESSION["memberManager"] == 1): ?>
                        <button class='grid-btn disabled'></button>取消座位（無此座位）
                        &emsp;&emsp;&emsp;
                    <?php endif; ?>
                    <img src="image/socket2.png" style="width: 30px;">&thinsp;有插座
                </div>
                <br>座位編號 RXCY：第X列、第Y行的座位
                <br><br>
                <div align='center'>
                    <h5>- 範例 -<h5>
                    <img src="image/ex.png" style="width: 300px;">
                </div>
                
            </div>
        </div>
        <br><br>
        <!--<button class=''>變更為使用者介面</button>-->

    </div>
    </div>
    <br><br>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
