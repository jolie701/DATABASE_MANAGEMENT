<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: index.php");
    exit();
}

$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");

function q($l, $sql) {
    $res = mysqli_query($l, $sql);
    $d = array();
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $d[] = $row;
        }
    }
    return $d;
}

function e($s) {
    if(isset($s)) {
        return htmlspecialchars($s);
    }
    return '';
}

function getChinaWeekDay($dt) {
    $days = array('日','一','二','三','四','五','六');
    return $days[date('w', strtotime($dt))];
}

// ========== 1. 日期與週總覽資料處理 ==========
if(isset($_GET["date"])) {
    $start_date_input = $_GET["date"];
} else {
    $start_date_input = date("Y-m-d");
}

$ts = strtotime($start_date_input);
if(!$ts) {
    $start_date_input = date("Y-m-d");
    $ts = strtotime($start_date_input);
}

// 計算當週星期一
$w = date("w", $ts);
$offset = ($w == 0) ? 6 : $w - 1;
$start_ts = strtotime("-$offset days", $ts);
$w_start = date("Y-m-d", $start_ts);
$w_end = date("Y-m-d", strtotime("+4 days", $start_ts));

$week_dates = array();
for ($i = 0; $i < 5; $i++) {
    $week_dates[date("Y-m-d", strtotime("+$i days", $start_ts))] = strtotime("+$i days", $start_ts);
}

$all_periods = array("08:00-09:00", "09:00-10:00", "10:00-11:00", "11:00-12:00", "12:00-13:00", "13:00-14:00", "14:00-15:00", "15:00-16:00", "16:00-17:00");

$sql_calendar = "SELECT a.at_start, a.at_end, a.at_state, a.c_id, c.c_name
                 FROM availabletime a
                 JOIN consultant c ON a.c_id = c.c_id
                 WHERE DATE(a.at_start) BETWEEN '$w_start' AND '$w_end'";

$time_data = array();

foreach (q($link, $sql_calendar) as $r) {
    $dt = date('Y-m-d', strtotime($r["at_start"]));
    $p = date('H:i', strtotime($r["at_start"])) . '-' . date('H:i', strtotime($r["at_end"]));

    if (!in_array($p, $all_periods)) {
        $all_periods[] = $p;
        sort($all_periods);
    }

    $state_flag = ($r["at_state"] === '尚未被預約') ? "open" : "booked";
    $time_data[$dt][$p][$r["c_id"]] = array(
        "name" => $r["c_name"],
        "state" => $state_flag,
        "type" => "中文初談"
    );
}

// ========== 2. 動作處理與特定諮商師查詢 ==========
if (isset($_POST["delete_at_id"])) {
    mysqli_query($link, "DELETE FROM availabletime WHERE at_id = '" . mysqli_real_escape_string($link, $_POST["delete_at_id"]) . "'");
    $back_c_id = "";
    if(isset($_POST["target_c_id"])) { $back_c_id = $_POST["target_c_id"]; }
    mysqli_close($link);
    header("Location: consultant_manage.php?c_id=" . urlencode($back_c_id) . "#slot-section");
    exit();
}

if (isset($_POST["add_slot"]) && isset($_POST["target_c_id"])) {
    $c_id = mysqli_real_escape_string($link, $_POST["target_c_id"]);
    $new_date = mysqli_real_escape_string($link, $_POST["new_date"]);
    $new_period = mysqli_real_escape_string($link, $_POST["new_period"]);

    $max_res = mysqli_query($link, "SELECT MAX(CAST(SUBSTRING(at_id, 2) AS UNSIGNED)) FROM availabletime");
    $max_row = mysqli_fetch_row($max_res);
    $max_id = $max_row[0] ? $max_row[0] : 0;
    $at_id = 'T' . str_pad($max_id + 1, 5, '0', STR_PAD_LEFT);

    $times = explode('-', $new_period);
    $start_dt = $new_date . ' ' . $times[0] . ':00';
    $end_dt = $new_date . ' ' . $times[1] . ':00';

    mysqli_query($link, "INSERT INTO availabletime (c_id, at_id, at_start, at_end, at_state) VALUES ('$c_id', '$at_id', '$start_dt', '$end_dt', '尚未被預約')");
    mysqli_close($link);
    header("Location: consultant_manage.php?c_id=" . urlencode($_POST["target_c_id"]) . "#slot-section");
    exit();
}

