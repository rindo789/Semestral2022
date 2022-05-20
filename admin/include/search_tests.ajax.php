<?php
session_start();
include_once "../../main/dbh.inc.php";

$search = $_REQUEST["search_prompt"];
$prompt = "%".$search."%";

$conn = OpenCon();
$stmt = $conn->prepare("SELECT id_test, nazov_testu, ucitel_id_uci as teacher_id FROM testy t
JOIN ucitel uc ON uc.id_uci = t.ucitel_id_uci
JOIN uzivatelia uz ON uc.uzivatelia_id_uzivatel = uz.id_uzivatel
WHERE manager_id = ? AND (id_test LIKE ? OR nazov_testu LIKE ? OR ucitel_id_uci LIKE ?)");
$stmt->bind_param("isss",$_SESSION["AID"],$prompt,$prompt,$prompt);
if (!$stmt->execute()) {
    echo "Chyba pri hladaní";
    CloseCon($conn);
    return;
}
$result = $stmt->get_result();
if (mysqli_num_rows($result) == 0) {
    echo "Chyba pri hladaní";
    CloseCon($conn);
    return;
}
CloseCon($conn);

echo json_encode($row = $result->fetch_all(MYSQLI_ASSOC));

?>