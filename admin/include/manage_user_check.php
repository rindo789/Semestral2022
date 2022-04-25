<?php
include_once "../../main/dbh.inc.php";
include_once "manage_user.inc.php";
session_start();
loginCheck();

if (isset($_POST["submit"])){
    
    addUser();
    header("location: ../manage_user.php");
    exit();

} else if ($_GET["state"] == "show"){

    $_SESSION["user_edit"] == $_GET["user_id"];
    header("location: ../manage_userNew.php");
    exit();

} else if ($_GET["state"] == "delete"){

    exit();
}

?>