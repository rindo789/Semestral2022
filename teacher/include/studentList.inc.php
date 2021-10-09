<?php
session_start();
require_once "../../main/dbh.inc.php";
require_once "teach_functions.inc.php";

if (isset($_POST["newGroup"]))
{
    newGroup($_POST["group_name"],$_SESSION["TID"]);

    header("location: ../index/studentList.php");
    exit();
}
else {
    header("location: ../index/teacher.php");
    exit();
}


?>