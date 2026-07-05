<?php
$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");


$sql = "SELECT ar.c_id AS 諮商師編號, 
               c.c_name AS 諮商師姓名, 
               COUNT(DISTINCT ar.m_id) AS 個案總數 
        FROM appointmentrecord ar, consultant c, consultant_field cf 
        WHERE ar.c_id = c.c_id 
          AND c.c_id = cf.c_id 
          AND cf.c_field = ( 
              SELECT aa_type FROM appointmentapply GROUP BY aa_type ORDER BY COUNT(*) DESC LIMIT 1 
          ) 
        GROUP BY ar.c_id, c.c_name 
        HAVING COUNT(DISTINCT ar.m_id) = ( 
            SELECT MAX(sub.case_count) FROM ( 
                SELECT COUNT(DISTINCT ar2.m_id) AS case_count 
                FROM appointmentrecord ar2, consultant_field cf2 
                WHERE ar2.c_id = cf2.c_id 
                  AND cf2.c_field = ( 
                      SELECT aa_type FROM appointmentapply GROUP BY aa_type ORDER BY COUNT(*) DESC LIMIT 1 
                  ) 
                GROUP BY ar2.c_id 
            ) AS sub 
        )";

$result = mysqli_query($link, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div style='padding:10px; background:#fff7ed; border:1px solid #ffedd5; border-radius:6px; margin-bottom:8px;'>
                <b>熱門諮商師：</b>{$row['諮商師姓名']} ({$row['諮商師編號']})<br>
                <b>目前於熱門領域所帶領的個案總數：</b><span style='color:#b45309; font-weight:bold; font-size:16px;'>{$row['個案總數']}</span> 人
              </div>";
    }
} else {
    echo "<p style='color:#64748b;'>目前查無符合條件的諮商師。</p>";
}
mysqli_close($link);
?>