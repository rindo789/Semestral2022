<?php
session_start();
require_once "dbh.inc.php";
require_once "teach_functions.inc.php";

if (empty($_SESSION["sessionNick"]) || empty($_SESSION["sessionUID"]) || empty($_SESSION["sessionTID"]))
{
    session_unset();
    session_destroy();
    header("location: ../index/login.php");
    exit();
}

$testId = $_GET["testId"];
$state = $_GET["state"];

if ($state == "delete")
{
    TeacherDeleteTest($testId);
    header("location: ../index/teacher.php");
}
?>