<?php
session_start();
include "../../main/dbh.inc.php";

$group_name = $_REQUEST["group_name"];
$prompt = "%".$group_name."%";

    $conn = OpenCon();
    $sql = 'SELECT g.id_group, g.group_name, g.teacher_id FROM groups g
    JOIN ucitel u ON u.id_uci = g.teacher_id
    JOIN uzivatelia uz ON uz.id_uzivatel = u.uzivatelia_id_uzivatel
    WHERE uz.manager_id = ? AND (id_group LIKE ? OR group_name LIKE ? OR teacher_id LIKE ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss",$_SESSION["AID"], $prompt, $prompt, $prompt);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    CloseCon($conn);
    
    $row = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($row)){
        echo "Nenašla sa žiadna skupina";
        return;
    }

    echo json_encode($row);
?>