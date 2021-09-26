<?php
session_start();
require_once "../../main/dbh.inc.php";
require_once "teach_functions.inc.php";

if (empty($_SESSION["Nick"]) || empty($_SESSION["UID"]) || empty($_SESSION["TID"]))
{
    session_unset();
    session_destroy();
    header("location: ../../login/index/login.php");
    exit();
}

$testId = $_GET["testId"];
$state = $_GET["state"];

if ($state == "delete")
{
    TeacherDeleteTest($testId);
    header("location: ../index/teacher.php");
    exit();
}
else if ($state == "show")
{
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT nazov_testu from testy where id_test = ?");
    $stmt->bind_param("i",$_GET["testId"]);
    $stmt->execute();    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);

    $_SESSION["testName"] = $row["nazov_testu"];
    $_SESSION["testIdToEdit"] = $_GET["testId"];
    header("location: ../index/editTest.php");
    exit();
}
?>