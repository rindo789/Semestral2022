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
    <link rel="stylesheet" href="../../teacher/css/tstyle.css">
    <title>Test</title>
</head>
<body>
<a href="student.php">Domov</a>
<form action='../include/takeTest.inc.php' method='POST' id="test_form">

<?php
loadTestStudent($_SESSION["testIdToEdit"]);
?>

<button type="submit" name="submit">submit</button>
</form>

</body>
</html>
