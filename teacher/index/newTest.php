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
    <link rel="stylesheet" href="../css/tstyle.css">
    <title>Document</title>
</head>
<body>
<a href="teacher.php">Domov</a>
        <script src="../../js/create1.js"></script>
    
    <form action='../include/newTest.inc.php' method='POST' id="test_form">
        <?php 
            echo "<p>".$_SESSION["testName"]."</p>";
            echo "skupina: ";
            echoGroups();    
            echo "<br>";  
        ?>
        <textarea name='opis' placeholder="opis" form="test_form"></textarea> <br>
            <button type="button" onclick="CreateQuestion('one')">Jeden výber</button>
            <button type="button" onclick="CreateQuestion('multi')">Vyber mnoho</button>
            <button type="button" onclick="CreateQuestion('text')">Napis odpoved</button> <br>

            <!--<button type="button" id="counter" value="0" style="display: none;"></button>
            <button type="button" id="real_counter" value="0" style="display: none;"></button> -->
            <button id="submitButton"type="submit" name="saveTest">submit</button>
        <!--<p onclick="showSettings()">Nastavenia</p>-->
        <fieldset id="settings">
            <!--<p>Opakovateľný</p>
            <input type="checkbox">-->
            <br>
            <p>Dátum a čas spustenia</p>
            <input type="datetime-local" name="date_time_on">
            <br><br>
            <p>Dátum a čas ukončenia</p>
            <input type="datetime-local" name="date_time_off">
        </fieldset> 
    </form>
        
</body>
</html>