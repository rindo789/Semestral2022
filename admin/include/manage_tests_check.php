<?php
session_start();
include_once "../../main/dbh.inc.php";
include_once "manage_tests.inc.php";
include_once '../../login/include/loginFunctions.inc.php';
include '../../teacher/include/test_func.php';

loginCheck();

if (isset($_GET["test_id"]) && $_GET["state"] == "delete"){
    if (!checkRights($_GET["test_id"])){
        header("location: ../index/manage_tests.php");
    exit();
    }

    AdminDeleteTest($_GET["test_id"]);
    header("location: ../index/manage_tests.php");
    exit();
} else if (isset($_POST["saveTest"])){
    saveTest($_SESSION["testName"],$_SESSION["testIdToEdit"]);
    header("location: ../index/manage_testsEdit.php?test_id=".$_SESSION["testIdToEdit"]."&state=show");
    exit();
}
else if (isset($_POST["createTest"])){
    $test_id = createNewTest($_POST["test_name"],$_POST["selected_teacher"]);
    generateTest($_POST["test_name"],$test_id);
    header("location: ../index/manage_testsEdit.php?test_id=".$test_id."&state=show");
    exit();
}

?>