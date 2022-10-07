<?php
session_start();
require_once "../../main/dbh.inc.php";
require_once "group_func.php";
require_once "test_func.php";

if (isset($_POST["newTest"])){
    $_SESSION["testName"] = $_POST["test_name"];

    newTest($_SESSION["testName"],$_SESSION["TID"]);
    
    $_SESSION["testIdToEdit"] = checkNewTestId($_SESSION["TID"]);
    
    header("location: ../index/newTest.php");
    exit();
    
} else if (isset($_POST["saveTest"])){
    saveTest($_SESSION["testName"],$_SESSION["testIdToEdit"]);
    header("location: test_states.php?testId=".$_SESSION["testIdToEdit"]."&state=show");
    exit();

} else if (isset($_POST["newGroup"])){
    newGroup($_POST["group_name"], $_SESSION["TID"]);
    header("location: ../index/editGroup.php");
    exit();

} else if (isset($_POST["saveGroup"])){
    saveGroup($_POST["student"]);
    header("location: ../index/editGroup.php");
    exit();
}
else {
    header("location: ../index/teacher.php");
    exit();
}


?>