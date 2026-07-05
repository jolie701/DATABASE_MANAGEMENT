<?php
$link = mysqli_connect("localhost", "root", "", "Consultation");
mysqli_query($link, "SET NAMES utf8");


$sql = "SELECT r_id, r_ann, r_link FROM resource
             WHERE r_ann IS NOT NULL AND r_ann != ''
               AND r_link IS NOT NULL AND r_link != ''
             ORDER BY r_id DESC";
$result = mysqli_query($link, $sql);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>資源連結</title>
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


        .resource-list {
            list-style-type: none;
            padding: 0;
        }


        .resource-item {
            margin-bottom: 20px;
            font-size: 22px;
        }


        .resource-link {
            color: #4d3bf3;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>相關資源連結</h2>
        <ul class="resource-list">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $title = $row["r_ann"];
                    $url = $row["r_link"];
                   
                    echo '<li class="resource-item">';
                    echo '<a href="' . $url . '" class="resource-link" target="_blank">';
                    echo '<u>' . $title . '</u>';
                    echo '</a>';
                    echo '</li>';
                }
            }
           
            else {
                echo '<li class="resource-item" style="color: #666;">目前暫無相關資源連結。</li>';
            }


            mysqli_close($link);
            ?>
        </ul>
        <button><a href="index.php">回首頁</a></button>
    </div>
</body>
</html>

