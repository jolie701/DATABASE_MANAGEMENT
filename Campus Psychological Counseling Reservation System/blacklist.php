<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>黑名單管理系統</title>
</head>
<body bgcolor="#ffe1b0" text="black">
<center>


<?php
session_start();  // 啟動交談期
require_once 'readdb.php'; // 引入妳的資料庫連線檔案


// 呼叫函式建立連線
$link = readDb();


$edit_mode = false;
$edit_bl_id = "";
$edit_m_id = "";
$edit_reason = "";
$edit_start = "";
$edit_end = "";


// 當點擊下方列表的刪除
if (isset($_POST["delete_action"]) && isset($_POST["delete_id"])) {
    $target_id = (int)$_POST["delete_id"];
    // 只刪除黑名單紀錄
    $delete_sql = "DELETE FROM blacklist WHERE bl_id = $target_id";
    if (mysqli_query($link, $delete_sql)) {
        echo "<script>alert('已成功將該筆紀錄從黑名單中移除！'); location.href='" . basename($_SERVER['PHP_SELF']) . "';</script>";
        exit();
    } else {
        echo "<font color=red>刪除失敗：" . mysqli_error($link) . "</font><br/>";
    }
}


// 當點擊下方列表的編輯
if (isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["id"])) {
    $edit_id = (int)$_GET["id"];
    $search_sql = "SELECT * FROM blacklist WHERE bl_id = $edit_id";
    $search_res = mysqli_query($link, $search_sql);
    if ($row = mysqli_fetch_assoc($search_res)) {
        $edit_mode = true;
        $edit_bl_id = $row['bl_id'];
        $edit_m_id = $row['m_id'];
        $edit_reason = $row['bl_reason'];
        $edit_start = date('Y-m-d\TH:i', strtotime($row['bl_start']));
        $edit_end = date('Y-m-d\TH:i', strtotime($row['bl_end']));
    }
}


