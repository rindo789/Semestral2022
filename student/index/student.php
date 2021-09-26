<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\teach_style.css">
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
        <tr>
            <td colspan="4" id="new_test_button">
                <button onclick="new_test()">nový test</button>
            </td>
            
            <td id="new_test">
                <form action="../include/newTest.inc.php" method="post">
                    <input type="text" name="test_name" placeholder="názov testu">
                    <input type="submit" name="newTest">
                </form>
            </td>
            
            <script>
                function new_test() 
                {
                    document.getElementById('new_test').colSpan = "4";
                    document.getElementById('new_test').style.display = 'table-cell';
                    document.getElementById('new_test_button').style.display = 'none';
                }
           </script>
        </tr>
    </table>
</body>
</html>