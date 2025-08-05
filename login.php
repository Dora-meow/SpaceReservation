<?php
require_once(__DIR__ . '/dbconfig.php');
include("navbar0.php");

session_start();
if(isset($_SESSION["loggedin"])){
    header("location: ./logout2.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 取得 POST 過來的資料 / Get the data from POST
    $username = $_POST["username"];
    $password = $_POST["password"];

    // 以帳號進資料庫查詢 / Select the data from database using username
    $sql = "SELECT `userID`, `username`, `password`, `email`, `manager` FROM `member` WHERE `username`=?";
    // 使用預處理語句 / Use prepared statement
    
    $stmt = mysqli_prepare($conn, $sql);
    // 使用 mysqli_prepare() 函數準備 SQL 語句 / Prepare SQL statement using mysqli_prepare()

    mysqli_stmt_bind_param($stmt, "s", $username);
    // 使用 mysqli_stmt_bind_param() 函數綁定參數 (prepare) / Bind parameters using mysqli_stmt_bind_param()

    mysqli_stmt_execute($stmt);
    // 使用 mysqli_stmt_execute() 函數執行預處理語句 / Execute the prepared statement using mysqli_stmt_execute()

    mysqli_stmt_bind_result($stmt, $result_userID, $result_username, $result_password, $result_email, $result_manager);
    // 使用 mysqli_stmt_bind_result() 函數綁定結果變數 / Bind result variables using mysqli_stmt_bind_result()

    if(mysqli_stmt_fetch($stmt)){
    // 使用 mysqli_stmt_fetch() 函數獲取結果 / Fetch the result using mysqli_stmt_fetch()

    
    
        // 驗證密碼 / Verify password
        if($password == $result_password){
            // 密碼通過驗證 / Password verification passed
            session_start();
            // 把資料存入Session / Put the data into session
            $_SESSION["loggedin"] = true;
            $_SESSION["memberId"] = $result_userID;
            $_SESSION["memberName"] = $result_username;
            $_SESSION["memberEmail"] = $result_email;
            $_SESSION["memberManager"] = $result_manager;
            // 轉跳到會員頁面 / Redirect to member page
            header("location: ./welcome.php"); 
            exit;
        }else{
            // 密碼驗證失敗 / Password verification failed
            echo '<script>alert("密碼錯誤 ! \nIncorrect Password.");</script>';

        }
    }else{
        // 帳號不存在 / Account does not exist
        echo '<script>alert("帳號不存在 ! \nIncorrect Account .");</script>';
    }

    mysqli_stmt_close($stmt);
    // 使用 mysqli_stmt_close() 函數關閉預處理語句 / Close the prepared statement using mysqli_stmt_close()
}

// Close connection
mysqli_close($conn);
// 使用 mysqli_close() 函數關閉資料庫連線 / Close the database connection using mysqli_close()

	
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
            background-image:url("image/login.png");
            background-repeat: no-repeat;            /* 不重複 */
            background-position: top center;         /* 貼齊上方置中 */
            background-attachment: fixed;            /* 固定背景位置 */
            background-size: cover;                  /* 填滿整個畫面 */
        }
        html, body {
            height: 100%;
            margin: 0;
            overflow: auto;
        }

        #thing {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start; 
            padding-bottom: 150px; 
        }

        .b1 {
            width: 80px;
            height: 32px;
            border: none;
            border-radius: 10px;
            background-color: #3498db;
            color: aliceblue;
        }

        .b1:hover {
            width: 82px;
            height: 34px;
            border: none;
            border-radius: 10px;
            background-color: #21618c;
            color: aliceblue;
        }

        .b2 {
            width: 82px;
            height: 28px;
            border: none;
            border-radius: 6px;
            background-color: #a569bd;
            color: aliceblue;
        }

        .b2:hover {
            width: 84px;
            height: 30px;
            border: none;
            border-radius: 6px;
            background-color: #8e44ad;
            color: aliceblue;
        }

        .zi_box_1 {
            border: 8px solid #eee;
            margin: 2em auto;
            padding: 15px;
            position: relative;
            z-index: 0;
            border-radius: 40px;
            width: 420px;
            text-align: center;
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

        table {
            width: 100%;
            margin: 0 auto;
        }
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
<br><br><br>
<div class="container d-flex justify-content-center  vh-100" id="thing">
   
<div class="zi_box_1 shadow-sm" style="width: 24rem;">
        <div class="card-body">
            <h2 class="h2 mb-2 fw-bold">登入</h2>
            <br>
            <img src="image/login1.png" style="width: 200px;">
            <form action="login.php" method="post">
                <table style="margin: 0;">
                    <tr>
                        <td class="form-label" style="text-align: right; width: 30%;">帳號：</td>
                        <td>
                            <input style="height: 30px; width: 180px;" type="text" class="form-control" id="username" placeholder="Account" name="username">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 15px;"></td> <!-- 空白行 -->
                    </tr>
                    <tr>
                        <td class="form-label" style="text-align: right; width: 30%;">密碼：</td>
                        <td>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
                <br>
                <div class=" align-items-center">
                    <button class="b1 " type="submit">登入</button>
                </div>
            </form>
            <br>
            還沒有帳號？&nbsp;&nbsp;&nbsp;<button class="b2" type="button" onclick="location.href='singup.php'">我要註冊</button>
        </div>
    </div>
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
