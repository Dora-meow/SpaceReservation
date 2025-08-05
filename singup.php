<?php
require_once(__DIR__ . '/dbconfig.php');


session_start();
if(!isset($_SESSION["loggedin"])){
    include("navbar0.php");
}else{
    if(!$_SESSION["memberManager"]){
        include("navbar_std.php");
    }else{
        include("navbar.php");
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 取得 POST 過來的資料 / Get the data from POST
    $username = $_POST["username"];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $email = $_POST['email'];
    $manager = isset($_POST['manager']) ? 1 : 0;
    
    // 檢查兩次密碼是否相同
    if ($password == $password2) {

        // 檢查帳號是否已存在
        $check_sql = "SELECT userID FROM member WHERE username = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (!mysqli_stmt_fetch($check_stmt)) {
            // 插入資料
            $insert_sql = "INSERT INTO member(username, password, email, manager) VALUES (?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sssi", $username, $password, $email, $manager);

            if (mysqli_stmt_execute($insert_stmt)) {
                echo "<script>alert('註冊成功！'); </script>";
                // 轉跳到登入頁面
                header("location: ./login.php"); 
                exit;
            } else {
                echo "<script>alert('註冊失敗！');</script>";
            }

            mysqli_stmt_close($insert_stmt);
        }
        else{
            echo "<script>alert('此帳號已存在！');</script>";
        }
        mysqli_stmt_close($check_stmt);
    }
    else{
        echo "<script>alert('輸入的密碼不一致 ! ');</script>";
    }

}
mysqli_close($conn)

?>

<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
            overflow: hidden;
        }

        table {
            width: 100%;
            margin: 0 auto;
        }

        .b1{width:80px;height:32px; border:none; border-radius:10px;background-color:#e67070; color:aliceblue;}
        .b1:hover{width:82px;height:34px; border:none; border-radius:10px;background-color:#cd5757 ; color:aliceblue;}

    </style>
    <style>
        .password-wrapper {
            position: relative;
            width: 180px;
            height: 30px;
        }
        .password-wrapper input {
            width: 100%;
            height: 100%;
            padding-right: 35px; /* 空出眼睛位置 */
            box-sizing: border-box;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            border: none;
            background: none;
            cursor: pointer;
            color: #666;
            font-size: 16px;
            padding: 0;
            height: 100%;
        }
        .password-toggle:focus {
            outline: none;
        }
    </style>
    <title>登入</title>
</head>

<body class="text-center">
<br><br><br><br>
    <h2>會員註冊</h2>
    <div class="container justify-content-center">
    <br><br>
        
        <form action="singup.php" method="post">
            <table style='width:500;'>
                <tr>
                    <td style="text-align: right;">帳號：</td>
                    <td><input style='height:30px; width:180;' type="text" name="username" class="form-control" required></td>
                </tr>
                <tr><td colspan="2" style="height: 10px;"></td></tr>

                <tr>
                    <td style="text-align: right;">密碼：</td>
                    <td>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr><td colspan="2" style="height: 10px;"></td></tr>

                <tr>
                    <td style="text-align: right;">再次輸入密碼：</td>
                    <td>
                        <div class="password-wrapper">
                            <input type="password" id="password2" name="password2" class="form-control" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password2', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr><td colspan="2" style="height: 10px;"></td></tr>

                <tr>
                    <td style="text-align: right;">Email：</td>
                    <td><input style='height:30px; width:180;'type="email" name="email" class="form-control" required></td>
                </tr>
                <tr><td colspan="2" style="height: 10px;"></td></tr>

                
            </table>
            <div class='m-1'>
            <input class='form-check-input' type="checkbox" name="manager" value="1">&nbsp;我是管理員
            </div>
            <br><br>
            <button type="submit" class="b1">提交</button>
        </form>
    </div>

    <script>
    function togglePassword(fieldId, button) {
        const input = document.getElementById(fieldId);
        const icon = button.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
