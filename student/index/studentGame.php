<?php
session_start();
include "../include/studentFunctions.inc.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>

<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../js/game6.js"></script>
    <title>Test</title>
</head>
<body onload="hideAll();">
<a href="student.php">Domov</a>
<p id="score"></p>
<form action='../include/takeTest.inc.php' method='POST' id="test_form">

<?php
loadTestStudent($_SESSION["testIdToEdit"]);
?>

<button type="submit" name="submit" id="submit">submit</button>

<input type="hidden" id="response">
<input type="hidden" name="score">
<input type="hidden" name="multiply">
<input type="hidden" name="timer">
</form>
<button type="button" id="next" onclick="sendData();nextQuestion();">Next question</button>

</body>
</html>
