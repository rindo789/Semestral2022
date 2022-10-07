<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8"><link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div id="flex_inside">
        <form action="../include/singup.inc.php" method="POST">
        <div id="divider_singup">
            <h1>Registrácia</h1>
            <p>Meno a Priezvisko</p>
            <input type="text" placeholder="Meno a Priezvisko" name="userName">
            <p>Užívateľské meno</p>
            <input type="text" placeholder="Užívateľské meno" name="userId">
            <p>Email</p>
            <input type="email" placeholder="Email" name="email">
            <p>Heslo</p>
            <input type="password" placeholder="Password" name="passWrd">
            <p>Heslo znova</p>
            <input type="password" placeholder="Zadajte heslo znova" name="passWrdRep">
            <p>Zvolte typ užívateľa</p>
            <select name="userType">
                <option default>Student</option>
                <option>Teacher</option>
                <option>Admin</option>
            </select>
            <input type="submit" name="submit" value="Registrovať sa" class="submiter">
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