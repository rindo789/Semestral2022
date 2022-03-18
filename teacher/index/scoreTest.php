<?php
session_start();
include "../include/teach_functions.inc.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../teacher/css/style.css">
    <title>Hodnotenie</title>
</head>
<body>
<a href="teacher.php">Domov</a>

    <p>si na stránke hodnotenia >:)</p>
    <?php
        scoreTable();
    ?>
</body>
</html>