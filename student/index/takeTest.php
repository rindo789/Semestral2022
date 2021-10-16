<?php
session_start();
include "../include/studentFunctions.inc.php";
?>

<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../teacher/css/tstyle.css">
    <title>Document</title>
</head>
</head>
<body>
<form action='../include/takeTest.inc.php' method='POST' id="test_form">

<?php
loadTestStudent($_SESSION["testIdToEdit"]);
?>

<button type="submit" name="submit">submit</button>
</form>

</body>
</html>
