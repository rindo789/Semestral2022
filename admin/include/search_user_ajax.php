<?php
include "../../main/dbh.inc.php";

$user_name = $_REQUEST["user_name"];
$prompt = "%".$user_name."%";



    $conn = OpenCon();
    $sql = 'SELECT id_uzivatel, nickname, meno_priezvysko, manager_id,
    CASE
    WHEN (SELECT s.user_student_id FROM student s WHERE s.user_student_id = u.id_uzivatel) = u.id_uzivatel THEN "ŠTUDENT"
    WHEN (SELECT t.uzivatelia_id_uzivatel FROM ucitel t WHERE t.uzivatelia_id_uzivatel = u.id_uzivatel) = u.id_uzivatel THEN "UČITEL"
    END AS type
    FROM uzivatelia u WHERE meno_priezvysko LIKE ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$prompt);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    
    CloseCon($conn);
    $row = $result->fetch_all(MYSQLI_ASSOC);
    //print_r($row);
    echo json_encode($row);

    /*$send = [];
    while($row = $result->fetch_assoc()){
        array_push($send,$row["meno_priezvysko"]);
    }
    
    $send = json_encode($send);

    echo $send;*/
?>