<?php
session_start();
include "../include/score_func.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../teacher/css/harrystyles.css">
    <title>Hodnotenie</title>
</head>
<body>
<a href="teacher.php">Domov</a>

    <p>si na stránke hodnotenia >:)</p>
    <table>
        <tr>
            <th>ID žiaka</th>
            <th>Meno žiaka</th>
            <?php echo "<th colspan=".autoColspan($_GET["schedule"]).">Odpovede</th>" ?>
            
            <th>Body</th>
            <th>Známka</th>
        </tr>
        <?php
            scoreAllStudents($_GET["schedule"]);
        ?>
    </table>
</body>
</html>