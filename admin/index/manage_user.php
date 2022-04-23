<?php
include '../../login/include/loginFunctions.inc.php';
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

    <script src="../js/search_user.js"></script>
</head>


<body>
    <nav>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
        <a href="index.php">Domov</a>
    </nav>
    <div class="sidenav">
        <a href="crate_user.php"></a>
    </div>
    <div class="search_user">
        <div class="search_bar">
            <input type="text" id="user_name" placeholder="Zadaj meno užívateľa">
            <input type="button" onclick="searchUser()" value="Hľadaj">
        </div>
        <div id="search_result">
            <p id="result_text"></p>
        </div>
        <table id="search_table">
            <tr>
                <th>ID</th>
                <th>Nick</th>
                <th>Meno Priezvisko</th>
                <th>Manažér</th>
                <th>Typ</th>
            </tr>
        </table>
    </div>
</body>

</html>