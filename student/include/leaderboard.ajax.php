<?php
session_start();
include_once "../../main/dbh.inc.php";

//najdi prvých 5 hráčov s najvyšším skóre
    $conn = OpenCon();
    $sql = 'SELECT u.meno_priezvysko, g.score FROM game g
            JOIN student s ON g.student_id = s.id_student
            JOIN uzivatelia u ON s.user_student_id = u.id_uzivatel
            WHERE g.schedule_id = ?
            ORDER BY g.score DESC
            LIMIT 5'; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["schedule"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    CloseCon($conn);

    //print_r($row);

    $the_results;

    for ($i = 0; $i < count($row); $i++){
        $the_results["scoring"][$i]["name"] = $row[$i]["meno_priezvysko"];
        $the_results["scoring"][$i]["score"] = $row[$i]["score"];
    }
    
//najdi hráča s najväčším násobiteľom
    $conn = OpenCon();
    $sql = 'SELECT u.meno_priezvysko, g.multiplier FROM game g
            JOIN student s ON g.student_id = s.id_student
            JOIN uzivatelia u ON s.user_student_id = u.id_uzivatel
            WHERE g.schedule_id = ?
            ORDER BY g.multiplier DESC
            LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["schedule"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    CloseCon($conn);

    for ($i = 0; $i < count($row); $i++){
        $the_results["multiplier"][$i]["name"] = $row[$i]["meno_priezvysko"];
        $the_results["multiplier"][$i]["score"] = $row[$i]["multiplier"];
    }

//najdi hráča s najväčším počtom správnych odpovedí
    $conn = OpenCon();
    $sql = 'SELECT u.meno_priezvysko, g.answers FROM game g
            JOIN student s ON g.student_id = s.id_student
            JOIN uzivatelia u ON s.user_student_id = u.id_uzivatel
            WHERE g.schedule_id = ?
            ORDER BY g.answers DESC
            LIMIT 1'; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["schedule"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    CloseCon($conn);

    for ($i = 0; $i < count($row); $i++){
        $the_results["answers"][$i]["name"] = $row[$i]["meno_priezvysko"];
        $the_results["answers"][$i]["score"] = $row[$i]["answers"];
    }

//najdi hráča s najrýchleším dokončením testu
    $conn = OpenCon();
    $sql = 'SELECT u.meno_priezvysko, g.full_time FROM game g
            JOIN student s ON g.student_id = s.id_student
            JOIN uzivatelia u ON s.user_student_id = u.id_uzivatel
            WHERE g.schedule_id = ?
            ORDER BY g.full_time ASC
            LIMIT 1'; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["schedule"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    CloseCon($conn);

    for ($i = 0; $i < count($row); $i++){
        $the_results["full_time"][$i]["name"] = $row[$i]["meno_priezvysko"];
        $the_results["full_time"][$i]["score"] = $row[$i]["full_time"];
    }

//najdi hráča s najrýchlejšou odpovedou na otázku
    $conn = OpenCon();
    $sql = 'SELECT u.meno_priezvysko, g.short_time FROM game g
            JOIN student s ON g.student_id = s.id_student
            JOIN uzivatelia u ON s.user_student_id = u.id_uzivatel
            WHERE g.schedule_id = ?
            ORDER BY g.short_time ASC
            LIMIT 1'; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["schedule"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    CloseCon($conn);

    for ($i = 0; $i < count($row); $i++){
        $the_results["short_time"][$i]["name"] = $row[$i]["meno_priezvysko"];
        $the_results["short_time"][$i]["score"] = $row[$i]["short_time"];
    }

    echo json_encode($the_results);
    
?>