<!DOCTYPE html>
<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/teacher_style.css">
    <title>Document</title>
</head>
<body>
        
        <script src="../../js/create20.js"></script>
    
    <form action='../include/newTest.inc.php' method='POST' id="test_form">
        <?php 
            echo "<p>".$_SESSION["testName"]."</p>";
        ?>
        <textarea name='opis' placeholder="opis" form="test_form"></textarea> <br>
            <button type="button" onclick="CreateQuestion('one')">Jeden výber</button>
            <button type="button" onclick="CreateQuestion('multi')">Vyber mnoho</button>
            <button type="button" onclick="CreateQuestion('text')">Napis odpoved</button> <br>

            <!--<button type="button" id="counter" value="0" style="display: none;"></button>
            <button type="button" id="real_counter" value="0" style="display: none;"></button> -->
            <button type="submit" name="saveTest">submit</button>
    </form>
        
</body>
</html>