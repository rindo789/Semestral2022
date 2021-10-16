<?php
session_start();
include '../include/teach_functions.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/tstyle.css">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th>Id</th>
            <th>Nazov</th>
            <th>Show</th>
            <th>Delete</th>
        </tr>
        <?php
            showGroups($_SESSION["TID"]);
        ?>
        <tr>
            <td colspan="4" id="new_sGroup_button">
                <button onclick="new_sGroup()">Vytvoriť novú skupinu</button>
            </td>
            
            <td id="new_group">
                <form action="../include/studentList.inc.php" method="post">
                    <input type="text" name="group_name" placeholder="názov skupiny">
                    <input type="submit" name="newGroup">
                </form>
            </td>
            
            <script>
                function new_sGroup() 
                {
                    document.getElementById('new_group').colSpan = "4";
                    document.getElementById('new_group').style.display = 'table-cell';
                    document.getElementById('new_sGroup_button').style.display = 'none';
                }
           </script>
        </tr>
    </table>
</body>
</html>