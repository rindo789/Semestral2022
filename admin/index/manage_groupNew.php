<?php
session_start();
include_once '../../login/include/loginFunctions.inc.php';
include_once '../include/manage_group.inc.php';
loginCheck();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/harrystyles.css">
    <title>Document</title>
</head>

<body>
    <div id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </div>

    <div class="manage_bar">
        <a href="manage_user.php">Manažér užívateľov</a>
        <a href="manage_group.php">Manažér skupín</a>
        <a href="manage_tests.php">Manažér testov</a>
        <a href="manage_result.php">Manažér hodnotení</a>
    </div>
    <form action='../include/manage_group_check.php' method='POST' id="group_form">
        <label for="name">Meno skupiny</label>
        <br>
        <input type="text" placeholder="Zadajte meno skupiny" name="name">
        <br>
        <label for="teacher">ID učiteľa</label>
        <br>
        <input type="number" placeholder="Zadajte ID ucitela" name="teacher_id">
        <br>
        <input type="submit" name="newGroup" value="Vytvoriť skupinu">
    </form>
</body>

</html>