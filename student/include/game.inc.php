<?php

require_once "../../main/dbh.inc.php";
require_once "student_check.php";
require_once "check_test.php";
require_once "gameMeta.php";

//session_start();
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
} else if (isset($_POST["submit"])){
    
    $active_test = testActive();
    if (!isset($active_test)){
        header("location: ../index/student.php?testIsNotActive"); 
        exit();
    }
    $answerId = saveAnswer($_POST["answer"],$active_test);
    checkAnsw($_POST["answer"],$answerId);
    scoreAns($answerId);
    saveGame();
    header("location: ../index/student.php"); 
    exit();
} else {
    header("location: ../index/student.php");
    exit();
}

?>