<?php
if (isset($_POST["submit"]))
{
    $nick = $_POST["userId"];
    $heslo = $_POST["passWrd"];

    require_once "../../main/dbh.inc.php";
    require_once "loginFunctions.inc.php";

    if (missingInputLogin($nick,$heslo) !== false)
    {
        header("location: ../index/login.php?error=missingInput");
        exit();
    }
    if (invalidUserIDLogin($nick) !== false)
    {
        header("location: ../index/login.php?error=invalidID");
        exit();
    }
    loginUser ($nick,$heslo);
    session_start();
    checkUserType();
} else {
    header("location: ../index/login.php", true);
    exit();
}
?>