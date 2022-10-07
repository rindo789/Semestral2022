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
    <title>Študent/Testy/Hodnotenia</title>
</head>
<body>
    <nav id="menu">
        <?php 
            echo "<p id='type_number'>ID študenta: ".$_SESSION["SID"]."</p>"
        ?>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>
    <table>
        <tr><th colspan="4"  class="nadpis_tabulky">Dostupné testy</th></tr>
        <tr>
            <th>Názov testu</th>
            <th>Dátum a čas začiatku</th>
            <th></th>
        </tr>
        <?php
            showTests();
        ?>
    </table>
    <br>
    <table>
        <tr><th colspan="3"  class="nadpis_tabulky">Hodnotenia</th></tr>
        <tr>
            <th>Názov testu</th>
            <th>Dátum a čas testu</th>
            <th>Show</th>
        </tr>
        <?php
            showScores();
        ?>
    </table>
</body>
</html>