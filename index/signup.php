<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form action="../include/singup.inc.php" method="POST">
        <p>Meno a Priezvisko</p>
        <input type="text" placeholder="Meno a Priezvisko" name="userName">
        <p>Username</p>
        <input type="text" placeholder="Nick" name="userId">
        <p>Email</p>
        <input type="email" placeholder="Email" name="email">
        <p>Heslo</p>
        <input type="password" placeholder="Password" name="passWrd">
        <p>Heslo znova</p>
        <input type="password" placeholder="Repeat Password" name="passWrdRep">
        <p>Zvolte typ užívatela</p>
        <select name="userType">
            <option default>Student</option>
            <option>Teacher</option>
            <option>Admin</option>
        </select>
        <br>
        <input type="submit" name="submit" value="Sign Up">
    </form>
    <?php include "../include/functions.inc.php";
        errorEcho();
    ?>
</body>
<footer>

</footer>
</html>