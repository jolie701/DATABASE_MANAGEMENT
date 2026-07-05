<?php
$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");


$sql_faq = "SELECT r_id, r_faq FROM resource
             WHERE r_faq IS NOT NULL AND r_faq != ''
             ORDER BY r_id DESC";
$result_faq = mysqli_query($link, $sql_faq);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>常見問答</title>
    <style>
        body {
            font-family: "Microsoft JhengHei", Arial, sans-serif;
            background-color: #f4edd2;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }


        .container {
            width: 100%;
            max-width: 800px;
            box-sizing: border-box;
        }


        h2 {
            font-size: 28px;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }


        .faq-list {
            list-style-type: none;
            padding: 0;
        }


        .faq-item {
            margin-bottom: 30px;
            font-size: 22px;
            line-height: 1.8;
            color: #000000;
            padding-bottom: 15px;
            border-bottom: 1px dashed #cccccc;
        }


        .back-btn:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>常見問答 (FAQ)</h2>
        <ul class="faq-list">
            <?php
            if ($result_faq && mysqli_num_rows($result_faq) > 0) {
                while ($row = mysqli_fetch_assoc($result_faq)) {
                    $faq_content = $row["r_faq"];
                    echo '<li class="faq-item">';
                    echo nl2br($faq_content);
                    echo '</li>';
                }
            }
           
            else {
                echo '<li class="faq-item" style="color: #666; border: none;">目前暫無常見問答。</li>';
            }


            mysqli_close($link);
            ?>
        </ul>
        <button><a href="index.php">回首頁</a></button>
    </div>
</body>
</html>


