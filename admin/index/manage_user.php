<?php
session_start();
include_once '../../login/include/loginFunctions.inc.php';
include_once '../include/manage_user.inc.php';


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
    <div id="menu">
        <a href="../../login/include/singout.inc.php">Odhlasiť sa</a>
    </div>

    <div class="manage_bar">
        <a href="manage_user.php">Manažér užívateľov</a>
        <a href="manage_group.php">Manažér skupín</a>
        <a href="manage_tests.php">Manažér testov</a>
        <a href="manage_results.php">Manažér hodnotení</a>
    </div>
    
    <a href="manage_userNew.php"><button>Vytvoriť užívateľa</button></a>

    <div class="search_user">
    <br><br>
        <div class="search_bar">
            <h3>Vyhladaj</h3>
            <br>
            <input type="text" id="user_name" placeholder="Zadaj meno užívateľa">
            <input type="button" onclick="searchUser()" value="Hľadaj">
        </div>
    <br>
    <?php
    if (isset($_SESSION["new_pass"])){
        echo "Nové heslo užívateľa je: ".$_SESSION["new_pass"]."<br>";
        $_SESSION["new_pass"] = null;
    } else $_SESSION["new_pass"] = null;
    ?>
        
        <table id="search_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nick</th>
                    <th>Meno Priezvisko</th>
                    <th>Manažér</th>
                    <th>Typ</th>
                    <th>ID Typ</th>
                </tr>
            </thead>
            <tbody>
                <?php echo showUsers(); ?>
            </tbody>
        </table>

    </div>

    
</body>

</html>