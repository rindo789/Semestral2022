<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div id="flex_inside">
    <form action="../include/login.inc.php" method="POST">
        <div id="divider_login">
            <h1>Prihlásiť sa</h1>
            <p>Užívateľské meno</p>
            <input type="text" placeholder="Nick" name="userId">
            <p>Heslo</p>
            <input type="password" placeholder="Password" name="passWrd">
            <input type="submit" name="submit" value="Prihlásiť sa" class="submiter">
        </div>        
    </form>
    <?php include "../include/loginFunctions.inc.php";
        errorEcho();
    ?>
    </div>
    
</body>
<footer>

</footer>
</html>