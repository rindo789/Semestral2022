<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="teach_style.css">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th>id</th>
            <th>nazov</th>
            <th>datum vytvorenia</th>
            <th>options</th>
        </tr>
        <?php
            include 'teacher_functions.php';
            showTests();
            ?>
        <tr>
            <td colspan="4" id="new_test_button">
                <button onclick="new_testicles()">nový test</button>
            </td>
            
            <td id="new_test">
                <form action="new_test.php" method="post">
                    <input type="text" name="test_name" placeholder="názov testu">
                    <input type="submit">
                </form>
            </td>

            <a href="new_test.php">test</a>
            
            <script>
                function new_testicles() 
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