<?php
session_start();
require_once "../../main/dbh.inc.php";
require_once "check_test.php";
require_once "student_check.php";
require_once "take_test.php";


//pri odoslani testu
if (isset($_POST["submit"]))
{
    $active_test = $_SESSION["schedule"];
    if (!isset($active_test)){
        header("location: ../index/student.php?testIsNotActive"); 
        exit();
    }

    $answerId = saveAnswer($_POST["answer"],$active_test);
    checkAnsw($_POST["answer"],$answerId);
    scoreAns($answerId);
    header("location: ../index/student.php"); 
    exit();
} else if (isset($_GET["scoreId"])){
    $_SESSION["testIdToEdit"] = $_GET["scoreId"];
    $_SESSION["schedule"]= $_GET["schedule_id"];

    if (studentBelong("score") !== true){
        header("location: ../index/student.php?wrongStudent"); 
        exit();
    }

    if ($_GET["type"] == "game"){
        header("location: ../index/scoreGame.php");
        exit();
    }

    header("location: ../index/scoreTest.php");
    exit();
}
//pri ukázaní testu
else if (isset($_GET["testId"])) {
    $_SESSION["testIdToEdit"] = $_GET["testId"];
    $_SESSION["schedule"] = $_GET["schedule"];

    if (studentBelong("test") !== true){
        header("location: ../index/student.php?wrongStudent"); 
        exit();
    }

    /*if (testTaken($_SESSION["testIdToEdit"]) == true){
        header("location: ../index/student.php?testTaken"); 
        exit();
    }*/

    header("location: ../index/takeTest.php");
    exit();
} else {
    header("location: ../index/student.php");
    exit();
}

?>