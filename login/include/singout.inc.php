<?php 
require_once "../../main/dbh.inc.php";
require_once "loginFunctions.inc.php";
session_start();

session_unset();
session_destroy();
header("location: ../index/login.php");
exit();
?>