<?php
include '../include/group_func.php';
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
    <!--<a href="studentList.php">My Students</a>-->
    <nav id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </nav>
    <div class="manage_bar">
        <a href="teacher.php">Testy</a>
        <a href="group.php">Skupiny</a>
        <a href="scoreTest.php">Hodnotenia</a>
        <div id="type_number">
            <?php
                echo "<p>ID učiteľa: ".$_SESSION["TID"]."</p>"
            ?>
        </div>
    </div>
    <table id="groups">
        <h1>Vaše skupiny</h1>
        <tr>
            <th>Nazov</th>
            <th></th>
            <th></th>
        </tr>
        <?php
            showGroups();
        ?>
        <tr>
            <td colspan="3" id="new_group_button">
                <button onclick="new_group()">nová skupina</button>
            </td>

            <td id="new_group">
                <form action="../include/newTest.inc.php" method="post">
                    <!-- ide to cez new test funkcie -->
                    <input type="text" name="group_name" placeholder="názov skupiny">
                    <input type="submit" name="newGroup">
                </form>
            </td>

            <script>
                function new_group() {
                    document.getElementById('new_group').colSpan = "4";
                    document.getElementById('new_group').style.display = 'table-cell';
                    document.getElementById('new_group_button').style.display = 'none';
                }
            </script>
        </tr>
    </table>
</body>

</html>