<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
</head>

<body>
    <form action="../include/login.inc.php" method="POST">
        <p>Meno</p>
        <input type="text" placeholder="Nick" name="userId">
        <p>Heslo</p>
        <input type="password" placeholder="Password" name="passWrd">
        <input type="submit" name="submit" value="Log In">
    </form>
    <?php include "../include/loginFunctions.inc.php";
        errorEcho();
    ?>
</body>
<footer>

</footer>
</html>