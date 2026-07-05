<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>人員資料管理（含電話號碼）</title>
</head>
<body>


<?php
session_start();
require_once 'readdb.php'; // 引入妳的資料庫連線檔案


// 建立資料庫連線
$link = readDb();
$current_page = basename($_SERVER['PHP_SELF']);


// ==========================================
// 邏輯 1：處理「新增學生與電話」
// ==========================================
if (isset($_POST["action"]) && $_POST["action"] == "add_student") {
    $m_id = $_POST["m_id"];
    $m_password = $_POST["m_password"];
    $m_name = $_POST["m_name"];
    $m_email = $_POST["m_email"];
    $s_dept = $_POST["s_dept"];
    $m_tel = $_POST["m_tel"]; // 學生電話


    if ($m_id != "" && $m_password != "" && $m_name != "" && $m_email != "" && $s_dept != "" && $m_tel != "") {
       
        // 檢查學號是否重複
        $check_stud = mysqli_query($link, "SELECT m_id FROM member WHERE m_id = '$m_id'");
        if (mysqli_num_rows($check_stud) > 0) {
            echo "<script>alert('❌ 錯誤：該學號 ($m_id) 已經存在於系統中！');</script>";
        } else {
            // 開啟交易處理（Transaction），確保三張表同步成功
            mysqli_begin_transaction($link);


            try {
                // 1. 寫入 member 表
                $sql1 = "INSERT INTO member (m_id, m_password, m_name, m_email) VALUES ('$m_id', '$m_password', '$m_name', '$m_email')";
                mysqli_query($link, $sql1);


                // 2. 寫入 student 表
                $sql2 = "INSERT INTO student (m_id, s_dept) VALUES ('$m_id', '$s_dept')";
                mysqli_query($link, $sql2);


                // 3. 寫入 member_tel 表
                $sql3 = "INSERT INTO member_tel (m_id, m_tel) VALUES ('$m_id', '$m_tel')";
                mysqli_query($link, $sql3);


                mysqli_commit($link);
                echo "<script>alert('🎉 新增學生與電話成功！'); location.href='$current_page';</script>";
                exit();
            } catch (Exception $e) {
                mysqli_rollback($link);
                echo "<font color=red>學生新增失敗，資料已復原。錯誤：" . mysqli_error($link) . "</font><br/>";
            }
        }
    } else {
        echo "<font color=red>提示：所有學生欄位皆為必填！</font><br/>";
    }
}


// ==========================================
// 邏輯 2：處理「新增諮商師與電話」
// ==========================================
if (isset($_POST["action"]) && $_POST["action"] == "add_consultant") {
    $c_id = $_POST["c_id"];
    $c_name = $_POST["c_name"];
    $c_gender = $_POST["c_gender"];
    $c_mail = $_POST["c_mail"];
    $c_tel = $_POST["c_tel"]; // 諮商師電話


    if ($c_id != "" && $c_name != "" && $c_gender != "" && $c_mail != "" && $c_tel != "") {
       
        // 檢查諮商師編號是否重複
        $check_cons = mysqli_query($link, "SELECT c_id FROM consultant WHERE c_id = '$c_id'");
        if (mysqli_num_rows($check_cons) > 0) {
            echo "<script>alert('❌ 錯誤：該諮商師編號 ($c_id) 已經存在於系統中！');</script>";
        } else {
            // 開啟交易處理
            mysqli_begin_transaction($link);


            try {
                // 1. 寫入 consultant 表
                $sql_c1 = "INSERT INTO consultant (c_id, c_name, c_gender, c_mail) VALUES ('$c_id', '$c_name', '$c_gender', '$c_mail')";
                mysqli_query($link, $sql_c1);


                // 2. 寫入 consultant_tel 表
                $sql_c2 = "INSERT INTO consultant_tel (c_id, c_tel) VALUES ('$c_id', '$c_tel')";
                mysqli_query($link, $sql_c2);


                mysqli_commit($link);
                echo "<script>alert('🎉 新增諮商師與電話成功！'); location.href='$current_page';</script>";
                exit();
            } catch (Exception $e) {
                mysqli_rollback($link);
                echo "<font color=red>諮商師新增失敗，資料已復原。錯誤：" . mysqli_error($link) . "</font><br/>";
            }
        }
    } else {
        echo "<font color=red>提示：所有諮商師欄位皆為必填！</font><br/>";
    }
}
?>