// 處理表單新增或修改資料的 PHP 程式碼
if (isset($_POST["student_id"]) && isset($_POST["reason"]) && isset($_POST["start_time"]) && isset($_POST["end_time"])) {
    $student_id = $_POST["student_id"];
    $reason = $_POST["reason"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $form_action = $_POST["form_action"];
   
    if ($student_id != "" && $reason != "" && $start_time != "" && $end_time != "") {
       
        if ($form_action == "update") {
            // 執行修改邏輯 (UPDATE)
            $target_bl_id = (int)$_POST["target_bl_id"];
            $update_sql = "UPDATE blacklist SET bl_reason = '$reason', bl_start = '$start_time', bl_end = '$end_time' WHERE bl_id = $target_bl_id";
           
            if (mysqli_query($link, $update_sql)) {
                echo "<script>alert('黑名單資料修改成功！'); location.href='" . basename($_SERVER['PHP_SELF']) . "';</script>";
                exit();
            } else {
                echo "<font color=red>修改失敗：" . mysqli_error($link) . "</font><br/>";
            }
           
        } else {
            // 執行新增邏輯 (INSERT)
            $check_member = mysqli_query($link, "SELECT m_id FROM member WHERE m_id = '$student_id'");
            if (mysqli_num_rows($check_member) > 0) {
               
                $id_query = mysqli_query($link, "SELECT MAX(bl_id) as max_id FROM blacklist");
                $id_row = mysqli_fetch_assoc($id_query);
                $next_bl_id = $id_row['max_id'] + 1;


                $insert_sql = "INSERT INTO blacklist (bl_id, bl_reason, bl_start, bl_end, m_id) VALUES ('$next_bl_id', '$reason', '$start_time', '$end_time', '$student_id')";
               
                if (mysqli_query($link, $insert_sql)) {
                    echo "<script>alert('新增黑名單成功！'); location.href='" . basename($_SERVER['PHP_SELF']) . "';</script>";
                    exit();
                } else {
                    echo "<font color=red>新增失敗：" . mysqli_error($link) . "</font><br/>";
                }
            } else {
                echo "<script>alert('❌ 新增失敗：學號 (m_id) 【$student_id】不存在於系統會員中！');</script>";
            }
        }
    } else {
        echo "<font color=red>提示：所有欄位皆為必填！</font><br/>";
    }
}
?>


<h2>黑名單管理系統</h2>
<p>
    <a href="index.php">首頁</a> |
    <a href="admin.php">回上一頁</a>
</p>
<hr/>


<h3>【功能操作】<?php echo $edit_mode ? "✏️ 修改黑名單資料 (編號: $edit_bl_id)" : "新增黑名單資料"; ?></h3>
<form action="" method="post">
    <input type="hidden" name="form_action" value="<?php echo $edit_mode ? 'update' : 'insert'; ?>">
    <input type="hidden" name="target_bl_id" value="<?php echo $edit_bl_id; ?>">


    <table border="1" cellpadding="8" cellspacing='0' style='border-collapse: collapse; text-align: left;'>
        <tr bgcolor="#d8e2dc">
            <td colspan="2" align="center"><b><?php echo $edit_mode ? "修改黑名單學生資料" : "輸入黑名單學生資料"; ?></b></td>
        </tr>
        <tr bgcolor="#f1f5f3">
            <td>學號 (m_id):</td>
            <td><input type="text" name="student_id" size="20" required placeholder="例如：H34985632" value="<?php echo $edit_mode ? htmlspecialchars($edit_m_id) : ''; ?>" <?php if($edit_mode) echo "readonly style='background:#ccc;'"; ?>/></td>
        </tr>
        <tr bgcolor="#f1f5f3">
            <td>鎖定原因:</td>
            <td>
                <select name="reason" required>
                    <option value="">--請選擇原因--</option>
                    <option value="無故爽約" <?php if($edit_reason == "無故爽約") echo "selected"; ?>>無故爽約</option>
                    <option value="頻繁遲到" <?php if($edit_reason == "頻繁遲到") echo "selected"; ?>>頻繁遲到</option>
                    <option value="騷擾與暴力行為" <?php if($edit_reason == "騷擾與暴力行為") echo "selected"; ?>>騷擾與暴力行為</option>
                    <option value="違反保密規定" <?php if($edit_reason == "違反保密規定") echo "selected"; ?>>違反保密規定</option>
                    <option value="惡意欠繳費用" <?php if($edit_reason == "惡意欠繳費用") echo "selected"; ?>>惡意欠繳費用</option>
                    <option value="違反醫療倫理之互動" <?php if($edit_reason == "違反醫療倫理之互動") echo "selected"; ?>>違反醫療倫理之互動</option>
                    <option value="其他" <?php if($edit_reason == "其他") echo "selected"; ?>>其他</option>
                </select>
            </td>
        </tr>
        <tr bgcolor="#f1f5f3">
            <td>停權開始時間:</td>
            <td><input type="datetime-local" name="start_time" required value="<?php echo $edit_mode ? $edit_start : ''; ?>"/></td>
        </tr>
        <tr bgcolor="#f1f5f3">
            <td>停權結束時間:</td>
            <td><input type="datetime-local" name="end_time" required value="<?php echo $edit_mode ? $edit_end : ''; ?>"/></td>
        </tr>
        <tr bgcolor="#d8e2dc">
            <td colspan="2" align="center">
                <input type="submit" value="<?php echo $edit_mode ? "💾 保存修改" : "新增黑名單資料"; ?>" style="font-weight: bold; cursor: pointer;"/>
                <?php if($edit_mode): ?>
                    <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" style="margin-left: 10px; text-decoration: none; font-size: 14px; color: #555;">[取消編輯]</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>


<br/><hr/><br/>


<h3>【黑名單名單總覽】</h3>


<?php
$sql = "SELECT
            b.bl_id,
            m.m_id,
            m.m_name,
            t.m_tel,
            b.bl_reason,
            b.bl_start,
            b.bl_end
        FROM blacklist b
        JOIN member m ON b.m_id = m.m_id
        LEFT JOIN member_tel t ON m.m_id = t.m_id";


$result = mysqli_query($link, $sql);
$total_records = mysqli_num_rows($result);


echo "記錄總數: $total_records 筆<br/><br/>";


echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; text-align: center;'>";
echo "<tr bgcolor='#d8e2dc' style='font-weight: bold;'>";
echo "<td>黑名單編號</td>";
echo "<td>學號</td>";
echo "<td>姓名</td>";
echo "<td>電話</td>";
echo "<td>鎖定原因</td>";
echo "<td>停權開始時間</td>";
echo "<td>停權結束時間</td>";
echo "<td>編輯</td>";  
echo "<td>刪除</td>";  
echo "</tr>";


while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "<tr bgcolor='#f1f5f3'>";
    echo "<td>" . $rows['bl_id'] . "</td>";
    echo "<td>" . $rows['m_id'] . "</td>";
    echo "<td>" . $rows['m_name'] . "</td>";
    echo "<td>" . ($rows['m_tel'] ? $rows['m_tel'] : '無電話') . "</td>";
    echo "<td>" . $rows['bl_reason'] . "</td>";
    echo "<td>" . $rows['bl_start'] . "</td>";
    echo "<td>" . $rows['bl_end'] . "</td>";
   
    // 編輯 (連回自己，帶入單一安全參數)
    echo "<td><a href='" . basename($_SERVER['PHP_SELF']) . "?action=edit&id=" . $rows['bl_id'] . "'><b>編輯</b></a></td>";
   
    echo "<td>";
    echo "<form action='' method='post' onsubmit='return confirm(\"確定要將學生【" . $rows['m_name'] . "】從黑名單中刪除嗎？\")' style='margin:0; display:inline;'>";
    echo "<input type='hidden' name='delete_action' value='1'>";
    echo "<input type='hidden' name='delete_id' value='" . $rows['bl_id'] . "'>";
    echo "<input type='submit' value='刪除' style='color:red; font-weight:bold; background:none; border:none; padding:0; cursor:pointer; font-family:inherit; font-size:inherit;'>";
    echo "</form>";
    echo "</td>";
   
    echo "</tr>";
}
echo "</table><br>";


mysqli_free_result($result);
mysqli_close($link);
?>


</center>
</body>
</html>

