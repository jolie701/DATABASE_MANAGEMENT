<?php
session_start();

if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: index.php");
    exit();
}
$login_administrative_id = $_SESSION["m_id"];
$login_administrative_name = isset($_SESSION["m_name"]) ? $_SESSION["m_name"] : $login_administrative_id;

$workload_list = []; // 先準備空陣列存工作量
$search_id = "";     // 用來記錄使用者輸入的查詢 ID

// 這裡的 POST 鍵值要跟 HTML 的 input name="consultant_id" 一致
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["consultant_id"])) {
    $search_id = $_POST["consultant_id"]; // 抓取使用者在輸入框打的 ID

    if (!empty($search_id)) {
        $link = mysqli_connect("localhost", "root", "", "Consultation");
        mysqli_query($link, "SET NAMES utf8");

       
        $sql = "SELECT
                    c.c_id,
                    c.c_name,
                    SUM(TIMESTAMPDIFF(HOUR, cr.cr_start, cr.cr_end)) AS 諮商時數,
                    COUNT(DISTINCT ar.ar_id) AS 總服務件數,
                    
                    SUM(CASE WHEN aa.aa_type = '人際關係' THEN 1 ELSE 0 END) AS 人際關係件數,
                    SUM(CASE WHEN aa.aa_type = '自我探索' THEN 1 ELSE 0 END) AS 自我探索件數,
                    SUM(CASE WHEN aa.aa_type = '學業壓力' THEN 1 ELSE 0 END) AS 學業壓力件數,
                    SUM(CASE WHEN aa.aa_type = '情緒困擾' THEN 1 ELSE 0 END) AS 情緒困擾件數
                FROM
                    consultant c
                    LEFT JOIN appointmentrecord ar ON c.c_id = ar.c_id
                    LEFT JOIN consultationrecord cr ON cr.ar_id = ar.ar_id 
                    LEFT JOIN appointmentapply aa ON aa.aa_id = ar.aa_id   
                WHERE
                    ar.ar_state = '結案/暫停' 
                    AND c.c_id = '$search_id' 
                GROUP BY
                    c.c_id, c.c_name";
    
        $result = mysqli_query($link, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $workload_list[] = $row;
            }
        }
        mysqli_close($link);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>校園心理諮商預約管理系統</title> 
</head>
<body style="background-color: #f4edd2; color: black; margin: 0; padding: 0;">

<header style="background-color: #e2e8f0; height: 50px; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; font-family: sans-serif; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="font-weight: bold; color: #334155; font-size: 16px;">
        登入者：行政人員 (<?php echo htmlspecialchars($login_administrative_name); ?>)
    </div>
    
    <div style="display: flex; gap: 10px;">
        <button onclick="history.back()" style="padding: 6px 16px; background-color: #ffffff; color: #334155; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            回上頁
        </button>
        <a href="index.php" style="padding: 6px 16px; background-color: #ffffff; color: #334155; text-decoration: none; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: bold; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            首頁
        </a>
    </div>
</header>

<main style="padding: 30px 20px; font-family: sans-serif; text-align: center;">
    
    <h1 style="font-size: 30px; font-weight: bold; margin-bottom: 10px; color: #000000;">
        諮商師服務量統計
    </h1>
    
    <hr style="border: none; border-top: 2px solid #cbd5e1; margin: 0 auto 30px auto; width: 150px; opacity: 0.5;">
    
    <form method="POST" action="" style="display: inline-block; background-color: #f8f9fa; padding: 20px 40px; border-radius: 8px; border: 1px solid #e2e8f0;">
        
        <div style="font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #334155;">
            查詢 (請輸入諮商師 id):
        </div>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
            <input type="text" name="consultant_id" value="<?php echo htmlspecialchars($search_id); ?>" placeholder="例如：D12345" style="padding: 6px 12px; width: 150px; font-size: 16px; border: 1px solid #cbd5e1; border-radius: 4px;">
            
            <button type="submit" style="padding: 6px 16px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">
                查詢
            </button>
        </div>
        
    </form>
</main>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <div style="padding: 0 20px;">
    <?php if (!empty($workload_list)): ?>
        <table border="1" style="border-collapse: collapse; width: 100%; text-align: center; background-color: white; font-family: sans-serif;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th style="padding: 10px;">諮商師 ID</th>
                    <th style="padding: 10px;">諮商師姓名</th>
                    <th style="padding: 10px;">總諮商時數</th>
                    <th style="padding: 10px;">總服務件數</th>
                    <th style="padding: 10px; color: #181cdf;">人際關係比例</th>
                    <th style="padding: 10px; color: #1c50de;">自我探索比例</th>
                    <th style="padding: 10px; color: #0934b4;">學業壓力比例</th>
                    <th style="padding: 10px; color: #263bdc;">情緒困擾比例</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            foreach ($workload_list as $work): 
                $total = $work['總服務件數'];
                
                if ($total > 0) {
                    // 先算前三個，利用 round() 做四捨五入的整數百分比
                    $p1 = round(($work['人際關係件數'] / $total) * 100);
                    $p2 = round(($work['自我探索件數'] / $total) * 100);
                    $p3 = round(($work['學業壓力件數'] / $total) * 100);
                    // 為了防四捨五入出現 101% 或 99% 的情況，最後一項用 100 去減，確保完美加總 100%
                    $p4 = 100 - ($p1 + $p2 + $p3);
                } else {
                    $p1 = $p2 = $p3 = $p4 = 0;
                }
            ?>
                <tr>
                    <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($work['c_id']); ?></td>
                    <td style="padding: 12px;"><?php echo htmlspecialchars($work['c_name']); ?></td>
                    <td style="padding: 12px;"><?php echo $work['諮商時數'] ? htmlspecialchars($work['諮商時數']) : 0; ?> 小時</td>
                    <td style="padding: 12px; font-weight: bold; color: #334155;"><?php echo $total; ?> 件</td>
                    <td style="padding: 12px; font-weight: bold; color: #0284c7;"><?php echo $p1; ?>%</td>
                    <td style="padding: 12px; font-weight: bold; color: #16a34a;"><?php echo $p2; ?>%</td>
                    <td style="padding: 12px; font-weight: bold; color: #b45309;"><?php echo $p3; ?>%</td>
                    <td style="padding: 12px; font-weight: bold; color: #dc2626;"><?php echo $p4; ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h3 style="color: red; text-align: center;">找不到該諮商師 ID 的服務量統計紀錄，或該諮商師尚未有狀態為「結案/暫停」的預約資料。</h3>
    <?php endif; ?>
    </div>
<?php endif; ?>
     
</body>
</html>