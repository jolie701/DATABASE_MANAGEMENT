<?php
session_start();

// 💡 1. 檢查是否登入
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => '未登入或連線逾期']);
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

// 💡 2. 【核心：非同步儲存下拉選單變更，完美處理外鍵約束】
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    
    $aa_id = $_POST['aa_id'] ?? '';
    $m_id  = $_POST['m_id'] ?? '';   // 接收傳遞過來的學生 ID 以符合外鍵條件
    $field = $_POST['field'] ?? '';  // 'ar_state' 或 'c_id'
    $value = $_POST['value'] ?? '';

    // 【已修正】嚴格檢查所有必要參數，防止空字串寫入主鍵
    if (empty($aa_id) || empty($m_id) || !in_array($field, ['ar_state', 'c_id'])) {
        echo json_encode(['status' => 'error', 'message' => '傳遞參數遺漏或錯誤，無法更新。']);
        exit();
    }

    $link = mysqli_connect("localhost", "root", "", "Consultation");
    if (!$link) {
        echo json_encode(['status' => 'error', 'message' => '資料庫連線失敗']);
        exit();
    }
    mysqli_query($link, "SET NAMES utf8");

    // 檢查 appointmentrecord 表中是否已有該筆記錄
    $check_sql = "SELECT aa_id FROM appointmentrecord WHERE aa_id = ?";
    $stmt = mysqli_prepare($link, $check_sql);
    mysqli_stmt_bind_param($stmt, "s", $aa_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $rows = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($rows > 0) {
        // 已有記錄：進行 UPDATE
        $update_sql = "UPDATE appointmentrecord SET {$field} = ? WHERE aa_id = ?";
        $stmt = mysqli_prepare($link, $update_sql);
        mysqli_stmt_bind_param($stmt, "ss", $value, $aa_id);
    } else {
        // 尚未有記錄：進行 INSERT 新增（必須同時塞入 m_id 以滿足外鍵約束限制）
        $state_val = ($field === 'ar_state') ? $value : '';
        $cid_val   = ($field === 'c_id') ? $value : '';

        // 💡 關鍵：將 m_id 加入新增的欄位清單中
        $insert_sql = "INSERT INTO appointmentrecord (aa_id, m_id, ar_state, c_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ssss", $aa_id, $m_id, $state_val, $cid_val);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => '更新成功']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '資料庫更新失敗：' . mysqli_error($link)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
    exit(); 
}

// ----------------------------------------------------
// 💡 3. 【常規網頁載入：撈取畫面所需呈現的初始資料】
$login_administrative_id = $_SESSION["m_id"];
$login_administrative_name = isset($_SESSION["m_name"]) ? $_SESSION["m_name"] : $login_administrative_id;

$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");

$sql = "SELECT 
            aa.aa_id, 
            aa.m_id, 
            aa.aa_type, 
            aa.aa_summary, 
            aa.aa_date,      
            aa.aa_period,    
            ar.ar_state, 
            ar.c_id 
        FROM appointmentapply aa
        LEFT JOIN appointmentrecord ar ON aa.aa_id = ar.aa_id"; 

$result = mysqli_query($link, $sql);

if (!$result) {
    die("SQL 查詢失敗: " . mysqli_error($link));
}

// 預先撈出所有諮商師的所有空閒時間，用於彈出小視窗對照
$time_sql = "SELECT c_id, at_start, at_end FROM availabletime WHERE at_state = '尚未被預約' ORDER BY at_start ASC";
$time_result = mysqli_query($link, $time_sql);

