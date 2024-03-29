<?php
session_start();
include "../include/score_func.php";
include '../../login/include/loginFunctions.inc.php';
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
    <title>Hodnotenie</title>
</head>
<body>
    <nav id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>
    <div class="manage_bar">
        <a href="teacher.php">Testy</a>
        <a href="group.php">Skupiny</a>
        <a href="scoreTest.php">Hodnotenia</a>
        <div id="type_number">
            <?php
                echo "<p>ID učiteľa: ".$_SESSION["TID"]."</p>"
            ?>
        </div>
    </div>
    <h1>Naplánované a ukončené testy</h1>
    <table>
        <tr>
            <th>Nazov</th>
            <th>Datum a čas začatia</th>
            <th>Datum a čas ukončenia</th>
        </tr>
        <?php
            scoreTable();
        ?>
    </table>
</body>
</html>