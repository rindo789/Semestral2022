<?php
include "../../main/dbh.inc.php";
//HODNOTENIA
//vypis tabulku testov ktoré boli vykonané
function scoreTable(){    
    //vypis vsetky zaznamy pre naplánované testy
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_schedule, h.id_test, nazov_testu, zaciatok, koniec FROM schedule h
                            JOIN testy t ON h.id_test = t.id_test 
                            WHERE ucitel_id_uci = ?");
    
    $stmt->bind_param("i",$_SESSION["TID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) != 0){        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>".$row['id_schedule']."</td>
                <td>".$row['id_test']."</td>
                <td><a href='scoreStudents.php?schedule=".$row["id_schedule"]."'>".$row['nazov_testu']."</a></td>
                <td>".$row['zaciatok']."</td>
                <td>".$row['koniec']."</td>
                </tr>";
        }
    }    
    CloseCon($conn);
}
    
    //vypis odpovede študentov v tabulke hodnotení
function scoreAllStudents($scheduleID){
    $xml = simplexml_load_file("../../xml/answers.xml");
    $answerXML = $xml->xpath("//answer[schedule=".$scheduleID."]");        

    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT o.id_student, u.meno_priezvysko, o.id_odp, sch.id_schedule, sch.id_test FROM uzivatelia u
                            JOIN student s ON u.id_uzivatel = s.user_student_id
                            JOIN odpoved o ON o.id_student = s.id_student
                            JOIN schedule sch ON sch.id_test = o.id_test
                            WHERE sch.id_schedule = ?");
    $stmt->bind_param("i", $scheduleID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);
    
    //spocitanie bodov
    $score = 0;

    foreach ($answerXML as $answer){
        $odpovedID = $answer["id"];
            
        echo "<tr>
            <td>".$odpovedID."</td>
            <td>".$row["meno_priezvysko"]."</td>";

        foreach($answer->question as $question){
            foreach ($question->option as $option){
                if ($question->score > 0){
                    echo "<td class='correct'>".$question["qId"].". ";
                    echo $option."</td>";
                }else {
                    echo "<td class='incorrect'>".$question["qId"].". ";
                    echo $option."</td>";
                }                
            }
        }

        foreach($answer->question as $question){
            $score += $question->score;
        }
        echo "<td>".$score."</td>";
        echo "<td>".$answer->mark."</td>
            </tr>";
        }
    }


//Funkcia na automatické priradenie colspanu pre stlpec Odpovede v tabulke hodnotenia testu
function autoColspan($scheduleID){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test FROM schedule WHERE id_schedule = ?");
    $stmt->bind_param("i", $scheduleID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);


    $testXML = simplexml_load_file("../../xml/tests.xml");
    $test_query = $testXML->xpath("//test[id=".$row["id_test"]."]/question");

    return count($test_query);
}
?>