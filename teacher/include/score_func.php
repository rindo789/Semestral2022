<?php
include "../../main/dbh.inc.php";
//HODNOTENIA
//vypis tabulku testov ktoré boli vykonané
function scoreTable(){    
    //vypis vsetky zaznamy pre naplánované testy
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_schedule, h.id_test, nazov_testu, zaciatok, koniec FROM schedule h
                            JOIN testy t ON h.id_test = t.id_test 
                            WHERE ucitel_id_uci = ?
                            ORDER BY koniec");
    
    $stmt->bind_param("i",$_SESSION["TID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) != 0){        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td><a href='scoreStudents.php?schedule=".$row["id_schedule"]."'>".$row['nazov_testu']."</a></td>
                <td>".date("H:i:s d.m.Y", strtotime($row['zaciatok']))."</td>
                <td>".date("H:i:s d.m.Y", strtotime($row['koniec']))."</td>
                </tr>";
        }
    }    
    CloseCon($conn);
}
    
//vypis odpovede študentov v tabulke hodnotení
function scoreAllStudents($scheduleID){
    //najdi subor XML hodnotení
    $xml = simplexml_load_file("../../xml/answers.xml");

    //najdi informácie o každej odpovedi na určitý test v SQL
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT o.id_student, u.meno_priezvysko, o.id_odp, sch.id_schedule, sch.id_test FROM schedule sch
                            JOIN odpoved o ON o.schedule_id = sch.id_schedule
                            JOIN student s ON o.id_student = s.id_student
                            JOIN uzivatelia u ON u.id_uzivatel = s.user_student_id
                            WHERE sch.id_schedule = ?");
    $stmt->bind_param("i", $scheduleID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    CloseCon($conn);

    //nájdi meno testu v Xml pre odpovede
    $answerXML = $xml->xpath("//answer[schedule=".$scheduleID."]/name");

    //Ak na test nebola spravená odpoved meno nevytváraj
    if ((string)$answerXML[0] != null) {
        echo "<h1>".(string)$answerXML[0]."</h1>";
    }

    //Vytvor tabulku
    echo "<table>";
    echo "<tr>";
    echo "<th>Meno študenta</th>";

    //variable na triedenie otázok 
    $sort_qID = [];

    //najdenie zadania otázky a pridanie ID otázky do triediaceho pola
   /*foreach ($row as $odpoved_info){
       $answerXML = $xml->xpath("//answer[@id=".$odpoved_info["id_odp"]."]/question");
       //generuj tabulku podľa otázok v teste
       foreach ($answerXML as $question){
            echo "<th>".$question["qId"].". ".$question->questionName."</th>";
            //tried odpovede
            array_push($sort_qID,$question["qId"]);
        }
        break;
   }*/

   foreach ($row as $odpoved_info){
        $answerXML = $xml->xpath("//answer[@id=".$odpoved_info["id_odp"]."]/question");
        //generuj tabulku podľa otázok v teste
        foreach ($answerXML as $question){
            //tried odpovede
            array_push($sort_qID,$question["qId"]);
        }
        break;
    }
   //tried pole s otázkamy
   sort($sort_qID, SORT_NUMERIC);

   foreach ($row as $odpoved_info){
       foreach ($sort_qID as $question_nums){
        $answerXML = $xml->xpath("//answer[@id=".$odpoved_info["id_odp"]."]/question[@qId=".$question_nums."]");
        //generuj tabulku podľa otázok v teste
        foreach ($answerXML as $question){
            //vypis znenie otázok
            echo "<th>".$question["qId"].". ".$question->questionName."</th>";
            }
        }
        break;
    }

    echo "<th>Celkovo</th>";
    echo "<th>Známka</th>";
    echo "</tr>";
    //prechádzaj cez odpovede študentov v SQL
    foreach ($row as $odpoved_info){
        
        //spocitavanie bodov a celkovo otázok 
        $score = 0;
        $nof_questions = 0;

        echo "<tr>"; 
        //vypis meno študenta
        echo "<td>".$odpoved_info["meno_priezvysko"]."</td>";

        //prechádzaj cez odpovede pomocou ID otázok z triediaceho poľa
        foreach($sort_qID as $qID){

            $answerXML = $xml->xpath("//answer[@id=".$odpoved_info["id_odp"]."]/question[@qId=".$qID."]");  

            //generuj aj viaceré odpovede ak bol typ checkbox
            foreach ($answerXML as $question){
                $nof_questions++;
                if ($question->score > 0){
                    echo "<td class='correct'>";
                    $score++;
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
                echo "</td>";         
            }
        }
        
        //vypis body a pocet otázok v teste, a aj známku, ktorú študent dostal
        echo "<td>".$score." / ".$nof_questions."</td>";

        $answerXML = $xml->xpath("//answer[@id=".$odpoved_info["id_odp"]."]/mark");  

        echo "<td>".$answerXML[0]."</td>";
        echo "</tr>";
    }

    echo "</table>";
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