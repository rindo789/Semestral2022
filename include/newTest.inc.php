<?php
session_start();
require_once "dbh.inc.php";

if (!isset($_POST["newTest"]))
{
    header("location: ../index/teacher.php");
    exit();
}
$_SESSION["testName"] = $_POST["test_name"];
newTest($_SESSION["testName"],$_SESSION["sessionTID"]);
$_SESSION["testIdToEdit"] = checkNewTestId($_SESSION["sessionTID"]);
header("location: ../index/newTest.php");
exit();
?>