<?php
session_start();
include_once '../../login/include/loginFunctions.inc.php';
include_once '../include/manage_user.inc.php';
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


<body onload="searchGroup();">
    <div id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </div>

    <div class="manage_bar">
        <a href="manage_user.php">Manažér užívateľov</a>
        <a href="manage_group.php">Manažér skupín</a>
        <a href="manage_tests.php">Manažér testov</a>
        <a href="manage_results.php">Manažér hodnotení</a>
    </div>

    <div id="groups">
        <a href="">
            <button type="button">Vytvoriť skupinu</button>
        </a>

        <br><br>

        <div class="search_bar">
            <h3>Vyhladaj</h3>
            <br>
            <input type="text" id="group_name" placeholder="Zadaj názov skupiny">
            <input type="button" onclick="searchGroup();" value="Hľadaj">
        </div>

        <table id="group_table">
            <thead>
                <tr>
                    <th>ID skupiny</th>
                    <th>Názov</th>
                    <th>ID Vlastníka</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

    </div>
</body>

</html>