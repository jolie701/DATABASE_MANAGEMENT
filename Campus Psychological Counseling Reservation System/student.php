<?php
session_start();


if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: index.php");
    exit();
}


$login_student_id = $_SESSION["m_id"];
$login_student_name = isset($_SESSION["m_name"]) ? $_SESSION["m_name"] : $login_student_id;


$current_service = "";
$appointment_list = [];
$popular_list = [];


$link = mysqli_connect("localhost", "root", "", "Consultation");
if (!$link) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}
mysqli_query($link, "SET NAMES utf8");




// 處理送出預約
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_at_id'])) {
    $at_id = $_POST['selected_at_id'];
    $summary = $_POST['aa_summary'];
    $aa_type = $_POST['aa_type'];
    // 產生隨機申請單編號
    $aa_id = 'aa' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
   
    $sql_time = "SELECT at_start FROM availabletime WHERE at_id = ?";
    $stmt_time = mysqli_prepare($link, $sql_time);
    mysqli_stmt_bind_param($stmt_time, "s", $at_id);
    mysqli_stmt_execute($stmt_time);
    $result_time = mysqli_stmt_get_result($stmt_time);
   
    $periods_map = [
        8 => '第一節課', 9 => '第二節課', 10 => '第三節課', 11 => '第四節課',
        13 => '第五節課', 14 => '第六節課', 15 => '第七節課', 16 => '第八節課'
    ];


    if ($row_time = mysqli_fetch_assoc($result_time)) {
        $at_start = $row_time['at_start'];
        $aa_date = date("Y-m-d", strtotime($at_start));
        $hour = (int)date("H", strtotime($at_start));
        $aa_period = isset($periods_map[$hour]) ? $periods_map[$hour] : '未定義';
       
        $sql_insert = "INSERT INTO appointmentapply (aa_id, aa_type, aa_summary, aa_date, aa_period, m_id)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($link, $sql_insert);
   
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, "ssssss", $aa_id, $aa_type, $summary, $aa_date, $aa_period, $login_student_id);
            if (mysqli_stmt_execute($stmt_insert)) {
                echo "<script>
                        alert('預約申請已成功送出！');
                        window.location.href = 'student.php';
                      </script>";
                exit;
            } else {
                echo "<script>alert('寫入失敗：" . mysqli_error($link) . "');</script>";
            }
        } else {
            echo "<script>alert('SQL 語法準備失敗：" . mysqli_error($link) . "');</script>";
        }
    } else {
        echo "<script>alert('找不到該時段資訊，請重新選擇。');</script>";
    }
}
// 處理服務選項切換 (s1, s2, s3)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["service"])) {
    $current_service = $_POST["service"];


    // 查詢預約紀錄
    if ($current_service == "s2") {
        $sql = "SELECT
                    ar.ar_id,
                    ar.ar_date,
                    c.c_name,
                    ar.aa_period,
                    ar.ar_state,
                    ar.ar_result
                FROM
                    appointmentrecord ar,
                    consultant c
                WHERE
                    ar.c_id = c.c_id                          
                    AND ar.m_id = '$login_student_id'          
                ORDER BY
                    ar.ar_date DESC";
        $result = mysqli_query($link, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $appointment_list[] = $row;
            }
        }
    }


    //查詢熱門時段
    if ($current_service == "s3") {
        $sql_pop = "SELECT aa_period, COUNT(*) AS count
                    FROM appointmentrecord
                    WHERE ar_date >= DATE_SUB('2026-05-01', INTERVAL 1 MONTH)
                    GROUP BY aa_period
                    ORDER BY count DESC LIMIT 5";
        $res_pop = mysqli_query($link, $sql_pop);
        if ($res_pop && mysqli_num_rows($res_pop) > 0) {
            while ($row = mysqli_fetch_assoc($res_pop)) {
                $popular_list[] = $row;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>學生功能主頁</title>
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
            position: relative;
        }
        .login-student-msg {
            font-size: 18px;
            font-weight: bold;
            color: #333333;
            margin-bottom: 15px;
            text-align: left;
        }
        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;        
            margin-bottom: 25px;
        }
        select, .btn-submit, .btn-home, .btn-action {
            font-size: 18px;
            padding: 8px 15px;
            border: 1px solid #000000;
            background-color: #ffffff;
        }
        .btn-submit, .btn-home, .btn-action {
            cursor: pointer;
            text-decoration: none;
            color: #000000;
            display: inline-block;
            text-align: center;
        }
        .btn-action:hover { background-color: #e6e6e6; }
        .separator {
            border: none;
            border-top: 2px solid #acacac;
            margin: 30px 0;
        }
        .record-table, .calendar-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            table-layout: fixed;
        }
        .record-table th, .record-table td, .calendar-table th, .calendar-table td {
            border: 1px solid #000000;
            padding: 12px;
            text-align: center;
            font-size: 18px;
            word-wrap: break-word;
        }
        .record-table th, .calendar-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .record-table td {
            background-color: #ffffff;
            font-weight: bold;
        }
        .survey-link {
            color: #627ff3;
            text-decoration: underline;
        }
        .not-open {
            color: #acacac;
        }
        .already-filled {
            color: #008800;
            font-weight: bold;
        }
        .period-header { background-color: #f2f2f2; font-weight: bold; width: 120px; }
        .slot-cell { background-color: #ffffff; transition: background 0.2s; }
        .slot-cell:hover { background-color: #fff9d4; }
        .empty-cell { background-color: #f9f9f9; color: #ccc; font-size: 14px; }
        .radio-item { transform: scale(1.2); margin-bottom: 5px; }
        .summary-box { background-color: #ffffff; padding: 25px; border: 1px solid #000000; margin-top: 25px; }
        textarea { width: 100%; padding: 10px; border: 1px solid #000000; box-sizing: border-box; font-family: inherit; font-size: 16px; margin-top: 10px; }
        .bottom-right-action { text-align: right; margin-top: 20px; }
    </style>
   
    <script>
        function goToStep2() {
            var radios = document.getElementsByClassName('radio-item');
            var selectedRadio = null;
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) { selectedRadio = radios[i]; break; }
            }
            if (!selectedRadio) {
                alert("請先在下方的表格中，勾選一個您想預約的時段格子！");
                return;
            }
            var fieldsString = selectedRadio.getAttribute('data-fields');
            var fieldsArray = fieldsString.split('、');
            var selectDropdown = document.getElementById('dynamic_aa_type');
            selectDropdown.innerHTML = '<option value="" disabled selected>-- 請選擇一個領域 --</option>';
            for (var j = 0; j < fieldsArray.length; j++) {
                var fieldName = fieldsArray[j].trim();
                if (fieldName !== "") {
                    var option = document.createElement('option');
                    option.value = fieldName;
                    option.text = fieldName;
                    selectDropdown.appendChild(option);
                }
            }
            document.getElementById('step1_calendar').style.display = 'none';
            document.getElementById('step2_summary').style.display = 'block';
        }


        function backToStep1() {
            document.getElementById('step2_summary').style.display = 'none';
            document.getElementById('step1_calendar').style.display = 'block';
        }


        function changeWeek() {
            var radios = document.getElementsByName('selected_at_id');
            for(var i = 0; i < radios.length; i++) {
                radios[i].checked = false;
            }
            document.getElementById('appointmentForm').submit();
        }
    </script>
</head>
<body>
    <div class="page-container">
        <div class="login-student-msg">
            登入者：<?php echo htmlspecialchars($login_student_name); ?>
        </div>
        <div class="top-section">
            <form action="student.php" method="post" id="serviceForm">
                <select name="service">
                    <option value="">請選擇服務項目</option>
                    <option value="s1" <?php if($current_service == 's1') echo 'selected'; ?>>申請諮商預約</option>
                    <option value="s2" <?php if($current_service == 's2') echo 'selected'; ?>>查詢預約紀錄</option>
                    <option value="s3" <?php if($current_service == 's3') echo 'selected'; ?>>查詢預約熱門時段</option>
                </select>
                <input type="submit" value="確定" class="btn-submit">
            </form>
            <a href="index.php" class="btn-home">回首頁</a>
        </div>
        <hr class="separator">


        <?php


        // 申請諮商預約
        if ($current_service == "s1") {
            echo "<h1>預約申請</h1>";
           
            // 接收週次參數
            $week_offset = isset($_POST['week_offset']) ? (int)$_POST['week_offset'] : 0;
            $db_offset = $week_offset * 7;
            ?>
            <form action="student.php" method="POST" id="appointmentForm">
                <input type="hidden" name="service" value="s1">
               
                <div id="step1_calendar">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3>可預約時段行事曆</h3>
                        <div>
                            <label for="week_offset" style="font-size: 16px; font-weight: bold;">選擇查詢週次：</label>
                            <select name="week_offset" id="week_offset" onchange="changeWeek()" style="font-size: 16px; padding: 5px;">
                                <option value="0" <?php if($week_offset == 0) echo 'selected'; ?>>最近一週</option>
                                <option value="1" <?php if($week_offset == 1) echo 'selected'; ?>>下一週</option>
                                <option value="2" <?php if($week_offset == 2) echo 'selected'; ?>>下下週</option>
                            </select>
                        </div>
                    </div>


                    <?php
                    $weekdays = ['Mon' => '星期一', 'Tue' => '星期二', 'Wed' => '星期三', 'Thu' => '星期四', 'Fri' => '星期五', 'Sat' => '星期六', 'Sun' => '星期日'];
                    $periods = [
                        1 => ['name' => '第一節課', 'hour' => 8],  2 => ['name' => '第二節課', 'hour' => 9],
                        3 => ['name' => '第三節課', 'hour' => 10], 4 => ['name' => '第四節課', 'hour' => 11],
                        5 => ['name' => '第五節課', 'hour' => 13], 6 => ['name' => '第六節課', 'hour' => 14],
                        7 => ['name' => '第七節課', 'hour' => 15], 8 => ['name' => '第八節課', 'hour' => 16],
                    ];


                    $sql_distinct_dates = "SELECT DISTINCT DATE(at_start) as distinct_date
                                           FROM availabletime
                                           WHERE at_state = '尚未被預約' AND at_start >= CURDATE()
                                           ORDER BY distinct_date ASC
                                           LIMIT 7 OFFSET $db_offset";
                    $res_dates = mysqli_query($link, $sql_distinct_dates);
                   
                    if (!$res_dates) {
                        echo "<p style='color:red; font-weight:bold;'>SQL 查詢失敗：" . mysqli_error($link) . "</p>";
                        $date_list = [];
                    } else {
                        $date_list = [];
                        while($d_row = mysqli_fetch_assoc($res_dates)) {
                            $date_list[] = $d_row['distinct_date'];
                        }
                    }


                    if (count($date_list) > 0) {
                        $sql_all_slots = "SELECT at.at_id, DATE(at.at_start) as at_date, HOUR(at.at_start) as at_hour,
                                          GROUP_CONCAT(cf.c_field SEPARATOR '、') as fields
                                          FROM availabletime at
                                          JOIN consultant c ON at.c_id = c.c_id
                                          JOIN consultant_field cf ON c.c_id = cf.c_id
                                          WHERE at.at_state = '尚未被預約' AND at.at_start >= CURDATE()
                                          GROUP BY at.at_id, at.at_start";
                        $res_slots = mysqli_query($link, $sql_all_slots);
                       
                        $grid = [];
                        if ($res_slots) {
                            while($slot_row = mysqli_fetch_assoc($res_slots)) {
                                $d = $slot_row['at_date'];
                                $h = (int)$slot_row['at_hour'];
                                $grid[$d][$h] = ['at_id' => $slot_row['at_id'], 'fields' => $slot_row['fields']];
                            }
                        }
                    ?>
                        <table class="calendar-table">
                            <tr>
                                <th class="period-header">節次 / 日期</th>
                                <?php foreach($date_list as $date_str):
                                    $w_eng = date("D", strtotime($date_str));
                                    $w_chi = isset($weekdays[$w_eng]) ? $weekdays[$w_eng] : $w_eng;
                                ?>
                                    <th><?php echo date("m/d", strtotime($date_str)) . "<br>" . $w_chi; ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach($periods as $p_num => $p_info): ?>
                                <tr>
                                    <td class="period-header"><?php echo $p_info['name']; ?></td>
                                    <?php foreach($date_list as $date_str):
                                        $target_hour = $p_info['hour'];
                                        if(isset($grid[$date_str][$target_hour])) {
                                            $cell_data = $grid[$date_str][$target_hour];
                                            ?>
                                            <td class="slot-cell">
                                                <label style="cursor: pointer; display: block; width: 100%; height: 100%;">
                                                    <input type="radio" name="selected_at_id" value="<?php echo $cell_data['at_id']; ?>" data-fields="<?php echo htmlspecialchars($cell_data['fields']); ?>" class="radio-item"><br>
                                                    <span style="font-size: 15px; font-weight: bold; color: #333;">
                                                        <?php echo $cell_data['fields']; ?>
                                                    </span>
                                                </label>
                                            </td>
                                            <?php
                                        } else {
                                            echo "<td class='empty-cell'>-</td>";
                                        }
                                    endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <div class="bottom-right-action">
                            <button type="button" class="btn-action" onclick="goToStep2()">下一步</button>
                        </div>
                    <?php
                    } else {
                        echo "<div style='background-color: #fff; padding: 25px; border: 2px solid #000; margin-top: 25px;'>";
                        echo "<p style='color: #d9534f; font-size: 20px; font-weight: bold; margin-top:0;'> 查無開放時段</p>";
                        echo "<p style='font-size: 16px; line-height: 1.6; color: #333;'><strong>提醒：</strong>您選擇的這週目前沒有任何可以預約的諮商時段喔！請嘗試在右上角切換至其他週次查看。</p>";
                        echo "</div>";
                    }
                    ?>
                </div>


                <div id="step2_summary" style="display: none;">
                    <div class="summary-box">
                        <h3>詳細敘述內容 (第二步)</h3>
                        <p><strong>1. 請選擇您本次預約的主要諮商領域：</strong></p>
                        <select id="dynamic_aa_type" name="aa_type" required style="width: 100%; margin-bottom: 15px;">
                            <option value="" disabled selected>-- 請選擇一個領域 --</option>
                        </select>


                        <p><strong>2. 請進一步說明詳細敘述內容：</strong></p>
                        <textarea name="aa_summary" rows="6" required placeholder="請詳細輸入您的困擾主旨..."></textarea>
                       
                        <div class="bottom-right-action" style="display: flex; justify-content: space-between;">
                            <button type="button" class="btn-action" onclick="backToStep1()">上一步</button>
                            <button type="submit" class="btn-action" style="background-color: #000; color: #fff;">確定 送出預約</button>
                        </div>
                    </div>
                </div>
            </form>
            <?php
        }
       
        // 查詢預約紀錄
        elseif ($current_service == "s2") {
            echo "<h1>預約紀錄</h1>";


            if (!empty($appointment_list)) {
                echo '<table class="record-table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>預約編號</th><th>預約日期</th><th>諮商師姓名</th><th>預約時段</th><th>預約狀態</th><th>通知結果</th><th>滿意度問卷</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                for ($i = 0; $i < count($appointment_list); $i++) {
                    $row = $appointment_list[$i];
                   
                    echo '<tr>';
                    echo '<td>' . $row["ar_id"] . '</td>';
                    echo '<td>' . $row["ar_date"] . '</td>';
                    echo '<td>' . $row["c_name"] . '</td>';
                    echo '<td>' . $row["aa_period"] . '</td>';
                    echo '<td>' . $row["ar_state"] . '</td>';
                    echo '<td>' . $row["ar_result"] . '</td>';
                    echo '<td>';
                   
                    if ($row["ar_state"] == "結案/暫停") {
                        $sql_check = "SELECT * FROM satisfaction WHERE m_id = '$login_student_id'";
                        $result_check = mysqli_query($link, $sql_check);


                        if ($result_check && mysqli_num_rows($result_check) > 0) {
                            echo '<span class="already-filled">已填寫</span>';
                        } else {
                            echo '<a href="survey.php?ar_id=' . $row["ar_id"] . '" class="survey-link">填寫滿意度調查</a>';
                        }
                    } else {
                        echo '<span class="not-open">未開放</span>';
                    }


                    echo '</td>';
                    echo '</tr>';
                }


                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p style="font-size: 20px; text-align: center;">目前暫無任何預約紀錄。</p>';
            }
        }


        // 查詢熱門預約時段
        elseif ($current_service == "s3") {
            echo "<h1>熱門預約時段 (近一個月)</h1>";
            if (!empty($popular_list)) {
                echo '<table class="record-table">';
                echo '<thead><tr><th>排名</th><th>預約時段 (節次)</th><th>預約次數</th></tr></thead>';
                echo '<tbody>';
                $rank = 1;
                foreach ($popular_list as $row) {
                    echo '<tr>';
                    echo '<td>' . $rank . '</td>';
                    echo '<td>' . $row["aa_period"] . '</td>';
                    echo '<td>' . $row["count"] . ' 次</td>';
                    echo '</tr>';
                    $rank++;
                }
                echo '</tbody></table>';
            } else {
                echo '<p style="font-size: 20px; text-align: center;">近一個月暫無熱門時段紀錄。</p>';
            }
        }


        // 預設畫面：沒選擇任何服務項目
        else {
            echo '<p style="font-size: 20px; color: #acacac; text-align: center;">請選擇服務項目，並點擊確定。</p>';
        }


        // 最後統一關閉資料庫連線
        if ($link) {
            mysqli_close($link);
        }
        ?>


    </div>
</body>
</html>



