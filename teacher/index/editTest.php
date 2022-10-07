<!DOCTYPE html>
<?php
include '../include/test_func.php';
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <script src="../../js/create1.js"></script>
    <script src="../../js/set_date.js"></script>

    <nav id="menu">
        <a href="teacher.php">Testy</a>
        <a href="group.php">Skupiny</a>
        <a href="scoreTest.php">Hodnotenia</a>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>
    
    <div id="inside">
        <form action='../include/newTest.inc.php' method='POST' id="test_form">
            <?php
            echo "<h1>" . $_SESSION["testName"] . "</h1>";
            echo "skupina: ";
            echoGroups();
            echo "<br>";
            echo "opis: ";
            echoDescription();
            echo "<br>";
            ?>
            <div id="flex_inside">
                <button type="button" onclick="CreateQuestion('one')">Jeden výber</button>
                <button type="button" onclick="CreateQuestion('multi')">Výber viac</button>
                <button type="button" onclick="CreateQuestion('text')">Napíš odpoved</button>
                <button type="submit" name="saveTest" id="submit">Ulož zmeny</button>
                <button type="button" onclick="showSchedule('classic');">Spusti Klasic</button>
                <button type="button" onclick="showSchedule('game');">Spusti Hru</button>
            </div>

            <?php
            echo loadTestTeacher($_SESSION["testIdToEdit"]);
            ?>
            <script>
                showStart();
            </script>

        </form>
    </div>

    <div id="schedule_window">
        <span onclick="hideSchedule();">x</span>
        <form action="../include/schedule.inc.php" method="post" id="set_date">
            <p>Spustit test</p>
            <p>Dátum a čas spustenia</p>
            <input type="datetime-local" name="date_time_on" id="date_on">

            <br><br>
            <p>Dátum a čas ukončenia</p>
            <input type="datetime-local" name="date_time_off" id="date_off">
            <br>
            <button type="submit" form="set_date" onclick="setSchedule();" id="date_button">Set date</button>
        </form>
        <p id="wrong_dates">Dátum ukončenia nemôže byť skorší ako dátum začatia</p>
    </div>

    <div id="schedule_response">
        <p id="schedule_ok">Test bol uspešne naplánovaný!</p>
        <p id="schedule_error">Pri naplánovaní testu sa stala chyba!</p>
    </div>

    <script>
        document.getElementById("date_button").addEventListener("click", function(event) {
            event.preventDefault()
        });
    </script>

</body>

</html>
