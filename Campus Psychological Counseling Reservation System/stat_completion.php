<?php
$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");

$sql = "SELECT 
            aa.aa_type AS 需求表單類型, 
            COUNT(ar.ar_id) AS 總預約人次, 
            ROUND( COUNT(CASE WHEN ar.ar_state = '預約成功' THEN 1 END) * 100.0 /   
                   NULLIF(COUNT(ar.ar_id), 0), 2 ) AS 完成率
        FROM
            appointmentapply aa
            JOIN student s ON aa.m_id = s.m_id
            LEFT JOIN appointmentrecord ar ON aa.aa_id = ar.aa_id           
            
            AND ar.ar_date BETWEEN '2026-05-01' AND '2026-05-31'                  
        GROUP BY aa.aa_type";

$result = mysqli_query($link, $sql);

echo "<table style='width:100%; border-collapse:collapse; font-size:14px;'>
        <tr style='background:#f1f5f9; text-align:left;'>
            <th style='padding:8px; border-bottom:1px solid #cbd5e1;'>領域類型</th>
            <th style='padding:8px; border-bottom:1px solid #cbd5e1;'>總人次</th>
            <th style='padding:8px; border-bottom:1px solid #cbd5e1;'>完成率</th>
        </tr>";

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rate = $row['完成率'] !== null ? $row['完成率'] . "%" : "0%";
        echo "<tr>
                <td style='padding:8px; border-bottom:1px solid #e2e8f0;'>{$row['需求表單類型']}</td>
                <td style='padding:8px; border-bottom:1px solid #e2e8f0;'>{$row['總預約人次']} 次</td>
                <td style='padding:8px; border-bottom:1px solid #e2e8f0; color:#16a34a; font-weight:bold;'>{$rate}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3' style='padding:8px; text-align:center;'>此區間暫無數據</td></tr>";
}
echo "</table>";
mysqli_close($link);
?>