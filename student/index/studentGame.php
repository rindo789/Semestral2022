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
    <script src="../../js/game.js"></script>
    <link rel="stylesheet" href="../css/student.css">
    <title>Test</title>
</head>
<body onload="hideAll();timerStart()">
<a href="student.php">Domov</a>
<p id="score">0</p>
<p id="multyplier">0</p>
<form action='../include/game.inc.php' method='POST' id="test_form" name="test_form" onkeydown="return event.key != 'Enter';">

<?php
loadTestStudent($_SESSION["testIdToEdit"]);
?>

<button type="submit" name="submit" id="submit">Ukončiť test</button>

<input type="hidden" name="score" id="score_send">
<input type="hidden" name="multiply" id="multiply_send">
<input type="hidden" name="full_time" id="full_time">
<input type="hidden" name="short_time" id="short_time">
<input type="hidden" name="good_answer" id="max_answers">

</form>
<button type="button" id="next" onclick="sendData();nextQuestion();">Next question</button>

</body>
</html>
