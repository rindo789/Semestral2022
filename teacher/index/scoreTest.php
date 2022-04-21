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
            <th>id plan</th>
            <th>ID testu</th>
            <th>Nazov</th>
            <th>Datum začatia</th>
            <th>Datum ukončenia</th>
        </tr>
        <?php
            scoreTable();
        ?>
    </table>
</body>
</html>