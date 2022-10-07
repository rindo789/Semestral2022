<?php
session_start();
include_once "../../main/dbh.inc.php";
include_once "manage_user.inc.php";
include_once '../../login/include/loginFunctions.inc.php';

loginCheck();

if (isset($_POST["submit"])){
    
    addUser();
    header("location: ../index/manage_user.php");
    exit();

} else if (isset($_GET["state"]) && $_GET["state"] == "show"){

    $_SESSION["user_edit"] == $_GET["user_id"];
    header("location: ../index/manage_userEdit.php");
    exit();

} else if (isset($_GET["state"]) && $_GET["state"] == "delete"){
    deleteUser((int)$_GET["user_id"]);
    header("location: ../index/manage_user.php");
    exit();
} else if (isset($_POST["update"])){
    updateUser($_GET["user_id"]);
    header("location: ../index/manage_user.php");
    exit();
} else if (isset($_POST["pass"])){
    resetUser($_GET["user_id"]);
    header("location: ../index/manage_user.php");
    exit();
}

?>