<?php
session_start();
include "../include/studentFunctions.inc.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../teacher/css/tstyle.css">
    <title>Študent/Testy/Hodnotenia</title>
</head>
<body>
    <table>
        <tr><th colspan="3">Dostupné testy</th></tr>
        <tr>
            <th>Id</th>
            <th>Nazov</th>
            <th>Show</th>
        </tr>
        <?php
        showTests();
        ?>
    </table>
    <br>
    <table>
        <tr><th colspan="3">Hodnotenia</th></tr>
        <tr>
            <th>Id</th>
            <th>Show</th>
        </tr>
        <?php
        showScores();
        ?>
    </table>
    <a href="../../login/include/singout.inc.php">Odhlásiť sa</a>
    <a href="studentGame.php">Hra</a>
    <a href='../include/game.inc.php?testId=43'>Ukáž</a></td>"
</body>
</html>