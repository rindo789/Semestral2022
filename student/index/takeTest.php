<?php
session_start();
include "../include/take_test.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>

<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/test.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <title>Test</title>
</head>
<body>
<nav id="menu">
        <a href="student.php">Domov</a>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
</nav>
<form action='../include/takeTest.inc.php' method='POST' id="test_form">

<?php
    loadTestStudent($_SESSION["testIdToEdit"]);
?>

<button type="submit" name="submit">Odošli odpoveď</button>
</form>

</body>
</html>
