<?php
session_start();  // 啟動交談期


// 檢查 Session 是否有存行政人員姓名，若沒有則給預設值
if (isset($_SESSION["AdminName"])) {
    $admin_name = $_SESSION["AdminName"];
} else {
    $admin_name = "行政人員";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>行政人員管理後台</title>
<style>
    /* 頂部導覽列樣式 (左姓名、右首頁) */
    .top-bar {
        width: 100%;
        display: flex;
        justify-content: space-between; /* 讓左右兩邊元件分開 */
        align-items: center;/*上下垂直制中*/
        padding: 12px 24px;
        box-sizing: border-box;
        background-color: #d8e2dc;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
   
    .admin-info {
        font-size: 16px;
        font-weight: bold;
        color: #2d2219;
    }


    .home-btn {
        padding: 8px 16px;
        background-color: #ffffff;
        color: #2d2219;
        text-decoration: none;
        font-weight: bold;
        border-radius: 6px;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: background 0.2s;
    }


    .home-btn:hover {
        background-color: #faf7f2;
    }


    /* 現代化按鈕樣式，擺脫傳統死板的按鈕 */
    .menu-btn {
        display: inline-block;
        width: 200px;
        padding: 15px 20px;
        margin: 10px;
        background-color: #CEDAD2;
        color: #2d2219;
        text-decoration: none;/* 移除超連結底線 */
        font-weight: bold;
        font-size: 16px;
        border-radius: 8px;/* 圓角 */
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }


    /* 說明文字的樣式 */
    .desc-text {
        color: #555555;
        font-size: 14px;
    }
</style>
</head>
<body bgcolor="#f4edd2" text="black" style="margin: 0; padding: 0;">


<div class="top-bar">
    <div class="admin-info">
        登入者：<?php echo htmlspecialchars($admin_name); ?>
    </div>
    <div>
        <a href="index.php" class="home-btn"> 首頁</a>
    </div>
</div>


<center>


    <br/>
    <h2>心輔諮商系統 - 行政人員管理後台</h2>
    <p style="color: #666;">歡迎回來！請選擇您要操作的功能項目。</p>
    <hr width="60%" color="#d8e2dc"/><br/>


    <table border="0" cellpadding="10" cellspacing="0" style="text-align: left;">
       
        <tr>
            <td><a href="consultant_manage.php" class="menu-btn">諮商師管理</a></td>
            <td class="desc-text">（管理諮商師的基本資料、聯絡電話、專長領域與排班排程）</td>
        </tr>
       
        <tr>
            <td><a href="student_query.php" class="menu-btn">查詢學生</a></td>
            <td class="desc-text">（查詢學生基本資料、所屬學系以及緊急聯絡人資訊）</td>
        </tr>
       
        <tr>
            <td><a href="admin_record.php" class="menu-btn">預約記錄</a></td>
            <td class="desc-text">（審核學生的諮商申請、管理媒合狀態與諮商紀錄追蹤）</td>
        </tr>
       
        <tr>
            <td><a href="blacklist.php" class="menu-btn">黑名單管理</a></td>
            <td class="desc-text">（檢視與新增違規學生名單、設定違規帳號的停權時間）</td>
        </tr>
       
        <tr>
            <td><a href="workload.php" class="menu-btn">服務統計</a></td>
            <td class="desc-text">（統計諮商時數、各項心理困擾案件比例與諮商師工作量）</td>
        </tr>
       
        <tr>
            <td><a href="satisfaction.php" class="menu-btn">滿意度調查</a></td>
            <td class="desc-text">（查看學生對諮商服務、整體環境與服務流程的評分反饋）</td>
        </tr>
       
        <tr>
            <td><a href="announcement.php" class="menu-btn">發布公告</a></td>
            <td class="desc-text">（發布心理健康資源連結、活動或中心公告）</td>
        </tr>


    </table>
</center>
</body>
</html>

