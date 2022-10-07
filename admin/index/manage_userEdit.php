<?php
include_once '../../login/include/loginFunctions.inc.php';
include_once '../include/manage_user.inc.php';

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
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">

    <script src="../js/search_user.js"></script>
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

    <div class="user_edit">
        <h3>Editovanie užívateľa</h3>
        <div id="add_user_form">
            <?php
                echo formUser($_GET["user_id"]);
            ?>
        </div>
    </div>
</body>

</html>