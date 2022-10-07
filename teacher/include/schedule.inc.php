<?php
session_start();
include "../../main/dbh.inc.php";

//print_r($request_json);

$start = $_GET["date_start"];
$end =  $_GET["date_end"];
$test_type = $_GET["test_type"];

$conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO schedule (id_test, zaciatok, koniec, test_type) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $_SESSION["testIdToEdit"], $start, $end, $test_type);
    if(!$stmt->execute()){
        echo "bad request";
    } else echo "šicko good";
CloseCon($conn);
?>