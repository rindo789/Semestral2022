<?php
include "../../main/dbh.inc.php";

$q = $_REQUEST["q"];



    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT meno_priezvysko FROM uzivatelia WHERE id_uzivatel = (SELECT user_student_id from student WHERE id_student = ?)");
    $stmt->bind_param("i",$q);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);
    if ($row == null){
        echo "Študent sa nenašiel";
        return;
    }

    echo $row["meno_priezvysko"];
?>