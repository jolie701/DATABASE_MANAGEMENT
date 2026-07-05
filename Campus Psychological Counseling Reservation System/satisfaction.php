<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>諮商滿意度調查系統</title>
</head>
<body bgcolor="#f4edd2" text="black">
<center>


<?php
session_start();
require_once 'readdb.php'; // 引入妳的資料庫連線檔案


// 建立資料庫連線
$link = readDb();
$current_page = basename($_SERVER['PHP_SELF']);
?>


<h2>諮商服務滿意度功能與結果總覽</h2>
<p>
    <a href="index.php">首頁</a> |
    <a href="admin.php">回上一頁</a>
</p>
<hr/>


<h3>【功能操作】</h3>
<form action="" method="get">
    <input type="submit" name="top3" value="🏆 顯示平均滿意度前三高的諮商師" style="font-weight: bold; padding: 10px 20px; cursor: pointer; background-color: #d8e2dc; border: 1px solid #999; border-radius: 5px;">
    <?php if (isset($_GET['top3'])): ?>
        <a href="<?=$current_page?>" style="margin-left: 10px; font-size: 14px; color: #555;">[隱藏排行榜]</a>
    <?php endif; ?>
</form>


<br/>


<?php
// 當按下按鈕時，計算並顯示前三名
if (isset($_GET['top3'])) {
    echo "<h3>📊 【榮譽榜：平均滿意度前三高諮商師】</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; text-align: center; width: 50%; border: 2px solid #b8c2bc;'>";
    echo "<tr bgcolor='#d8e2dc' style='font-weight: bold;'>";
    echo "<td width='20%'>名次</td>";
    echo "<td width='40%'>諮商師姓名 (c_name)</td>";
    echo "<td width='40%'>平均滿意度分數 (滿分 5.0)</td>";
    echo "</tr>";


    /* 排行 SQL：
       1. 透過 appointmentrecord (ar) 串接滿意度表 (sat) 與評分明細表 (survey)。
       2. 用 GROUP BY c.c_id 將每位諮商師的所有評分聚集。
       3. 用 AVG(survey.sat_score) 算出該諮商師得到的所有題目平均分。
       4. 依平均分從高到低排序 (DESC)，並用 LIMIT 3 只取前三名。
    */
    $sql_top3 = "
        SELECT
            c.c_name,
            ROUND(AVG(survey.sat_score), 2) AS avg_score
        FROM consultant c
        JOIN appointmentrecord ar ON c.c_id = ar.c_id
        JOIN satisfaction sat ON ar.m_id = sat.m_id
        JOIN sat_survey survey ON sat.sat_id = survey.sat_id
        GROUP BY c.c_id, c.c_name
        ORDER BY avg_score DESC
        LIMIT 3
    ";


    $res_top3 = mysqli_query($link, $sql_top3);
    $rank = 1;
   
    if (mysqli_num_rows($res_top3) > 0) {
        while ($top_row = mysqli_fetch_assoc($res_top3)) {
            // 前三名給予不同的底色點綴
            $bg = '#f1f5f3';
            if ($rank == 1) $bg = '#fff3cd'; // 金牌底色
            if ($rank == 2) $bg = '#e2e3e5'; // 銀牌底色
            if ($rank == 3) $bg = '#f8d7da'; // 銅牌底色


            echo "<tr bgcolor='{$bg}'>";
            echo "<td><b>第 " . $rank . " 名</b></td>";
            echo "<td><b>" . htmlspecialchars($top_row['c_name']) . "</b></td>";
            echo "<td style='color: red; font-weight: bold;'>" . htmlspecialchars($top_row['avg_score']) . " 分</td>";
            echo "</tr>";
            $rank++;
        }
    } else {
        echo "<tr><td colspan='3'><font color=gray>暫無評分數據，無法計算排行。</font></td></tr>";
    }
    echo "</table><br/><hr/><br/>";
}
?>


<h3>【每筆滿意度調查結果總覽（以預約紀錄 ar_id 為根據）】</h3>


<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; text-align: center; width: 95%;">
    <tr bgcolor="#d8e2dc" style="font-weight: bold;">
        <td>預約編號 (ar_id)</td>
        <td>諮商日期</td>
        <td>諮商師姓名</td>
        <td>學生學號</td>
        <td>問卷編號</td>
        <td>Q1 分數</td>
        <td>Q2 分數</td>
        <td>Q3 分數</td>
        <td>Q4 分數</td>
        <td>Q5 分數</td>
    </tr>


    <?php
    /* 每筆明細 SQL 關聯查詢：
       1. 從 appointmentrecord (ar) ，抓到 c_id 與 m_id
       2. JOIN consultant (c) 拿到諮商師名字
       3. JOIN satisfaction (sat) 透過 m_id 連接該學生的滿意度問卷紀錄
       4. 利用 5 次 LEFT JOIN 將同一個問卷的 1~5 題分數在同一橫列（Row）攤開顯示
    */
    $sql_report = "
        SELECT
            ar.ar_id,
            ar.ar_date,
            c.c_name,
            ar.m_id,
            sat.sat_id,
            q1.sat_score AS q1_score,
            q2.sat_score AS q2_score,
            q3.sat_score AS q3_score,
            q4.sat_score AS q4_score,
            q5.sat_score AS q5_score
        FROM appointmentrecord ar
        JOIN consultant c ON ar.c_id = c.c_id
        JOIN satisfaction sat ON ar.m_id = sat.m_id
        LEFT JOIN sat_survey q1 ON sat.sat_id = q1.sat_id AND q1.sat_no = 1
        LEFT JOIN sat_survey q2 ON sat.sat_id = q2.sat_id AND q2.sat_no = 2
        LEFT JOIN sat_survey q3 ON sat.sat_id = q3.sat_id AND q3.sat_no = 3
        LEFT JOIN sat_survey q4 ON sat.sat_id = q4.sat_id AND q4.sat_no = 4
        LEFT JOIN sat_survey q5 ON sat.sat_id = q5.sat_id AND q5.sat_no = 5
        ORDER BY ar.ar_id ASC, sat.sat_id ASC
    ";


    $res_report = mysqli_query($link, $sql_report);
    $total_rows = mysqli_num_rows($res_report);


    if ($total_rows > 0) {
        while ($row = mysqli_fetch_assoc($res_report)) {
            echo "<tr bgcolor='#f1f5f3'>";
            echo "<td><b>" . htmlspecialchars($row['ar_id']) . "</b></td>";
            echo "<td>" . htmlspecialchars($row['ar_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['c_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['m_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['sat_id']) . "</td>";
           
            // 顯示每一題的分數，如果沒有填寫則顯示 -
            echo "<td style='color:blue; font-weight:bold;'>" . ($row['q1_score'] ?? '-') . "</td>";
            echo "<td style='color:blue; font-weight:bold;'>" . ($row['q2_score'] ?? '-') . "</td>";
            echo "<td style='color:blue; font-weight:bold;'>" . ($row['q3_score'] ?? '-') . "</td>";
            echo "<td style='color:blue; font-weight:bold;'>" . ($row['q4_score'] ?? '-') . "</td>";
            echo "<td style='color:blue; font-weight:bold;'>" . ($row['q5_score'] ?? '-') . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10'><font color=gray>目前尚無對應的滿意度調查結果資料</font></td></tr>";
    }


    mysqli_close($link);
    ?>
</table>


</center>
</body>
</html>

