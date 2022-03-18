<?php

require_once "../../main/dbh.inc.php";
require_once "../include/studentFunctions.inc.php";

session_start();

//pri odoslani testu
if (isset($_POST["submit"]))
{
    $active_test = testActive();
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

    if (studentBelong("score") !== true){
        header("location: ../index/student.php?wrongStudent"); 
        exit();
    }

    header("location: ../index/scoreTest.php");
    exit();
}
//pri ukázaní testu
else if (isset($_GET["testId"])) {
    $_SESSION["testIdToEdit"] = $_GET["testId"];

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