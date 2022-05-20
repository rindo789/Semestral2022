<?php
session_start();
include_once "../../main/dbh.inc.php";

$conn = OpenCon();
$stmt = $conn->prepare("SELECT id_group, group_name FROM groups WHERE teacher_id = ?");
$stmt->bind_param("i",$_REQUEST["teacher_id"]);
$stmt->execute();
$result = $stmt->get_result();
CloseCon($conn);

    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
 
?>