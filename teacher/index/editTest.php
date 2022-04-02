<!DOCTYPE html>
<?php
include '../include/teach_functions.inc.php';
include '../../login/include/loginFunctions.inc.php';
session_start();
loginCheck();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/teacher.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body>
<script src="../../js/create1.js"></script>
<div id="menu"><a href="teacher.php">Domov</a></div>
<div id="inside">
    <form action='../include/newTest.inc.php' method='POST' id="test_form">
        <?php
            echo "<h1>".$_SESSION["testName"]."</h1>"; 
            echo "skupina: ";
            echoGroups();    
            echo "<br>";    
        ?>
        
        <button type="button" onclick="CreateQuestion('one')">Jeden výber</button>
        <button type="button" onclick="CreateQuestion('multi')">Výber viac</button>
        <button type="button" onclick="CreateQuestion('text')">Napíš odpoved</button>
        <!--<button type="button" id="counter" value="0" style="display: none;"></button>
        <button type="button" id="real_counter" value="0" style="display: none;"></button> -->
        <button type="submit" name="saveTest" id="submit">Ulož zmeny</button>

        <?php
            echo loadTestTeacher($_SESSION["testIdToEdit"]);
        ?>
        <script>showStart();</script>
        <fieldset id="settings">
            <!--<p>Opakovateľný</p>
            <input type="checkbox">-->
            <p>Dátum a čas spustenia</p>
            <input type="datetime-local" name="date_time_on">
            
            <br><br>
            <p>Dátum a čas ukončenia</p>
            <input type="datetime-local" name="date_time_off">
        </fieldset>
    </form>
    </div>        
</body>
</html>