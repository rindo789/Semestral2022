<!DOCTYPE html>
<?php
include '../include/group_func.php';
include '../../login/include/loginFunctions.inc.php';
session_start();
loginCheck();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/harrystyles.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body onload="countFields();">
<script src="../../js/group4.js"></script>
<script src="../../js/ajax1.js"></script>
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
<p id="txtHint"></p>

    <form action='../include/newTest.inc.php' method='POST' id="group_form">
        
        <?php
            showGroupName();
            loadStudents(); 
        ?>

        <div id="flex_buttons">
            <button type="button" onclick="AddStudent()" id="add_member">Pridaj študenta</button>
    
            <input type="submit" name="saveGroup" id="group_submit">
        </div>
    </form>
        
</body>
</html>