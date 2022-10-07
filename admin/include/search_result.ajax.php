<?php
session_start();
include_once "../../main/dbh.inc.php";
if (isset($_REQUEST["show_result"])){
    getResult();
    return;
}

$search = $_REQUEST["search_prompt"];
$prompt = "%".$search."%";

$conn = OpenCon();
$stmt = $conn->prepare("SELECT o.id_odp, o.id_student, uz.meno_priezvysko, o.id_test, o.schedule_id FROM odpoved o
JOIN student s ON o.id_student = s.id_student
JOIN uzivatelia uz ON s.user_student_id = uz.id_uzivatel
WHERE uz.manager_id = ? AND (o.id_odp LIKE ? OR o.id_student LIKE ? OR uz.meno_priezvysko LIKE ? OR o.id_test LIKE ? OR o.schedule_id LIKE ?)");
$stmt->bind_param("isssss",$_SESSION["AID"],$prompt,$prompt,$prompt, $prompt, $prompt);
if (!$stmt->execute()) {
    echo "Chyba pri hladaní";
    CloseCon($conn);
    return;
}
$result = $stmt->get_result();
if (mysqli_num_rows($result) == 0) {
    echo "Nič nenašlo";
    CloseCon($conn);
    return;
}
CloseCon($conn);

echo json_encode($row = $result->fetch_all(MYSQLI_ASSOC));


function getResult(){
//najdi informácie o odpovedi
$conn = OpenCon();
$stmt = $conn->prepare("SELECT id_odp, id_test, id_student, schedule_id FROM odpoved WHERE id_odp = ?");
$stmt->bind_param("i", $_REQUEST["show_result"]);
if (!$stmt->execute()) {
    echo "Chyba pri hladaní";
    CloseCon($conn);
    return;
}
$result = $stmt->get_result();
if (mysqli_num_rows($result) == 0) {
    echo "Nič nenašlo";
    CloseCon($conn);
    return;
}
CloseCon($conn);
//uloz si dáta
$row = $result->fetch_assoc();

//najdi kolko bolo v teste otázok
$xml = simplexml_load_file("../../xml/answers.xml");
$test = $xml->xpath("//answer[@id=".$row["id_odp"]."]/question");
//a uloz to do arrayu s dátamy
$row["colspan"] = count($test);

//najdi odpoved študenta
$answerXML = $xml->xpath("//answer[@id=".$row["id_odp"]."]"); 

foreach ($answerXML as $answer){
    $score = 0;
    $row["mark"] = (string)$answer->mark;
    foreach($answer->question as $question){
        $row["questions"][(int)$question["qId"]]["name"] = (string)$question->questionName;
        $row["questions"][(int)$question["qId"]]["correct"] = (string)$question->score;
        $option_counter = 1;
        foreach ($question->option as $option){
            $row["questions"][(int)$question["qId"]]["answer".$option_counter] = (string)$option;
            $option_counter++;
        }
    }

    foreach($answer->question as $question){
        $score += $question->score;
    }
    $row["score"] = $score;
    
}

echo json_encode($row);
}
?>