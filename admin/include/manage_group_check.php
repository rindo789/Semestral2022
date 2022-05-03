<?php
session_start();
include_once "manage_group.inc.php";
include_once '../../login/include/loginFunctions.inc.php';
loginCheck();

if (isset($_GET["state"]) && $_GET["state"]=="delete"){
    deleteGroup($_GET["group_id"]);
    header("location: ../index/manage_group.php");
    exit();
} else if (isset($_POST["saveGroup"]) || isset($_SESSION["groupEdit"]) || isset($_POST["student"])){
    saveGroup($_POST["student"]);
    $_SESSION["groupEdit"] = null;
    header("location: ../index/manage_group.php");
    exit();
}

?>