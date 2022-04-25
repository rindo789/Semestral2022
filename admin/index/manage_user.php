<?php
include_once '../../login/include/loginFunctions.inc.php';
include_once '../include/manage_user.inc.php';

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

    <script src="../js/search_user.js"></script>
</head>


<body>
    <nav>
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
        <a href="index.php">Domov</a>
    </nav>
    <div class="search_user">
        <div class="search_bar">
            <input type="text" id="user_name" placeholder="Zadaj meno užívateľa">
            <input type="button" onclick="searchUser()" value="Hľadaj">
        </div>
        <a href="manage_userNew.php"><button>Vytvoriť užívateľa</button></a>
        <table id="search_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nick</th>
                    <th>Meno Priezvisko</th>
                    <th>Manažér</th>
                    <th>Typ</th>
                </tr>
            </thead>
            <tbody>
                <?php echo showUsers(); ?>
            </tbody>
        </table>
    </div>
</body>

</html>