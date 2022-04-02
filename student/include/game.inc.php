<?php

require_once "../../main/dbh.inc.php";
require_once "../include/studentFunctions.inc.php";

session_start();
if (isset($_GET["testId"])) {
    $_SESSION["testIdToEdit"] = $_GET["testId"];

    if (studentBelong("test") !== true){
        header("location: ../index/student.php?wrongStudent"); 
        exit();
    }

    /*if (testTaken($_SESSION["testIdToEdit"]) == true){
        header("location: ../index/student.php?testTaken"); 
        exit();
    }*/

    header("location: ../index/studentGame.php");
    exit();
} else {
    header("location: ../index/student.php");
    exit();
}

?>