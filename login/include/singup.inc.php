<?php
if (isset($_POST["submit"]))
{
    $meno = $_POST["userName"];
    $nick = $_POST["userId"];
    $email = $_POST["email"];
    $heslo = $_POST["passWrd"];
    $hesloZnova = $_POST["passWrdRep"];
    $typ = $_POST["userType"];

    require_once "../../main/dbh.inc.php";
    require_once "loginFunctions.inc.php";

    if (missingInput($meno,$nick,$email,$heslo,$hesloZnova,$typ) !== false)
    {
        header("location: ../index/signup.php?error=missingInput");
        exit();
    }
    if (invalidUserID($nick) !== false)
    {
        header("location: ../index/signup.php?error=invalidID");
        exit();
    }
    if (existUserID($nick) !== false)
    {
        header("location: ../index/signup.php?error=userExists");
        exit();
    }
    if (invalidEmail($email) !== false)
    {
        header("location: ../index/signup.php?error=invalidEmail");
        exit();
    }
    if (pwdMissmatch($heslo,$hesloZnova) !== false)
    {
        header("location: ../index/signup.php?error=pwdMissmatch");
        exit();
    }

    createUser($meno,$nick,$email,$heslo,$typ);
    header("location: ../index/login.php");
    exit();
}
else
{
    header("location: ../index/signup.php", true);
    exit();
}

?>