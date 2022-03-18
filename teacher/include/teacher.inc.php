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


$state = $_GET["state"];


if (isset($_GET["testId"]) && isset($state)){
    $testId = $_GET["testId"];
//kontrola či učitelovy patrí test a či existuje
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT ucitel_id_uci from testy where id_test = ?");
    $stmt->bind_param("i",$testId);
    $stmt->execute();    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);
    //či test existuje
    if (mysqli_num_rows($result)==0){
        header("location: ../index/teacher.php?error=testnotfound");
        exit();
    }    
    //či patrí
    if ($row['ucitel_id_uci'] != $_SESSION['TID']){
        header("location: ../index/teacher.php?error=testTIDnoMatch");
        exit();
    }

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
} else if ($_GET["groupId"] && isset($state)) {
    $groupId = $_GET["groupId"];
    //kontrola či učitelovy patrí skupina a či existuje
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT teacher_id from groups where id_group = ?");
    $stmt->bind_param("i",$groupId);
    $stmt->execute();    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);
    //či skupina existuje
    if (mysqli_num_rows($result)==0){
        header("location: ../index/group.php?error=groupnotfound");
        exit();
    }    
    //či patrí
    if ($row['teacher_id'] != $_SESSION['TID']){
        header("location: ../index/group.php?error=groupTIDnoMatch");
        exit();
    }

    if ($state == "delete") {
        $_SESSION["groupEdit"] = $_GET["groupId"];
        groupDelete($_GET['groupId']);
        header('location: ../index/group.php');
        exit();

    } else if ($state == "show") {
        $_SESSION["groupEdit"] = $_GET["groupId"];
        header("location: ../index/editGroup.php");
        exit();
    }
    exit();
}
?>