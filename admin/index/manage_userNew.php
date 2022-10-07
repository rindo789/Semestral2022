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
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
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

    <div id="add_user_form">
        <h3>Vytvorenie nového užívateľa</h3>
        <form action="../include/manage_user_check.php" method="post">
            <label for="nick">Užívateľské meno</label>
            <input type="text" name="nick" placeholder="Zadajte užívateľské meno">

            <label for="name">Meno a priezvisko</label>
            <input type="text" name="name" placeholder="Zadajte meno a priezvisko">

            <label for="email">E-mail</label>
            <input type="email" name="email" placeholder="Zadajte e-mail">

            <label for="type">Typ užívateľa</label>
            <select name="type">
                <option value="teacher">Učiteľ</option>
                <option value="student">Študent</option>
            </select>

            <input type="submit" value="Vytvoriť" name="submit" id="new_user_button">
        </form>
    </div>
</body>

</html>