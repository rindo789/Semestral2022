<?php
include "../../main/dbh.inc.php";
session_start();

$user_name = $_REQUEST["user_name"];
$prompt = "%".$user_name."%";

    $conn = OpenCon();
    $sql = 'SELECT id_uzivatel, nickname, meno_priezvysko, manager_id,
    CASE
    WHEN (SELECT s.user_student_id FROM student s WHERE s.user_student_id = u.id_uzivatel) = u.id_uzivatel THEN "ŠTUDENT"
    WHEN (SELECT t.uzivatelia_id_uzivatel FROM ucitel t WHERE t.uzivatelia_id_uzivatel = u.id_uzivatel) = u.id_uzivatel THEN "UČITEL"
    END AS type
    FROM uzivatelia u WHERE meno_priezvysko LIKE ? AND id_uzivatel != ? AND manager_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii",$prompt, $_SESSION["UID"], $_SESSION["UID"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    CloseCon($conn);
    
    $row = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($row);
?>