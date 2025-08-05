<?php
session_start();
if(!$_SESSION["memberManager"]){
    include("navbar_std.php");
}else{
    include("navbar.php");
}
?>

<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background-image:url("image/back2.png");
            background-repeat: no-repeat;            /* 不重複 */
            background-position: top center;         /* 貼齊上方置中 */
            background-attachment: fixed;            /* 固定背景位置 */
            background-size: 1500px; 
        }
        html, body {
            height: 100%;
            margin: 0;
            overflow: auto;
        }

        .b1{width:52px;height:30px; line-height: 30px;/*垂直置中*/ border:none; border-radius:10px;background-color:rgb(60, 157, 101); color:aliceblue;}
        .b1:hover{width:57px;height:35px; line-height: 35px; border:none; border-radius:10px;background-color: #1b7441 ; color:aliceblue;}
        .b2{width:52px;height:30px; line-height: 30px; border:none; border-radius:10px;background-color:rgb(200, 87, 75); color:aliceblue;}
        .b2:hover{width:57px;height:35px; line-height: 35px; border:none; border-radius:10px;background-color: #b53e32 ; color:aliceblue;}
    </style>
    <title>登出</title>
</head>

<body class="text-center">
<br><br><br><br>
    <h2>是否登出</h2>
    <div class="container justify-content-center">
        <br><br>
        <div class="b1" style="display: inline-block;">
            <a href="./logout.php" style='color: white; text-decoration: none;'>是</a>
        </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <div class="b2" style="display: inline-block;">
            <a href="./welcome.php" style='color: white; text-decoration: none;'>否</a>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>