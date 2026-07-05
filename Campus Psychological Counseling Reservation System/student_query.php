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
    if(isset($s)) { return htmlspecialchars($s); }
    return '';
}

// --- 接收參數 ---
$keyword = "";
if(isset($_POST["keyword"])) { $keyword = trim($_POST["keyword"]); }

$dept_filter = "";
if(isset($_POST["dept_filter"])) { $dept_filter = trim($_POST["dept_filter"]); }

$tab = "with_record";
if(isset($_POST["tab"])) { $tab = $_POST["tab"]; }

// 取得學系列表
$dept_res = mysqli_query($link, "SELECT DISTINCT s_dept FROM student ORDER BY s_dept");
$dept_list = array();
while($row = mysqli_fetch_assoc($dept_res)) {
    $dept_list[] = $row["s_dept"];
}

$kw_esc = mysqli_real_escape_string($link, $keyword);
$dp_esc = mysqli_real_escape_string($link, $dept_filter);

$cond = "";
if ($keyword != "") {
    $cond .= " AND (m.m_name LIKE '%$kw_esc%' OR s.m_id LIKE '%$kw_esc%')";
}
if ($dept_filter != "") {
    $cond .= " AND s.s_dept = '$dp_esc'";
}

$sql_with = "SELECT s.m_id, m.m_name, s.s_dept,
                    GROUP_CONCAT(DISTINCT mt.m_tel SEPARATOR '、') AS m_phone,
                    GROUP_CONCAT(DISTINCT se.s_emergency SEPARATOR '、') AS s_emergency,
                    COUNT(DISTINCT cr.cr_id) AS consultation_count,
                    COUNT(DISTINCT CASE WHEN ar.ar_state = '預約成功' AND cr_check.cr_id IS NULL THEN ar.ar_id END) AS violation_count
             FROM student s
             JOIN member m ON s.m_id = m.m_id
             LEFT JOIN member_tel mt ON m.m_id = mt.m_id
             LEFT JOIN student_emergency se ON s.m_id = se.m_id
             LEFT JOIN consultationrecord cr ON s.m_id = cr.m_id
             LEFT JOIN appointmentrecord ar ON s.m_id = ar.m_id
             LEFT JOIN consultationrecord cr_check ON cr_check.ar_id = ar.ar_id
             WHERE cr.cr_id IS NOT NULL $cond
             GROUP BY s.m_id, m.m_name, s.s_dept ORDER BY m.m_name";
$with_record_list = q($link, $sql_with);

$sql_without = "SELECT s.m_id, m.m_name, s.s_dept,
                       GROUP_CONCAT(DISTINCT mt.m_tel SEPARATOR '、') AS m_phone,
                       GROUP_CONCAT(DISTINCT se.s_emergency SEPARATOR '、') AS s_emergency,
                       0 AS consultation_count, 0 AS violation_count
                FROM student s
                JOIN member m ON s.m_id = m.m_id
                LEFT JOIN member_tel mt ON m.m_id = mt.m_id
                LEFT JOIN student_emergency se ON s.m_id = se.m_id
                WHERE NOT EXISTS (SELECT 1 FROM consultationrecord cr WHERE cr.m_id = m.m_id) $cond
                GROUP BY s.m_id, m.m_name, s.s_dept ORDER BY m.m_name";
$without_record_list = q($link, $sql_without);

if ($tab === "with_record") {
    $data_list = $with_record_list;
} else {
    $data_list = $without_record_list;
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>查詢學生</title>
    <style>
        body { font-family: "Microsoft JhengHei", sans-serif; background-color: #f4edd2; padding: 20px; color: #2d2219; }
        .container { max-width: 1200px; margin: 0 auto; }
        .top-bar { background-color: #d8e2dc; padding: 12px 24px; border-radius: 6px; margin-bottom: 24px; overflow: hidden; }
        .admin-info { float: left; font-size: 14px; margin-top: 8px; }
        .btn { padding: 8px 16px; background: #fff; border: 1px solid #bbb; border-radius: 6px; cursor: pointer; text-decoration: none; color: #2d2219; display: inline-block; }
        .btn.right { float: right; }
        .card { background: #fff; border: 1px solid #c8d4cc; padding: 20px; margin-bottom: 24px; }
        .tab { padding: 10px 16px; font-size: 14px; cursor: pointer; background: #eee; border: 1px solid #ccc; }
        .tab.active { background-color: #eef5f0; font-weight: bold; border-bottom: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
        th, td { padding: 8px; border: 1px solid #c0d0c8; text-align: left; }
        th { background-color: #d8e2dc; font-weight: bold; }
        .badge-ok { background-color: #d4edda; color: #1a6b30; padding: 4px; border-radius: 4px; }
        .badge-warn { background-color: #fde8d8; color: #a03010; padding: 4px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div class="admin-info">登入者：<?php echo e(isset($_SESSION["m_name"]) ? $_SESSION["m_name"] : $_SESSION["m_id"]); ?></div>
            <a href="admin.php" class="btn right">回首頁</a>
        </div>

        <h2 style="text-align: center;">查詢學生</h2>
        <p style="text-align: center; color: #666;">查詢學生基本資料、所屬學系及緊急聯絡人資訊</p>

        <div class="card">
            <form method="post" action="student_query.php">
                <button type="submit" name="tab" value="with_record" class="tab <?php if($tab === 'with_record') echo 'active'; ?>">有諮商紀錄（<?php echo count($with_record_list); ?>）</button>
                <button type="submit" name="tab" value="without_record" class="tab <?php if($tab === 'without_record') echo 'active'; ?>">無諮商紀錄（<?php echo count($without_record_list); ?>）</button>
               
                <div style="margin-top: 20px;">
                    姓名/學號：<input type="text" name="keyword" value="<?php echo e($keyword); ?>" style="height:25px;">
                    學系：
                    <select name="dept_filter" style="height:28px;">
                        <option value="">全部</option>
                        <?php foreach ($dept_list as $dept) { ?>
                            <option value="<?php echo e($dept); ?>" <?php if($dept_filter === $dept) echo "selected"; ?>><?php echo e($dept); ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn">查詢</button>
                </div>
            </form>

            <?php if (count($data_list) > 0) { ?>
                <table>
                    <tr>
                        <th>姓名</th><th>學號</th><th>學系</th><th>聯絡電話</th><th>緊急聯絡人</th>
                        <?php if ($tab === "with_record") { ?>
                            <th>諮商次數</th><th>違規狀況</th>
                        <?php } ?>
                    </tr>
                    <?php foreach ($data_list as $s) { ?>
                        <tr>
                            <td><?php echo e($s["m_name"]); ?></td>
                            <td><?php echo e($s["m_id"]); ?></td>
                            <td><?php echo e($s["s_dept"]); ?></td>
                            <td><?php echo e($s["m_phone"] ? $s["m_phone"] : "--"); ?></td>
                            <td><?php echo e($s["s_emergency"] ? $s["s_emergency"] : "--"); ?></td>
                            <?php if ($tab === "with_record") { ?>
                                <td><?php echo (int)$s["consultation_count"]; ?></td>
                                <td>
                                    <?php if((int)$s["violation_count"] > 0) { ?>
                                        <span class="badge-warn">爽約 <?php echo (int)$s["violation_count"]; ?> 次</span>
                                    <?php } else { ?>
                                        <span class="badge-ok">正常</span>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p style="text-align:center; color:#999; margin-top:20px;">查無資料。</p>
            <?php } ?>
        </div>
    </div>
</body>
</html>





