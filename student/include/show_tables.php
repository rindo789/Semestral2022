<?php
require_once "../../main/dbh.inc.php";

function showTests(){
    //nájdi, v ktorých skupinách sa študent nachádza a zisti ich id
    $xml = simplexml_load_file("../../xml/groups.xml");
    $studentIDs = $xml->xpath("//*[student='".$_SESSION["SID"]."']/parent::group/attribute::*");

    $firstout = false;
    $idstring = "";
    //najdi id skupin v testoch a pridaj id testov do stringu
    $xml = simplexml_load_file("../../xml/tests.xml");
    //za každu skupinu v ktorej test je
    foreach ($studentIDs as $id){
        $testIDs = $xml->xpath("//*[group='".$id['id']."']/id");
        
        //za každý test v danej skupine
        foreach ($testIDs as $id) {
            
            //SPRAV FUNKCIU NA ZABLOKOVANIE OPAKOVANIA TESTU

            //zisti či študent už test dokončil
            /*if (testTaken($id) == true){
                continue;
            }*/

            if ($firstout == true) {
                $idstring = $idstring .", ". $id;
            }
            else {
                $idstring = $id;
                $firstout = true;
            }
        }
    }

    //ak nemam v stringu ani jedno ID testu, tak zrus funkciu
    if ($idstring == ""){
        return;
    }

    //najdi čas teraz
    $now = date("Y-m-d H:i:s", time());

    //najdi všetky testy, ktoré študent môže vykonať
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT h.id_test, id_schedule, nazov_testu, zaciatok, koniec, test_type FROM schedule h
                            JOIN testy t ON h.id_test = t.id_test
                            WHERE t.id_test IN (".$idstring.")");
    $stmt->execute();
    $result = $stmt->get_result();
    //vytvor tlacidla na ukazanie testu
    while ($row = $result->fetch_assoc()) {
        //ak test skoncil tak ho neukazuj
        if ($row["koniec"]<$now) continue;

        echo "<tr><td>".$row['nazov_testu']."</td>
        <td>".date("H:i:s d.m.Y", strtotime($row['zaciatok']))."</td>";

        //ak test ešte nazačal tak neukazul tlačidlo
        if ($row["zaciatok"]<$now){
            if ($row["test_type"] == "classic"){
                echo "<td><a href='../include/takeTest.inc.php?testId=".$row['id_test']."&schedule=".$row["id_schedule"]."'>Spusti test</a></td>";
            } else if ($row["test_type"] == "game"){
                echo "<td><a href='../include/game.inc.php?testId=".$row['id_test']."&schedule=".$row["id_schedule"]."'>Spusti hru</a></td>";
            }
        } else echo "<td></td>";
        echo "</tr>";
    }
    CloseCon($conn);
}

//hladanie vykonaných testov
function showScores(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT o.id_odp, s.zaciatok, t.nazov_testu, s.test_type, s.id_schedule FROM odpoved o 
                            JOIN schedule s ON o.schedule_id = s.id_schedule
                            JOIN testy t ON s.id_test = t.id_test
                            WHERE o.id_student = ?");
    $stmt->bind_param("i", $_SESSION["SID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
        <td>".$row["nazov_testu"]."</td>
        <td>".date("H:i:s d.m.Y", strtotime($row['zaciatok']))."</td>";
        if ($row["test_type"] == "classic"){
            echo "<td><a href='../include/takeTest.inc.php?scoreId=".$row['id_odp']."'>Ukáž hodnotenie</a></td>";
        } else echo "<td><a href='../include/takeTest.inc.php?scoreId=".$row['id_odp']."&type=game&schedule_id=".$row['id_schedule']."'>Ukáž hodnotenie hry</a></td>";        
        echo "</tr>";
    }
    CloseCon($conn);
}

//zobrazenie hodnotenie testu pre študenta
function scoreTable(){
    $answerID = $_SESSION["testIdToEdit"];

    //nájdi test podla id odpovede
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test, nazov_testu FROM testy where id_test = 
                            (SELECT id_test FROM odpoved WHERE id_odp = ? AND id_student = ?)");
    $stmt->bind_param("ii", $answerID, $_SESSION["SID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);

    $xml = simplexml_load_file("../../xml/answers.xml");
    $xml2 = simplexml_load_file("../../xml/tests.xml");
    $answerXML = $xml->xpath("//answer[@id=".$answerID."]/name");

    foreach ($answerXML as $name)
    {
        echo "<h1>".$name."</h1>";
    }
    echo "<table id='student_score'>";
    echo "<tr>";
    echo "<th>Otázka</th>";

    $answerXML = $xml->xpath("//answer[@id=".$answerID."]/question");
    //tried odpovede

    $sort_qID = [];
    foreach ($answerXML as $question)
    {
        array_push($sort_qID,$question["qId"]);
    }
    sort($sort_qID, SORT_NUMERIC);

    //generuj tabulku podľa otázok v teste
    foreach($sort_qID as $qID){
        $answerXML = $xml->xpath("//answer[@id=".$answerID."]/question[@qId=".$qID."]");
        foreach ($answerXML as $question)
        {
            echo "<th>".$question["qId"].". ".$question->questionName."</th>";
        }
    }
    
    echo "<th>Celkovo</th>";
    echo "<th>Známka</th>";
    echo "</tr>";
    echo "<tr>"; 
    echo "<td>Odpoveď</td>";
    
    //generuj tabulku podľa odpovedí študenta
    //najdi otázky podla ID v XML a pretriedenom zozname
    foreach($sort_qID as $qID){
        $answerXML = $xml->xpath("//answer[@id=".$answerID."]/question[@qId=".$qID."]");        
        
        //generuj aj viaceré odpovede ak bol typ checkbox
        foreach ($answerXML as $question){
            if ($question->score > 0){
                echo "<td class='correct'>";
            } else {
                echo "<td class='incorrect'>";
            }
            foreach($question->option as $option){
                if ($option == "/*empty*/"){
                    echo " - ";
                    continue;
                }
                echo $option;
                echo "<br>";
            }            
        }
        echo "</td>";

    }
    echo "<td></td>
    <td></td>";
    echo "</tr>";
    echo "<tr>";

    echo "<td>Hodnotenie</td>";
    $sum = 0;
    foreach($sort_qID as $qID){
        $answerXML = $xml->xpath("//answer[@id=".$answerID."]/question[@qId=".$qID."]");
        
        echo "<td>";
        //generuj hodnotenie pre odpoved
        foreach ($answerXML as $question){
                echo $question->score;
                $sum += $question->score;
        }
        echo "</td>";
    }
    echo "<td>".$sum."/".count($sort_qID)."</td>";

    //sprav echo na známku
    $answerXML = $xml->xpath("//answer[@id=".$answerID."]");
    foreach($answerXML as $answer){
        echo "<td>".$answer->mark."</td>";
    }
    
    echo "</tr>";
    echo "</table>";
}
?>