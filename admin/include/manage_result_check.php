<?php
session_start();
include_once "manage_result.inc.php";
include_once '../../login/include/loginFunctions.inc.php';
loginCheck();

if (isset($_GET["result_id"]) || $_GET["state"] == "delete"){
    deleteAnswer($_GET["result_id"]);
    header("location: ../index/manage_result.php");
    exit();
}
?>