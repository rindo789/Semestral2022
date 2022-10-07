<?php
include '../include/test_func.php';
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
    <h1>Vaše testy</h1>
    <table id="tests">
        <tr>
            <th class="table_name">Nazov</th>
            <th></th>
            <th></th>
        </tr>
        <?php
            showTests($_SESSION["TID"]);
        ?>
        <tr>
            <td colspan="3" id="new_test_button">
                <button onclick="new_test()">nový test</button>
            </td>

            <td id="new_test">
                <form action="../include/newTest.inc.php" method="post">
                    <input type="text" name="test_name" placeholder="názov testu">
                    <input type="submit" name="newTest">
                </form>
            </td>

            <script>
                function new_test() {
                    document.getElementById('new_test').colSpan = "4";
                    document.getElementById('new_test').style.display = 'table-cell';
                    document.getElementById('new_test_button').style.display = 'none';
                }
            </script>
        </tr>
    </table>
</body>

</html>