$keyword = "";
if(isset($_POST["keyword"])) { $keyword = trim($_POST["keyword"]); }

$sql_c = "SELECT c.c_id, c.c_name, GROUP_CONCAT(DISTINCT ct.c_tel SEPARATOR '、') AS c_tel, GROUP_CONCAT(DISTINCT cf.c_field SEPARATOR '、') AS fields FROM consultant c LEFT JOIN consultant_tel ct ON c.c_id = ct.c_id LEFT JOIN consultant_field cf ON c.c_id = cf.c_id ";
if ($keyword != "") {
    $sql_c .= "WHERE c.c_name LIKE '%$keyword%' OR c.c_id LIKE '%$keyword%' ";
}
$sql_c .= "GROUP BY c.c_id ORDER BY c.c_id";

$consultant_list = q($link, $sql_c);

$selected_c_id = "";
if(isset($_GET["c_id"])) { $selected_c_id = $_GET["c_id"]; }
else if(isset($_POST["target_c_id"])) { $selected_c_id = $_POST["target_c_id"]; }

$selected_c_name = "";
$selected_c_tel = "";
$selected_fields = "";
$slot_list = array();
$appointment_list = array();

if ($selected_c_id != "") {
    $c_id_esc = mysqli_real_escape_string($link, $selected_c_id);
    $det = q($link, "SELECT c.c_name, GROUP_CONCAT(DISTINCT ct.c_tel SEPARATOR '、') AS c_tel, GROUP_CONCAT(DISTINCT cf.c_field SEPARATOR '、') AS fields FROM consultant c LEFT JOIN consultant_tel ct ON c.c_id = ct.c_id LEFT JOIN consultant_field cf ON c.c_id = cf.c_id WHERE c.c_id = '$c_id_esc' GROUP BY c.c_id");
   
    if (count($det) > 0) {
        $selected_c_name = $det[0]["c_name"];
        $selected_c_tel = $det[0]["c_tel"];
        $selected_fields = $det[0]["fields"] ? $det[0]["fields"] : "未設定";
    }

    $slot_list = q($link, "SELECT at_id, DATE(at_start) AS at_date, CONCAT(DATE_FORMAT(at_start, '%H:%i'), '-', DATE_FORMAT(at_end, '%H:%i')) AS at_period, at_state FROM availabletime WHERE c_id = '$c_id_esc' ORDER BY at_start DESC");
   
    $month_start = date("Y-m-01");
    $month_end = date("Y-m-t");
    $appointment_list = q($link, "SELECT m.m_name AS 學生姓名, ar.aa_period AS 預約時段, ar.ar_state AS 服務狀態, ar.ar_date AS 預約日期 FROM appointmentrecord ar LEFT JOIN member m ON ar.m_id = m.m_id WHERE ar.c_id = '$c_id_esc' AND ar.ar_date BETWEEN '$month_start' AND '$month_end' ORDER BY ar.ar_date DESC");
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>諮商師管理</title>
    <style>
        body { font-family: "Microsoft JhengHei", sans-serif; background-color: #f4edd2; padding: 20px; color: #2d2219; }
        .container { max-width: 1100px; margin: 0 auto; }
        .top-bar { background-color: #d8e2dc; padding: 12px 24px; border-radius: 6px; margin-bottom: 24px; overflow: hidden; }
        .admin-info { float: left; margin-top: 8px; }
        .btn { padding: 8px 16px; background: #fff; border: 1px solid #bbb; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; color: #2d2219; }
        .btn.right { float: right; }
        .btn-delete { background-color: #fde8d8; color: #a03010; border-color: #d4a574; }
        .page-title { text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 8px; }
        .page-subtitle { text-align: center; font-size: 13px; color: #666; margin-bottom: 24px; }
        .card { background: #fff; border: 1px solid #c8d4cc; padding: 20px; margin-bottom: 24px; }
        .section-title { font-size: 16px; font-weight: bold; border-left: 3px solid #CEDAD2; padding-left: 10px; }
        .form-row { margin-bottom: 10px; }
        input[type="text"], input[type="date"], select { height: 30px; padding: 0 5px; border: 1px solid #bbb; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; border: 1px solid #c0d0c8; text-align: left; }
        th { background-color: #d8e2dc; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; }
        .badge-success { background-color: #d4edda; color: #1a6b30; }
        .badge-warning { background-color: #fde8d8; color: #a03010; }
        .schedule-pill { display: block; padding: 6px; margin: 4px 0; background-color: #009ebd; color: #fff; text-align: center; text-decoration: none; font-size: 12px; }
        .pill-booked { background-color: #b93a4b; }
        .calendar-header { background-color: #d8e2dc; padding: 10px; text-align: center; border: 1px solid #c0d0c8; border-bottom: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div class="admin-info">登入者：<?php echo e(isset($_SESSION["m_name"]) ? $_SESSION["m_name"] : $_SESSION["m_id"]); ?></div>
            <a href="admin.php" class="btn right">回首頁</a>
        </div>

        <div class="page-title">諮商師管理</div>
        <div class="page-subtitle">管理諮商師的基本資料、聯絡電話、專長領域與排班排程</div>

        <div class="card">
            <div class="section-title">查詢諮商師</div>
            <form action="consultant_manage.php" method="post" style="margin-top: 10px;">
                <input type="text" name="keyword" placeholder="輸入姓名或編號" value="<?php echo e($keyword); ?>">
                <button type="submit" class="btn">查詢</button>
            </form>

            <?php if (count($consultant_list) > 0) { ?>
                <table>
                    <tr><th>編號</th><th>姓名</th><th>聯絡電話</th><th>專長領域</th><th>操作</th></tr>
                    <?php foreach ($consultant_list as $c) { ?>
                        <tr>
                            <td><?php echo e($c["c_id"]); ?></td>
                            <td><?php echo e($c["c_name"]); ?></td>
                            <td><?php echo e($c["c_tel"] ? $c["c_tel"] : "無"); ?></td>
                            <td><?php echo e($c["fields"] ? $c["fields"] : "未設定"); ?></td>
                            <td><a href="consultant_manage.php?c_id=<?php echo urlencode($c["c_id"]); ?>#slot-section" class="btn">管理時段</a></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>請輸入查詢條件或查無資料。</p>
            <?php } ?>
        </div>

        <a id="calendar-section"></a>
        <div class="section-title">預約班表總覽</div>
        <div class="calendar-header">
            可預約區間：<?php echo date("Y/m/d", $start_ts); ?> ~ <?php echo $w_end; ?><br><br>
            <button class="btn" onclick="goWeek(-1)">上週</button>
            <input type="date" id="dateInput" value="<?php echo e($start_date_input); ?>" onchange="goToDate()">
            <button class="btn" onclick="goWeek(1)">下週</button>
        </div>
        <table>
            <tr>
                <th class="text-center">時段</th>
                <?php foreach($week_dates as $dt => $ts_val) { ?>
                    <th class="text-center"><?php echo date("Y/m/d", $ts_val); ?><br>(週<?php echo getChinaWeekDay($dt); ?>)</th>
                <?php } ?>
            </tr>
            <?php foreach ($all_periods as $p) { ?>
                <tr>
                    <td class="text-center" style="background-color: #f8fcf6; font-weight: bold;"><?php echo e($p); ?></td>
                    <?php if ($p === "12:00-13:00") { ?>
                        <td colspan="5" class="text-center" style="background-color: #e8dfc5;">-- 中午休息時間 --</td>
                    <?php } else { ?>
                        <?php foreach ($week_dates as $dt => $ts_val) { ?>
                            <td style="vertical-align: top; width: 18%;">
                                <?php
                                if (isset($time_data[$dt][$p])) {
                                    foreach ($time_data[$dt][$p] as $cid => $slot) {
                                        if ($slot["state"] === "open") {
                                            echo '<a href="#" class="schedule-pill">● '.e($slot["name"]).' '.e($slot["type"]).'</a>';
                                        } else {
                                            echo '<div class="schedule-pill pill-booked">'.e($slot["name"]).'(已預約)</div>';
                                        }
                                    }
                                }
                                ?>
                            </td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <br>

        <a id="slot-section"></a>
        <div class="card">
            <div class="section-title">特定諮商師 排班排程管理</div>
            <?php if ($selected_c_id != "") { ?>
                <p>目前管理：<strong><?php echo e($selected_c_name); ?></strong>（<?php echo e($selected_c_id); ?>）</p>
                <p>電話：<?php echo e($selected_c_tel ? $selected_c_tel : "無"); ?> | 專長：<?php echo e($selected_fields); ?></p>
                <hr>
               
                <form action="consultant_manage.php?c_id=<?php echo urlencode($selected_c_id); ?>#slot-section" method="post">
                    <input type="hidden" name="target_c_id" value="<?php echo e($selected_c_id); ?>">
                    <input type="hidden" name="add_slot" value="1">
                    <div class="form-row">
                        新增時段：
                        <input type="date" name="new_date" required>
                        <select name="new_period" required>
                            <option value="">-- 請選擇時段 --</option>
                            <?php
                            $std_arr = array("08:00-09:00", "09:00-10:00", "10:00-11:00", "11:00-12:00", "13:00-14:00", "14:00-15:00", "15:00-16:00", "16:00-17:00");
                            foreach($std_arr as $std_p) {
                                echo '<option value="'.$std_p.'">'.$std_p.'</option>';
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn">新增</button>
                    </div>
                </form>

                <p><strong>可預約時段列表（共 <?php echo count($slot_list); ?> 個）</strong></p>
                <?php if (count($slot_list) > 0) { ?>
                    <table>
                        <tr><th>日期</th><th>時段</th><th>狀態</th><th>操作</th></tr>
                        <?php foreach ($slot_list as $slot) { ?>
                            <tr>
                                <td><?php echo e($slot["at_date"]); ?></td>
                                <td><?php echo e($slot["at_period"]); ?></td>
                                <td>
                                    <?php if($slot["at_state"] === '尚未被預約') { ?>
                                        <span class="badge badge-success"><?php echo e($slot["at_state"]); ?></span>
                                    <?php } else { ?>
                                        <span class="badge badge-warning"><?php echo e($slot["at_state"]); ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <form action="consultant_manage.php?c_id=<?php echo urlencode($selected_c_id); ?>#slot-section" method="post" onsubmit="return confirm('確定刪除？');">
                                        <input type="hidden" name="delete_at_id" value="<?php echo e($slot["at_id"]); ?>">
                                        <input type="hidden" name="target_c_id" value="<?php echo e($selected_c_id); ?>">
                                        <button type="submit" class="btn btn-delete">刪除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { echo "<p>尚無時段</p>"; } ?>

                <br><p><strong>本月預約安排</strong></p>
                <?php if (count($appointment_list) > 0) { ?>
                    <table>
                        <tr><th>預約日期</th><th>學生姓名</th><th>預約時段</th><th>服務狀態</th></tr>
                        <?php foreach ($appointment_list as $appt) { ?>
                            <tr>
                                <td><?php echo e($appt["預約日期"]); ?></td>
                                <td><?php echo e($appt["學生姓名"]); ?></td>
                                <td><?php echo e($appt["預約時段"]); ?></td>
                                <td><?php echo e($appt["服務狀態"]); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { echo "<p>本月無預約</p>"; } ?>
            <?php } else { ?>
                <p>請先到上方清單點選「管理時段」</p>
            <?php } ?>
        </div>
    </div>

    <script>
        function goWeek(offset) {
            var input = document.getElementById('dateInput');
            var d = new Date(input.value);
            d.setDate(d.getDate() + offset * 7);
            var month = '' + (d.getMonth() + 1), day = '' + d.getDate(), year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            input.value = [year, month, day].join('-');
            goToDate();
        }
        function goToDate() {
            var input = document.getElementById('dateInput');
            var urlParams = new URLSearchParams(window.location.search);
            var cid = urlParams.get('c_id') || "";
            var url = "consultant_manage.php?date=" + input.value;
            if (cid !== "") { url += "&c_id=" + encodeURIComponent(cid); }
            url += "#calendar-section";
            window.location.href = url;
        }
    </script>
</body>
</html>




