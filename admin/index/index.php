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
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <title>Document</title>
</head>


<body>
    <nav>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>
<div class="manage_bar">
    <a href="manage_user.php">Manažér užívateľov</a>
    <a href="manage_group.php">Manažér skupín</a>
    <a href="manage_tests.php">Manažér testov</a>
    <a href="manage_result.php">Manažér hodnotení</a>
</div>
</body>

</html>