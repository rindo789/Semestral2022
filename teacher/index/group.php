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
    <title>Document</title>
</head>

<body>
    <!--<a href="studentList.php">My Students</a>-->
    <table id="groups">
        <tr>
            <th>Id</th>
            <th>Nazov</th>
            <th>Show</th>
            <th>Delete</th>
        </tr>
        <?php
            showGroups();
        ?>
        <tr>
            <td colspan="4" id="new_group_button">
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
    <a href="../../login/include/singout.inc.php">Odhlásiť sa</a>
</body>

</html>