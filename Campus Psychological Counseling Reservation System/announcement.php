<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>諮商資源與公告發布系統</title>
</head>
<body bgcolor="#ffe1b0" text="black">
<center>


<?php
session_start();  
require_once 'readdb.php'; // 引入妳的資料庫連線檔案


// 建立資料庫連線
$link = readDb();
$current_page = basename($_SERVER['PHP_SELF']);


// ==========================================
// 核心邏輯：處理動態欄位發布與 ID 自動生成
// ==========================================
if (isset($_POST["info_type"]) && isset($_POST["info_content"])) {
    $info_type = $_POST["info_type"];
    $info_content = $_POST["info_content"];
   
    if ($info_type != "" && $info_content != "") {
       
        // 1. 根據使用者選擇的類型，決定 ID 的開頭英文字母與寫入的目標欄位
        $prefix = "";
        $target_field = "";
       
        switch ($info_type) {
            case "r_ann":
                $prefix = "a";
                $target_field = "r_ann";
                break;
            case "r_service":
                $prefix = "s";
                $target_field = "r_service";
                break;
            case "r_ophour":
                $prefix = "o";
                $target_field = "r_ophour";
                break;
            case "r_faq":
                $prefix = "f";
                $target_field = "r_faq";
                break;
            case "r_link":
                $prefix = "l";
                $target_field = "r_link";
                break;
        }


        // 2. 自動產生該類型的最新 r_id (例如 a002, f002 等)
        $id_query = mysqli_query($link, "SELECT r_id FROM resource WHERE r_id LIKE '{$prefix}%' ORDER BY r_id DESC LIMIT 1");
        if (mysqli_num_rows($id_query) > 0) {
            $id_row = mysqli_fetch_assoc($id_query);
            $last_num = (int)substr($id_row['r_id'], 1); // 擷取後三位數字
            $next_num = $last_num + 1;
        } else {
            $next_num = 1; // 如果是該類型的第一筆，就從 1 開始
        }
        $next_r_id = $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT); // 補零格式化，如 a002


        // 3. 組裝 SQL 指令，將內容精準寫入被選中的欄位，其餘欄位給空字串
        $fields = array('r_ann' => '', 'r_service' => '', 'r_ophour' => '', 'r_faq' => '', 'r_link' => '');
        $fields[$target_field] = $info_content; // 將內容塞入指定欄位


        $insert_sql = "INSERT INTO resource (r_id, r_ann, r_service, r_ophour, r_faq, r_link)
                       VALUES ('$next_r_id', '{$fields['r_ann']}', '{$fields['r_service']}', '{$fields['r_ophour']}', '{$fields['r_faq']}', '{$fields['r_link']}')";
       
        if (mysqli_query($link, $insert_sql)) {
            echo "<script>alert('資訊發布成功！自動生成編號為：$next_r_id'); location.href='$current_page';</script>";
            exit();
        } else {
            echo "<font color=red>發布失敗：" . mysqli_error($link) . "</font><br/>";
        }
    } else {
        echo "<font color=red>提示：請選擇發布類型並填寫內容！</font><br/>";
    }
}
?>


<h2>諮商中心資源與公告發布</h2>
<p>
    <a href="index.php">首頁</a> |
    <a href="admin.php">回到行政後台</a>
</p>
<hr/>


<h3>【功能操作】動態發布諮商資源</h3>
<form action="" method="post">
    <table border="1" cellpadding="8" cellspacing='0' style='border-collapse: collapse; text-align: left;'>
        <tr bgcolor="#d8e2dc">
            <td colspan="2" align="center"><b>選擇類型與輸入內容</b></td>
        </tr>
        <tr bgcolor="#f1f5f3">
            <td>請選擇發布類型:</td>
            <td>
                <select name="info_type" required>
                    <option value="">--請選擇要發布的項目--</option>
                    <option value="r_ann">最新公告 (r_ann ➔ ID自動生成 aXXX)</option>
                    <option value="r_service">服務內容 (r_service ➔ ID自動生成 sXXX)</option>
                    <option value="r_ophour">開放時間 (r_ophour ➔ ID自動生成 oXXX)</option>
                    <option value="r_faq">常見問題 (r_faq ➔ ID自動生成 fXXX)</option>
                    <option value="r_link">相關連結 (r_link ➔ ID自動生成 lXXX)</option>
                </select>
            </td>
        </tr>
        <tr bgcolor="#f1f5f3">
            <td>請輸入內容:</td>
            <td>
                <textarea name="info_content" rows="8" cols="55" required placeholder="請根據選擇的項目，填寫對應的公告內容、問答或網址連結..."></textarea>
            </td>
        </tr>
        <tr bgcolor="#d8e2dc">
            <td colspan="2" align="center">
                <input type="submit" value="確認發布資料" style="font-weight: bold; cursor: pointer;"/>
            </td>
        </tr>
    </table>
</form>


<br/><hr/><br/>


<h3>【當前資源與公告列表總覽 (resource)】</h3>


<?php
// 撈出目前 resource 資料表的所有資料
$sql = "SELECT * FROM resource ORDER BY r_id DESC";
$result = mysqli_query($link, $sql);
$total_records = mysqli_num_rows($result);


echo "目前總共有: $total_records 筆資源資料<br/><br/>";


echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; text-align: center; width: 90%;'>";
echo "<tr bgcolor='#d8e2dc' style='font-weight: bold;'>";
echo "<td width='10%'>資源編號 (r_id)</td>";
echo "<td width='18%'>最新公告 (r_ann)</td>";
echo "<td width='22%'>服務內容 (r_service)</td>";
echo "<td width='15%'>開放時間 (r_ophour)</td>";
echo "<td width='20%'>常見問題 (r_faq)</td>";
echo "<td width='15%'>相關連結 (r_link)</td>";
echo "</tr>";


while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "<tr bgcolor='#f1f5f3'>";
    echo "<td><b>" . $rows['r_id'] . "</b></td>";
    echo "<td align='left'>" . nl2br(htmlspecialchars($rows['r_ann'])) . "</td>";
    echo "<td align='left'>" . nl2br(htmlspecialchars($rows['r_service'])) . "</td>";
    echo "<td align='left'>" . nl2br(htmlspecialchars($rows['r_ophour'])) . "</td>";
    echo "<td align='left'>" . nl2br(htmlspecialchars($rows['r_faq'])) . "</td>";
   
    // 如果是連結，自動幫它加上超連結標籤，方便點擊
    echo "<td align='left'>";
    if (!empty($rows['r_link'])) {
        echo "<a href='" . htmlspecialchars($rows['r_link']) . "' target='_blank'>" . htmlspecialchars($rows['r_link']) . "</a>";
    }
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

