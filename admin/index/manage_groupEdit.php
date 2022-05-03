<?php
session_start();
include_once '../../login/include/loginFunctions.inc.php';
include_once '../include/manage_group.inc.php';

$_SESSION["groupEdit"] = $_GET["group_id"];
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

    <script src="../js/search_group.js"></script>
</head>

<body>
    <div id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </div>

    <div class="manage_bar">
        <a href="manage_user.php">Manažér užívateľov</a>
        <a href="manage_group.php">Manažér skupín</a>
        <a href="manage_tests.php">Manažér testov</a>
        <a href="manage_results.php">Manažér hodnotení</a>
    </div>
    <form action='../include/manage_group_check.php' method='POST' id="group_form">
        
        <?php
            loadStudents(); 
        ?>
        <button type="button" onclick="addField();" id="add_button">Pridaj študenta</button>

        <input type="submit" name="saveGroup">
    </form>
</body>

</html>