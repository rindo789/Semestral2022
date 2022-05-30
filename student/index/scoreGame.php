<?php
session_start();
include "../include/show_tables.php";
include '../../login/include/loginFunctions.inc.php';
loginCheck();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/student.css">
    <link rel="stylesheet" href="../css/game.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <script src="../js/game_result.js"></script>
    <title>Hodnotenie</title>
</head>
<body onload='gameLeaderboard();'>
    <nav id="menu">
        <a href="student.php">Domov</a>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>

    <?php
        scoreTable();
    ?>

    <div id="game_leadeboard">
        <div id="score_container">
            <h1>Najväčšie skóre</h1>
            <div id="score">
                <div id="second">
                    <p>2</p>
                    <p>-</p>
                </div>
                <div id="first">
                    <p>1</p>
                    <p>-</p>
                </div>
                <div id="third">
                    <p>3</p>
                    <p>-</p>
                </div>
            </div>
            <div id="participants">
                    <p>-</p>
                    <p>-</p>
                    <p>-</p>
            </div>
            <div id="other_participants">
                <table>
                    <!--<tr>
                        <td>4.</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>-->
                </table>
            </div>
        </div>
        <div id="other_meta">
            <div id="multiplier">
                <h1>Najvačší násobiteľ</h1>
                <p>-</p>
                <p>-</p>
            </div>
            <div id="right_asnwer">
                <h1>Najviac správnych odpovedí</h1>
                <p>-</p>
                <p>-</p>
            </div>
            <div id="timer">
                <h1>Najrýchlešie dokončenie testu</h1>
                <p>-</p>
                <p>-</p>
            </div>
            <div id="timer_short">
                <h1>Najrýchlešia odpoveď na otázku</h1>
                <p>-</p>
                <p>-</p>
            </div>
        </div>
    </div>
</body>
</html>