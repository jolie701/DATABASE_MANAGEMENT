<?php
session_start();


$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");


//處理登入
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_account"])) {
    $user = $_POST["user_account"];
    $pass = $_POST["user_password"];


    $sql_login = "SELECT * FROM member
            WHERE m_id='$user' AND m_password='$pass'";


    $result = mysqli_query($link, $sql_login);


    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["login_session"] = true;
        $_SESSION["m_id"] = $row["m_id"];
        $_SESSION["m_name"] = $row["m_name"];


        //擷取帳號的第一個字，之後用來判斷身分
        $first_letter = substr($user, 0, 1);


        //若帳號開頭的英文字母為A，此使用者為行政人員
        if ($first_letter == 'A' || $first_letter == 'a') {
            mysqli_close($link);
            header("Location: admin.php");
            exit();
        }


      
        else  {
            mysqli_close($link);
            header("Location: student.php");
            exit();
        }
    }


    else {
        mysqli_close($link);
        header("Location: index.php?msg=1");
        exit();
    }
}


//抓取公告
$sql_news = "SELECT r_id, r_ann, r_service FROM resource
             WHERE r_ann IS NOT NULL AND r_ann != ''
               AND r_service IS NOT NULL AND r_service != ''
             ORDER BY r_id DESC LIMIT 2";


$result_news = mysqli_query($link, $sql_news);
$news_list = [];// 用陣列來儲存公告


if ($result_news && mysqli_num_rows($result_news) > 0) {
    while ($row = mysqli_fetch_assoc($result_news)) {
        $news_list[] = $row;
    }
}


mysqli_close($link);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>心理諮商預約管理平台</title>
    <style>
        body {
            font-family: "Microsoft JhengHei", Arial, sans-serif;
            background-color: #f4edd2;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }


        .page-container {
            width: 100%;
            max-width: 1000px;
            box-sizing: border-box;
        }


        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;        
            margin-bottom: 25px;
        }


        .nav-buttons {
            display: flex;
            gap: 40px;          
        }


        .btn {
            display: inline-block;
            background-color: #000000;
            color: #ffffff;
            text-decoration: none;
            font-size: 24px;
            padding: 15px 35px;
            border-radius: 20px;      
            text-align: center;
        }


        .login-box {
            border: 2px solid #000000;
            padding: 20px;
            width: 320px;
            background-color: #ffffff;
        }


        .input-group {
            font-size: 18px;
            margin-bottom: 10px;
        }


        .input-group input {
            width: 180px;
            font-size: 16px;
            padding: 2px;
        }


        .separator {
            border: none;
            border-top: 2px solid #acacac;
            margin: 30px 0;
        }


       
        .announcement-container {
            margin-bottom: 25px;
            text-align: left;
        }


        .ann-title {
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 8px;
        }


        .ann-content {
            font-size: 18px;
            line-height: 1.6;
            color: #333333;
            background-color: #ffffff;
            padding: 15px;
            border-left: 4px solid #000000;
        }


        .working-time {
            font-size: 20px;
            margin-top: 30px;
            color: #000000;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="top-section">
            <div class="nav-buttons">
                <a href="resources.php" class="btn">資源連結</a>
                <a href="faq.php" class="btn">常見問答</a>
            </div>
            <div class="login-box" style="text-align: center;">
                <?php
                if (isset($_GET["msg"]) && $_GET["msg"] == "1") {
                    echo "<p style='color:red; font-weight:bold; margin-top:0;'>使用者名稱或密碼錯誤！</p>";
                }
                ?>
                <form action="index.php" method="post">
                    <div class="input-group" style="text-align: left;">
                        帳號：<input type="text" name="user_account" required>
                        <br><br>
                        密碼：<input type="password" name="user_password" required>
                    </div>
                    <input type="submit" value="登入">
                </form>
            </div>
        </div>
        <h1>最新公告</h1>
        <?php
        if (!empty($news_list)) {
            for ($i = 0; $i < count($news_list); $i++) {
                $news = $news_list[$i];


                echo '<hr class="separator">';
                echo '<div class="announcement-container">';
                echo '<div class="ann-title">' . $news["r_ann"] . '</div>';
                echo '<div class="ann-content">' . nl2br($news["r_service"]) . '</div>';
                echo '</div>';
            }
        }


        else {
            echo '<hr class="separator">';
            echo '<div class="no-announcement">目前暫無最新公告</div>';
        }
        ?>


        <hr class="separator">
        <div class="working-time">
            開放時段：每周一到五 8：00至17：00，如遇國定假日會休診
        </div>
    </div>
</body>
</html>
