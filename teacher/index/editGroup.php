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
    <title>Document</title>
</head>
<body>
<script src="../../js/group4.js"></script>
<script src="../../js/ajax1.js"></script>
<a href="teacher.php">Domov</a>
<p id="txtHint"></p>

    <form action='../include/newTest.inc.php' method='POST' id="group_form">
        
        <?php
            loadStudents(); 
        ?>
        <button type="button" onclick="AddStudent()">Pridaj študenta</button>

        <input type="submit" name="saveGroup">
    </form>
        
</body>
</html>