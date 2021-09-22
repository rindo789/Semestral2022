<?php
session_start();
require_once "dbh.inc.php";
require_once "teach_functions.inc.php";

if (isset($_POST["newTest"]))
{
    $_SESSION["testName"] = $_POST["test_name"];

    newTest($_SESSION["testName"],$_SESSION["sessionTID"]);
    
    $_SESSION["testIdToEdit"] = checkNewTestId($_SESSION["sessionTID"]);
    
    header("location: ../index/newTest.php");
    exit();
} else if (isset($_POST["saveTest"]))
{
    saveTest($_SESSION["testName"],$_SESSION["testIdToEdit"]);
    header("location: ../index/teacher.php");
    exit();
} 
else {
    header("location: ../index/teacher.php");
    exit();
}


?>