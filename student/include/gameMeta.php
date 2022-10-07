<?php
session_start();
require_once "../../main/dbh.inc.php";


function saveGame(){

    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO game (test_id, schedule_id, student_id, score, answers, multiplier, full_time, short_time) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("iiiiiiii",$_SESSION["testIdToEdit"], $_SESSION["schedule"],$_SESSION["SID"], $_POST["score"],$_POST["good_answer"],$_POST["multiply"],$_POST["full_time"],$_POST["short_time"]);
    if (!$stmt->execute()){
        echo "niečo je zle";
    } else echo "sent data!";
}

function checkAvailible(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT MAX(id_schedule) as latest FROM schedule WHERE id_test = ?");
    $stmt->bind_param("i",$_SESSION["testIdToEdit"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);

    return (int)$row["latest"];
}

?>