$consultant_times = [];
if ($time_result) {
    while ($time_row = mysqli_fetch_assoc($time_result)) {
        $consultant_times[$time_row['c_id']][] = [
            'start' => $time_row['at_start'],
            'end' => $time_row['at_end']
        ];
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

<header style="background-color: #e2e8f0; height: 50px; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; font-family: sans-serif;">
    <div style="font-weight: bold; color: #334155; font-size: 14px;">
        行政人員：<?php echo htmlspecialchars($login_administrative_name); ?>
    </div>
    <div style="display: flex; gap: 10px;">
        <button onclick="history.back()" style="padding: 6px 16px; background-color: #ffffff; color: #334155; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold;">
            回上頁
        </button>
        <a href="index.php" style="padding: 6px 16px; background-color: #ffffff; color: #334155; text-decoration: none; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; font-weight: bold; display: inline-block;">
            首頁
        </a>
    </div>
</header>

<main style="padding: 30px 20px; font-family: sans-serif;">
    <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 25px; color: #000000;">
        學生預約紀錄總管理後台
    </h1>
    <div style="display: flex; gap: 12px; margin-bottom: 25px;">
        <button onclick="window.location.href='stat_completion.php'" class="menu-btn" style="padding: 10px 18px; background-color: #334155; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            查看整體完成率
        </button>
        
       <button onclick="window.location.href='stat_top_consultant.php'" class="menu-btn" style="padding: 10px 18px; background-color: #b45309; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            熱門領域最高個案諮商師(一個月內)
       </button>
    </div>
    <div style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead style="background-color: #f1f5f9; color: #334155; font-weight: bold;">
                <tr>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">學生 ID (m_id)</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">申請單號 (aa_id)</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">學生預約時間</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">問題領域 (aa_type)</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">管理預約狀態</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">分配諮商師 (c_id)</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">時段對照</th>
                    <th style="padding: 12px 15px; border-bottom: 2px solid #cbd5e1;">詳細內容</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $current_cid = $row['c_id'];
                        $target_date = isset($row['aa_date']) ? $row['aa_date'] : date("Y-m-d");
                        $target_period = isset($row['aa_period']) ? $row['aa_period'] : '未指定時段';
                        $aa_id = $row['aa_id'];
                        $m_id = $row['m_id']; // 學生 ID

                        echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
                        echo "<td style='padding: 12px 15px; font-weight: bold; color: #1e293b;'>" . htmlspecialchars($m_id) . "</td>";
                        echo "<td style='padding: 12px 15px; color: #64748b;'>" . htmlspecialchars($aa_id) . "</td>";
                        
                        echo "<td style='padding: 12px 15px; color: #053147; font-weight: bold;'>";
                        echo htmlspecialchars($target_date) . " <span style='font-size:12px; background:#e0f2fe; padding:2px 6px; border-radius:4px;'> " . htmlspecialchars($target_period) . " </span>";
                        echo "</td>";

                        echo "<td style='padding: 12px 15px;'> " . htmlspecialchars($row['aa_type']) . "</td>";
                        
                        // 🛠️ 狀態下拉選單
                        echo "<td style='padding: 12px 15px;'>";
                        echo "<select class='ajax-update' data-field='ar_state' data-aa-id='" . htmlspecialchars($aa_id) . "' data-m-id='" . htmlspecialchars($m_id) . "' style='padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e1; background-color: #fff;'>";
                        echo "<option value=''" . (empty($row['ar_state']) ? ' selected' : '') . ">-- 請選擇狀態 --</option>";
                        echo "<option value='預約成功'" . ($row['ar_state'] == '預約成功' ? ' selected' : '') . ">預約成功</option>";
                        echo "<option value='已通知'" . ($row['ar_state'] == '已通知' ? ' selected' : '') . ">已通知</option>";
                        echo "<option value='媒合中'" . ($row['ar_state'] == '媒合中' ? ' selected' : '') . ">媒合中</option>";
                        echo "<option value='諮商中'" . ($row['ar_state'] == '諮商中' ? ' selected' : '') . ">諮商中</option>";
                        echo "<option value='初步洽詢'" . ($row['ar_state'] == '初步洽詢' ? ' selected' : '') . ">初步洽詢</option>";
                        echo "<option value='結案/暫停'" . ($row['ar_state'] == '結案/暫停' ? ' selected' : '') . ">結案/暫停</option>";
                        echo "</select>";
                        echo "</td>";

                        // 🛠️ 分配諮商師欄位
                        echo "<td style='padding: 12px 15px;'>";
                        $avail_sql = "SELECT DISTINCT c_id FROM availabletime WHERE DATE(at_start) = '$target_date' AND at_state = '尚未被預約'";
                        $avail_result = mysqli_query($link, $avail_sql);
                        
                        echo "<select class='ajax-update' data-field='c_id' data-aa-id='" . htmlspecialchars($aa_id) . "' data-m-id='" . htmlspecialchars($m_id) . "' style='padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e1; background-color: #fff;'>";
                        echo "<option value=''>尚未分配</option>";
                        
                        if ($current_cid) {
                            echo "<option value='{$current_cid}' selected>{$current_cid} (目前指派)</option>";
                        }
                        
                        if ($avail_result && mysqli_num_rows($avail_result) > 0) {
                            while ($avail_row = mysqli_fetch_assoc($avail_result)) {
                                $available_cid = $avail_row['c_id'];
                                if ($available_cid !== $current_cid) { 
                                    echo "<option value='{$available_cid}'>{$available_cid} (此日有空)</option>";
                                }
                            }
                        }
                        echo "</select>";
                        echo "</td>";
                        
                        // 時段對照按鈕
                        echo "<td style='padding: 12px 15px;'>";
                        if ($current_cid) {
                            $time_list_html = "";
                            if (isset($consultant_times[$current_cid]) && !empty($consultant_times[$current_cid])) {
                                foreach ($consultant_times[$current_cid] as $t) {
                                    $time_list_html .= "• " . substr($t['start'], 5, 11) . " 到 " . substr($t['end'], 11, 5) . "<br>";
                                }
                            } else {
                                $time_list_html = "該諮商師目前無空閒時段！";
                            }
                            echo "<button onclick=\"openTimeModal('" . htmlspecialchars($current_cid) . "', '" . addslashes($time_list_html) . "')\" style='background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;'>可預約時段</button>";
                        } else {
                            echo "<span style='color: #94a3b8; font-size: 12px;'>請先指定諮商師</span>";
                        }
                        echo "</td>";
                        
                        // 檢視摘要
                        echo "<td style='padding: 12px 15px;'>";
                        echo "<button onclick=\"alert('【申請理由】\\n\\n" . addslashes($row['aa_summary']) . "')\" style='background-color: #f1f5f9; border: 1px solid #e2e8f0; color: #0284c7; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;'>檢視摘要</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='padding: 30px; text-align: center; color: #94a3b8;'>目前資料庫中沒有任何學生的預約紀錄。</td></tr>";
                }
                mysqli_close($link);
                ?>
            </tbody>
        </table>
    </div>
</main>

<dialog id="timeModal" style="border: none; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 25px; width: 350px; background-color: #ffffff; font-family: sans-serif;">
    <h3 style="margin-top: 0; color: #1e293b; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
         諮商師 <span id="modalCid" style="color: #16a34a;"></span> 空閒時段
    </h3>
    <div id="modalBody" style="color: #475569; font-size: 14px; line-height: 1.6; margin: 15px 0;">
    </div>
    <div style="text-align: right; margin-top: 20px;">
        <button onclick="document.getElementById('timeModal').close()" style="background-color: #64748b; color: white; border: none; padding: 6px 16px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;">
            關閉視窗
        </button>
    </div>
</dialog>

<script>
function openTimeModal(cid, timeHtml) {
    document.getElementById('modalCid').innerText = cid;
    document.getElementById('modalBody').innerHTML = timeHtml;
    document.getElementById('timeModal').showModal(); 
}

document.querySelectorAll('.ajax-update').forEach(select => {
    select.addEventListener('change', function() {
        const aa_id = this.getAttribute('data-aa-id');
        const m_id = this.getAttribute('data-m-id'); 
        const field = this.getAttribute('data-field');
        const value = this.value;

        const params = new URLSearchParams();
        params.append('aa_id', aa_id);
        params.append('m_id', m_id); 
        params.append('field', field);
        params.append('value', value);

        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => response.text()) 
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.status === 'success') {
                    if (field === 'c_id') {
                        alert('諮商師指派成功！頁面即將重新整理以同步時間資訊。');
                        location.reload(); 
                    } else {
                        console.log('預約狀態儲存成功！');
                    }
                } else {
                    alert('儲存失敗：' + data.message);
                }
            } catch (err) {
                alert('【系統後端報錯】\n\n' + text);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('網路連線失敗，請檢查本地伺服器狀態。');
        });
    });
});
</script>
     
</body>
</html>