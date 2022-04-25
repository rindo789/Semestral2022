<?php
include_once "../../login/include/loginFunctions.inc.php";
include_once "../include/manage_user.inc.php";
session_start();
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
    <nav>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
        <a href="index.php">Domov</a>
    </nav>
    <div id="add_user_form">
        <form action="../include/manage_user_check.php" method="post">
            <label for="nick">Nickname</label>
            <input type="text" name="nick">

            <label for="name">Meno a priezvisko</label>
            <input type="text" name="name">

            <label for="email">E-mail</label>
            <input type="email" name="email">

            <label for="type">Typ</label>
            <select name="type">
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>

            <input type="submit" value="Vytvoriť" name="submit">
        </form>
    </div>
</body>

</html>