<h2>人員資料管理系統（含電話號碼）</h2>
<hr/>


<h3>【新增學生資料】</h3>
<form action="" method="post">
    <input type="hidden" name="action" value="add_student">
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td>學號 (m_id)：</td>
            <td><input type="text" name="m_id" required placeholder="如：H34985632" size="15"></td>
        </tr>
        <tr>
            <td>密碼 (m_password)：</td>
            <td><input type="password" name="m_password" required size="15"></td>
        </tr>
        <tr>
            <td>學生姓名 (m_name)：</td>
            <td><input type="text" name="m_name" required placeholder="如：江小惠" size="15"></td>
        </tr>
        <tr>
            <td>電子信箱 (m_email)：</td>
            <td><input type="email" name="m_email" required placeholder="xxx@gs.ncku.edu.tw" size="30"></td>
        </tr>
        <tr>
            <td>就讀科系 (s_dept)：</td>
            <td><input type="text" name="s_dept" required placeholder="如：工資系" size="15"></td>
        </tr>
        <tr>
            <td>電話號碼 (m_tel)：</td>
            <td><input type="text" name="m_tel" required placeholder="如：0911222333" size="15"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="確認新增學生">
            </td>
        </tr>
    </table>
</form>


<br/><hr/><br/>


<h3>【新增諮商師資料】</h3>
<form action="" method="post">
    <input type="hidden" name="action" value="add_consultant">
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td>諮商師編號 (c_id)：</td>
            <td><input type="text" name="c_id" required placeholder="如：D45357" size="15"></td>
        </tr>
        <tr>
            <td>姓名 (c_name)：</td>
            <td><input type="text" name="c_name" required placeholder="如：王政賢" size="15"></td>
        </tr>
        <tr>
            <td>性別 (c_gender)：</td>
            <td>
                <input type="radio" name="c_gender" value="女性" checked>女性
                <input type="radio" name="c_gender" value="男性">男性
            </td>
        </tr>
        <tr>
            <td>電子信箱 (c_mail)：</td>
            <td><input type="email" name="c_mail" required placeholder="xxx@gmail.com" size="30"></td>
        </tr>
        <tr>
            <td>電話號碼 (c_tel)：</td>
            <td><input type="text" name="c_tel" required placeholder="如：0943432568" size="15"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="確認新增諮商師">
            </td>
        </tr>
    </table>
</form>


<br/><hr/><br/>


<h3>當前學生名單總覽 (含電話)</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr bgcolor="#eeeeee">
        <td>學號 (m_id)</td>
        <td>姓名</td>
        <td>電子信箱</td>
        <td>就讀科系</td>
        <td>電話號碼</td>
    </tr>
    <?php
    $stud_sql = "SELECT m.m_id, m.m_name, m.m_email, s.s_dept, t.m_tel
                 FROM member m
                 JOIN student s ON m.m_id = s.m_id
                 LEFT JOIN member_tel t ON m.m_id = t.m_id";
    $stud_res = mysqli_query($link, $stud_sql);
    while ($row = mysqli_fetch_assoc($stud_res)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['m_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['m_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['m_email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['s_dept']) . "</td>";
        echo "<td>" . ($row['m_tel'] ? htmlspecialchars($row['m_tel']) : '無電話') . "</td>";
        echo "</tr>";
    }
    ?>
</table>


<br/><br/>


<h3>當前諮商師名單總覽 (含電話)</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr bgcolor="#eeeeee">
        <td>諮商師編號 (c_id)</td>
        <td>姓名</td>
        <td>性別</td>
        <td>電子信箱</td>
        <td>電話號碼</td>
    </tr>
    <?php
    $cons_sql = "SELECT c.*, t.c_tel
                 FROM consultant c
                 LEFT JOIN consultant_tel t ON c.c_id = t.c_id";
    $cons_res = mysqli_query($link, $cons_sql);
    while ($row = mysqli_fetch_assoc($cons_res)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['c_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['c_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['c_gender']) . "</td>";
        echo "<td>" . htmlspecialchars($row['c_mail']) . "</td>";
        echo "<td>" . ($row['c_tel'] ? htmlspecialchars($row['c_tel']) : '無電話') . "</td>";
        echo "</tr>";
    }
    mysqli_close($link);
    ?>
</table>


</body>
</html>

