<?php
function readDb() {
    $dbName = 'Consultation';
    $conn = mysqli_connect('localhost', 'root', '', $dbName);
    if (!$conn) {
        die("連線失敗: " . mysqli_connect_error());
    }
    $conn->set_charset("utf8");
    if (!mysqli_select_db($conn, $dbName)) {
        die("無法開啟 $dbName 資料庫!");
    }
    return $conn;
}
?>

