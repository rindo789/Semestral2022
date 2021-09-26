<?php
session_start();
require_once "../../main/dbh.inc.php";
require_once "teach_functions.inc.php";

if (isset($_POST["newTest"]))
{
    $_SESSION["testName"] = $_POST["test_name"];

    newTest($_SESSION["testName"],$_SESSION["TID"]);
    
    $_SESSION["testIdToEdit"] = checkNewTestId($_SESSION["TID"]);
    
    header("location: ../index/newTest.php");
    exit();
} else if (isset($_POST["saveTest"]))
{
    saveTest($_SESSION["testName"],$_SESSION["testIdToEdit"]);
    header("location: teacher.inc.php?testId=".$_SESSION["testIdToEdit"]."&state=show");
    exit();
} 
else {
    header("location: ../index/teacher.php");
    exit();
}


?>