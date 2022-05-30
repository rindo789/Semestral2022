<?php
session_start();
include "../include/show_tables.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/student.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <title>Hodnotenie</title>
</head>
<body>
<nav id="menu">
        <a href="student.php">Domov</a>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>
    <?php
        scoreTable();
    ?>
</body>
</html>