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
    <link rel="stylesheet" href="../css/test.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/game.css">
    <title>Test</title>
</head>
<body onload="hideAll();timerStart()">
<nav id="menu">
        <a href="student.php">Domov</a>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
</nav>
<div id="score_flex">
    <p id="score">0</p>
    <p id="multyplier">Násobok: 0</p>
</div>

    
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
