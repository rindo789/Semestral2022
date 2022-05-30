<?php
session_start();
include_once '../../login/include/loginFunctions.inc.php';
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

    <script src="../js/search_test.js"></script>
</head>


<body onload="searchTest();">
    <div id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </div>

    <div class="manage_bar">
        <a href="manage_user.php">Manažér užívateľov</a>
        <a href="manage_group.php">Manažér skupín</a>
        <a href="manage_tests.php">Manažér testov</a>
        <a href="manage_result.php">Manažér hodnotení</a>
    </div>

    <div id="search_add_flex">
        <div id="search_bar">
            <h3>Vyhladaj test</h3>
            <input type="text" id="search_field" name="search_prompt" placeholder="Zadaj výraz na vyhľadanie">
            <button onclick="searchTest()">Vyhladaj</button>
        </div>

        <a href="manage_testsNew.php"><button>Vytvor nový test</button></a>
    </div>
    
    <br>
    <div id="test_table">
        <table id="search_table">
            <thead>
                <tr>
                    <th>ID testu</th>
                    <th>Názov testu</th>
                    <th>ID uciteľa</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</body>

</html>