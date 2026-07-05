<?php
session_start();


if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: index.php");
    exit();
}
//檢查網址有沒有帶著預約編號 (?ar_id=xxx)，沒有的話就中斷執行
if (!isset($_GET["ar_id"]) || empty($_GET["ar_id"])) {
    echo "無效的預約紀錄編號！";
    exit();
}


$ar_id = $_GET["ar_id"];
$login_student_id = $_SESSION["m_id"];


// 當學生按下「送出問卷」按鈕後，開始插入問卷結果的資料進資料庫
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $link = mysqli_connect("localhost", "root", "", "Consultation");
    mysqli_query($link, "SET NAMES utf8");


    // 先去satisfaction找出目前編號最新（最後面）的那一筆問卷編號
    $id_sql = "SELECT sat_id FROM satisfaction ORDER BY sat_id DESC LIMIT 1";
    $id_result = mysqli_query($link, $id_sql);
   
    if ($id_result && mysqli_num_rows($id_result) > 0) {
        $id_row = mysqli_fetch_assoc($id_result);
        $last_id = $id_row['sat_id'];// 假設取出來是 "s004"
        $num = (int)substr($last_id, 1) + 1;// substr("s004", 1) -> 把 's' 切掉變成 "004"，轉成數字 4，再加 1 變成 5
        $new_sat_id = "s" . str_pad($num, 3, "0", STR_PAD_LEFT);//把 5 補零成三位數 "005"，前面加上 s 變成 "s005"
    }


    // 如果資料庫裡完全沒有任何問卷紀錄，這就是第一筆，代號給 s001
    else {
        $new_sat_id = "s001";
    }


    $sql1 = "INSERT INTO satisfaction (sat_id, sat_date, m_id, ar_id) VALUES ('$new_sat_id', NOW(), '$login_student_id', '$ar_id')";
    $result1 = mysqli_query($link, $sql1);


    $questions = [
        '您對本次服務是否滿意？',
        '您是否願意再次使用本服務？',
        '服務流程是否清楚？',
        '服務人員態度是否良好？',
        '整體環境是否舒適？'
    ];


    $all_q_success = true;// 建立一個檢查變數，預設所有題目都寫入成功
   
    // 用 for 迴圈連續跑 5 次，把 5 道題目的分數分開存成 5 筆紀錄
    for ($i = 1; $i <= 5; $i++) {
        $score = (int)$_POST["q" . $i];// 取得前端傳來的 radio 分數 (1~5分)        
        $question = $questions[$i - 1]; // 根據迴圈次數，抓取陣列對應的題目文字 (陣列索引從 0 開始)
        $sql_q = "INSERT INTO sat_survey (sat_id, sat_no, sat_question, sat_score) VALUES ('$new_sat_id', $i, '$question', $score)";
        $result_q = mysqli_query($link, $sql_q);
       
        if (!$result_q) {
            $all_q_success = false;
        }
    }


    if ($result1 && $all_q_success) {
        $_SESSION['filled_surveys'][] = $ar_id;
        mysqli_close($link);
        echo "<script>alert('問卷繳交成功！謝謝您的填寫。'); window.location.href='student.php';</script>";
        exit();
    }


    else {
        echo "資料庫寫入失敗，請檢查欄位定義或資料格式。";
        mysqli_close($link);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>滿意度問卷調查</title>
    <style>
        body {
            font-family: "Microsoft JhengHei", Arial, sans-serif;
            background-color: #ffffff;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .survey-container {
            width: 100%;
            max-width: 600px;
            border: 2px solid #000000;
            padding: 30px;
        }
        h2 { border-bottom: 2px solid #627ff3; padding-bottom: 10px; }
        .question-group { margin-bottom: 25px; font-size: 18px; }
        .options { margin-top: 8px; display: flex; gap: 25px; }
        .options label { cursor: pointer; display: flex; align-items: center; gap: 5px; }
       
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 20px;
        }
       
        .btn-submit {
            font-size: 18px; padding: 10px 25px;
            background-color: #627ff3; color: #ffffff;
            border: none; cursor: pointer;
        }
       
        .btn-back {
            font-size: 18px; padding: 10px 25px;
            background-color: #ffffff; color: #000000;
            border: 1px solid #000000; text-decoration: none;
            display: inline-block; text-align: center;
        }
    </style>
</head>
<body>
    <div class="survey-container">
        <h2>滿意度問卷調查</h2>
        <p>預約編號：<?php echo $ar_id; ?></p>
        <form action="survey.php?ar_id=<?php echo $ar_id; ?>" method="post">  
            <div class="question-group">
                <p>1. 您對本次服務是否滿意？</p>
                <div class="options">
                    <label><input type="radio" name="q1" value="5" required> 5</label>
                    <label><input type="radio" name="q1" value="4"> 4</label>
                    <label><input type="radio" name="q1" value="3"> 3</label>
                    <label><input type="radio" name="q1" value="2"> 2</label>
                    <label><input type="radio" name="q1" value="1"> 1</label>
                </div>
            </div>
            <div class="question-group">
                <p>2. 您是否願意再次使用本服務？</p>
                <div class="options">
                    <label><input type="radio" name="q2" value="5" required> 5</label>
                    <label><input type="radio" name="q2" value="4"> 4</label>
                    <label><input type="radio" name="q2" value="3"> 3</label>
                    <label><input type="radio" name="q2" value="2"> 2</label>
                    <label><input type="radio" name="q2" value="1"> 1</label>
                </div>
            </div>
            <div class="question-group">
                <p>3. 服務流程是否清楚？</p>
                <div class="options">
                    <label><input type="radio" name="q3" value="5" required> 5</label>
                    <label><input type="radio" name="q3" value="4"> 4</label>
                    <label><input type="radio" name="q3" value="3"> 3</label>
                    <label><input type="radio" name="q3" value="2"> 2</label>
                    <label><input type="radio" name="q3" value="1"> 1</label>
                </div>
            </div>
            <div class="question-group">
                <p>4. 服務人員態度是否良好？</p>
                <div class="options">
                    <label><input type="radio" name="q4" value="5" required> 5</label>
                    <label><input type="radio" name="q4" value="4"> 4</label>
                    <label><input type="radio" name="q4" value="3"> 3</label>
                    <label><input type="radio" name="q4" value="2"> 2</label>
                    <label><input type="radio" name="q4" value="1"> 1</label>
                </div>
            </div>
            <div class="question-group">
                <p>5. 整體環境是否舒適？</p>
                <div class="options">
                    <label><input type="radio" name="q5" value="5" required> 5</label>
                    <label><input type="radio" name="q5" value="4"> 4</label>
                    <label><input type="radio" name="q5" value="3"> 3</label>
                    <label><input type="radio" name="q5" value="2"> 2</label>
                    <label><input type="radio" name="q5" value="1"> 1</label>
                </div>
            </div>
            <div class="action-buttons">
                <input type="submit" value="送出問卷" class="btn-submit">
                <a href="student.php" class="btn-back">回上一頁</a>
            </div>
        </form>
    </div>
</body>
</html>

