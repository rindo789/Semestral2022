<?php

require_once "../../main/dbh.inc.php";
require_once "../include/studentFunctions.inc.php";
session_start();

if (isset($_POST["submit"]))
{
    checkAnsw($_SESSION["testIdToEdit"],$_POST["answer"]);
    echo "<br><br>";
    //print_r($_POST["answer"]);
    //echo $_POST["answer"];
    //sendAnsw($_POST["answer"]);
}
else {
    $_SESSION["testIdToEdit"] = $_GET["testId"];
    header("location: ../index/takeTest.php");
    exit();
}

?>