<?php
session_start();
include "../include/studentFunctions.inc.php";
//include "../../teacher/include/teach_functions.inc.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../teacher/css/teacher_style.css">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th>Id</th>
            <th>Nazov</th>
            <th>Show</th>
        </tr>
        <?php
        showTests();
        ?>
    </table>
</body>
</html>