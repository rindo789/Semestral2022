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

    $now = date("Y-m-d H:i:s", time());

    //najdi všetky testy, ktoré študent môže vykonať
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT h.id_test, nazov_testu, zaciatok, koniec FROM hotovo h
                            JOIN testy t ON h.id_test = t.id_test
                            WHERE t.id_test IN (".$idstring.")");
    $stmt->execute();
    $result = $stmt->get_result();
    //vytvor tlacidla na ukazanie testu
    while ($row = $result->fetch_assoc()) {
        //ak test skoncil tak ho neukazuj
        if ($row["koniec"]<$now) continue;
        echo "<tr><td>".$row['id_test']."</td>
        <td>".$row['nazov_testu']."</td>
        <td>".$row["zaciatok"]."</td>";

        //ak test ešte nazačal tak neukazul tlačidlo
        if ($row["zaciatok"]<$now){
            echo "<td><a href='../include/takeTest.inc.php?testId=".$row['id_test']."'>Ukáž</a></td>";
        }
        echo "</tr>";
    }
    CloseCon($conn);
}

//hladanie vykonaných testov
function showScores(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_odp FROM odpoved WHERE id_student = ?");
    $stmt->bind_param("i", $_SESSION["SID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id_odp']."</td>
        <td><a href='../include/takeTest.inc.php?scoreId=".$row['id_odp']."'>Ukáž Hodnotenie</a></td>
        </tr>";
    }
    CloseCon($conn);
}

//na randomizovanie otazok a moznosti
function RandTest($test)
{
    $array = [];
    $qNumber = 0;
    //vytvor pre kazdu otazku array z option ID
    //kazdy array by mal mat kluc ID otázky
    foreach ($test->question as $question)
    {
        $qNumber++;
        $array[$qNumber]=[];
        $numbers = [];
        //daj cisla moznosti do arrayu podla cisla otazky doradu
                        //question-1 lebo je tam data na name a typ
        for ($i = 1; $i < count($question)-1;$i++)
        {
            array_push($numbers, $i);
        }
        shuffle($numbers);
        $array[$qNumber] = $numbers;
        //vytvor nový array pre samotné ID otázok
        //nový array pre otazky sa musí vytvorit lebo shuffle na id questionov nejde?!
        $quest=[];
        for ($i = 1; $i < count($test)-2;$i++)
        {
            array_push($quest, $i);
        }
    }
    shuffle($quest);
    //print_r($quest);
    //print_r($array);

    //pridaj to to returnovacieho arrayu
    $shuffled["questions"] = $quest;
    $shuffled["options"] = $array;
    //print_r($shuffled);
    return $shuffled;
}

function loadTestStudent($testID){
    $xml = simplexml_load_file("../../xml/tests.xml");
    
    $tests = $xml->xpath("//test[id=".$testID."]");

    $returnString = "";
    
    //toto sluzi na vthnutie do arrayu???
    foreach ($tests as $test) {

        //spustenie random question/option testu
        $shuffled = RandTest($test);

        $returnString = $returnString.
        "<p>".$test->description."</p>";

        //vytvorenie otázky
        foreach ($shuffled["questions"] as $qNumber) {
            $questionXml = $xml->xpath("//test[id=".$testID."]/question[@qId=".$qNumber."]");
            foreach ($questionXml as $question){
                
            //fieldset
            $returnString = $returnString.
            "<fieldset id='fieldset".$question["qId"]."'>
            <p>".$question->questionName."</p>
            <br>";

            //ukazanie moznosti v otazke
            //ak je typ otazky text nerob randomizáciu
            if ($question->type == "text") {
                foreach ($question->option as $option){
                    $returnString = $returnString.
                    "<input type='text' placeholder='odpoved' name='answer[".$question["qId"]."][moznost][".$option["oId"]."]'>";
                }
            } else {
                foreach ($shuffled["options"][$qNumber] as $oNumber)
                {
                    $optionXml = $xml->xpath("//test[id=".$testID."]/question[@qId=".$qNumber."]/option[@oId=".$oNumber."]");
                    foreach ($optionXml as $option){
                            if($question->type == "checkbox"){
                                $returnString = $returnString.
                                "<input type='checkbox' name='answer[".$question["qId"]."][moznost][correct".$option["oId"]."]'>";
                            } else {
                                $returnString = $returnString.
                                "<input type=".$question->type." name='answer[".$question["qId"]."][moznost][correct]'>";
                            }                        
                                
                            $returnString = $returnString.
                            "<p>".$option->optionName."</p>
                            <input type='hidden' name='answer[".$question["qId"]."][moznost][".$option["oId"]."]' value='".$option->optionName."'>
                            <br>";                     
                    } 
                }
                $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='answer[".$question["qId"]."][type]'>";                  
            }

            $returnString = $returnString.
            "</fieldset>";
        }
    }
    }
    echo $returnString;
}

//funckia na kontrolu či sa uloženie testu uskutočnuje v čase testu
function testActive(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT MAX(id_hotovo) as active_test FROM hotovo WHERE id_test = ?");
    $stmt->bind_param("i",$_SESSION["testIdToEdit"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);
    if (isset($row["active_test"]))
    {
        return $row["active_test"];
    }    
}

function saveAnswer($answers, $active_test){
    //pridanie odpovede do databazy
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO odpoved (id_test,id_student) VALUES (?,?)");
    $stmt->bind_param("ii",$_SESSION["testIdToEdit"],$_SESSION["SID"]);
    $stmt->execute();

    //najdenie novej odpovede
    $stmt = $conn->prepare("SELECT MAX(id_odp) as newAnswer FROM odpoved WHERE id_student = ?");
    $stmt->bind_param("i",$_SESSION["SID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);

    //pridanie odpovede do xml
    $xml = simplexml_load_file("../../xml/answers.xml");
    //xml testu na najdenie textu otázky a mena testu a následne uloženie
    $xml2 = simplexml_load_file("../../xml/tests.xml");
    //pridanie id odpovede
    $answerXml = $xml->addChild('answer');
    $answerXml->addattribute('id',$row['newAnswer']);
    
    $addname = false;
    //pridanie otázky
    //vkročenie do arrayu otázky
    foreach ($answers as $key => $question) {
        $questionXml = $answerXml->addChild('question');
        $questionXml->addAttribute('qId',$key);

        //pridanie nazvu testu
        if ($addname == false){
            $testXML = $xml2->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/name");
            foreach ($testXML as $name)
            {
                $answerXml->addChild('name',$name);
            }
            $addname = true;
        }        
        
        //najdi správny test, otázku a jej znenie a pridaj ho do xml odpovede
        $testXML = $xml2->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$key."]/questionName");
        foreach ($testXML as $qName)
        {
            $questionXml->addChild('questionName', $qName);
        }

        //skontrolovat či je v arrayi na odpoved aj typ otázky, ak nie je (čo sa rovná 1) znamená to
        //že typ je text a môže sa pridať odpoveď hneď
        if (count($question) == 1){
            foreach ($question["moznost"] as $key => $value){
                //ak nebola zodpovedaná otázka
                if (empty($value)){
                    continue;
                }
                $optionXml = $questionXml->addChild('option', $value);
                $optionXml->addAttribute('oId',$key);
            }
        }

        $chosen = false;
        //vkročenie do vybraných možností
        //najdi, ktoré študent vybral
        foreach ($question["moznost"] as $key => $value) {
            if (preg_match("/correct/",$key)){
                $chosen = true;
                continue;
            }
            if ($chosen == true){
                $optionXml = $questionXml->addChild('option', $value);
                $optionXml->addAttribute('oId',$key);
                $chosen = false;
            }
        }
        //pridaj na ktorý naplanovaný test sa ma odpoved priradit
        $answerXml->addChild('schedule', $active_test);
    }

    //uloženie xml
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/answers.xml');

    return $row['newAnswer'];
}

function checkAnsw($array, $answerId){
    $xml = simplexml_load_file("../../xml/tests.xml");
    $test = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]");

    $xml2 = simplexml_load_file("../../xml/answers.xml");
    $answer = $xml2->xpath("//answer[@id=".$answerId."]/question");

    //vlez do otázok odpoveede študenta
    foreach($answer as $question){
        //echo $question->attributes();

        //uloz atribut otázky
        $qID = $question->attributes();
        //vlezenie do mozností otázky odpovede študenta
        foreach($question as $option)
        {
            //echo "<br>". $option->attributes()." ". $option."<br>";
            //ulozenie ID možnosti
            $oID = $option->attributes();
            //najdenie typu otázky
            $type = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/type");
            foreach ($type as $typename) {
                $type = $typename;
            }
            //najdenie údajov v teste
            //ak je otázka typ text potrebujeme nájsť jeho optionName na porovnávanie
            if ($type == "text")
                {
                    $checkOption = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/option[".$oID."]/optionName");
                } else {
                    $checkOption = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/option[".$oID."]");
                }
            
            
            //kontrola odpovede
            foreach ($checkOption as $opt){
                //ak je odpoved text, porovnaj odpovede
                //pretypuj z arrayu na string
                if ($type == "text"){
                    $text1 = (string) $option;
                    $text2 = (string) $opt;
                    if ($text1 == $text2){
                        $option->addChild("correct", "yes");
                        break;
                    } else {
                        $option->addChild("correct", "no");
                        break;
                    }
                }
                //pridaj normalne ak iné
                $option->addChild("correct", $opt->correct);
            }
        }
    }

    //uloz xml
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml2->asXML());
    $dom->save('../../xml/answers.xml');
}

//oboduj jednotlivé odpovede
//obodobanie je len zatial 0-1
function scoreAns($answerId){
    //nacitaj odpoved
    $xml2 = simplexml_load_file("../../xml/answers.xml");

    //nacitaj test
    $xml = simplexml_load_file("../../xml/tests.xml");
    $test = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question");

    //prejdenie do otázok testu
    foreach ($test as $question){
        //zober ID otázok
        $qID = $question->attributes();

        //ak je viac výberová otázka
        if ($question->type == "checkbox"){
            //počítadlo správnych odpovedí v teste
            $nof_correct = 0;
            foreach($question->option as $option)
            {
                if ($option->correct == "yes"){
                    $nof_correct++;
                }
            }
            //počítadlo správnych odpovedí študenta
            $correct_student = 0;
            $options = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]/option");
            foreach($options as $correct){
                if ($correct->correct == "yes"){
                    $correct_student++;
                }
            }
            
            //vypočítaj percentuálne koľko mal študent správnych odpovedí a daj do čísla (1, 0.75 atď)
            $question_student = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]");
            foreach($question_student as $que){
                $que->addChild("score", (($correct_student*100)/$nof_correct)/100);
            }

        } else {
        //najdi či študent správne alebo nesprávne odpovedal a pridaj mu ohodnotenie 1 alebo 0
        $answer = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]/option");
        foreach($answer as $correct){
            $question_student = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]");
            if ($correct->correct == "yes"){
                foreach($question_student as $que){
                    $que->addChild("score", 1);
                }
            } else {
                foreach($question_student as $que){
                    $que->addChild("score", 0);
                }
            }
        }
    }
    }

    //sprav percentualne vyhodnotenie testu a pripis do xml odpovede známku
    $sum = 0;
    $answerXML = $xml->xpath("//answer[@id=".$answerId."]/question");
        
        echo "<td>";
        //generuj spocitaj body za odpovede
        foreach ($answerXML as $question){
                $sum += $question->score;
        }
        echo "</td>";
    //vypocitaj kolko percent je odpoved
    if ($sum == 0) {
        $answerXML = $xml->xpath("//answer[@id=".$answerId."]");
        foreach ($answerXML as $answer){
            $answer->addChild("mark","FX");
        }
    }else {
        $percent = ($sum*100)/count($answerXML);
    }

    $answerXML = $xml2->xpath("//answer[@id=".$answerId."]");
    //pridaj do xml odpovede
    foreach ($answerXML as $answer){
        if ($percent < 56) {
            $answer->addChild("mark","FX");
        } else if ($percent > 56 && $percent < 65) {
            $answer->addChild("mark","E");
        } else if ($percent > 65 && $percent < 74){
            $answer->addChild("mark","D");
        } else if ($percent > 74 && $percent < 83){
            $answer->addChild("mark","C");
        } else if ($percent > 83 && $percent < 92){
            $answer->addChild("mark","B");
        } else if ($percent > 92 && $percent <= 100){
            $answer->addChild("mark","A");
        }
    }

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml2->asXML());
    $dom->save('../../xml/answers.xml');
}

//funckia na kontrolu či študent už test spravil
function testTaken($testID){

    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test FROM odpoved WHERE id_test = ? AND id_student = ?");
    $stmt->bind_param("ii", $testID, $_SESSION["SID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);

    if(mysqli_num_rows($result)>0){
        return true;
    } else return false;
}

//skontroluj či test alebo score je pre študenta
function studentBelong($type){
    if ($type == "test"){
        $xml = simplexml_load_file("../../xml/tests.xml");
        $testXML = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]");
        $group_id = 0;

        foreach ($testXML as $test){
            $group_id = (int) $test->group;
            //echo $group_id;
        }        
        
        $xml = simplexml_load_file("../../xml/groups.xml");
        $groupXML = $xml->xpath("//group[@id=".$group_id."]/students/student");
        foreach ($groupXML as $student){            
            if ($_SESSION["SID"] == $student){                
                return true;
            }
        }
    } else if ($type == "score"){
        $conn = OpenCon();
        $stmt = $conn->prepare("SELECT id_student FROM odpoved WHERE id_odp = ? AND id_student = ?");
        $stmt->bind_param("ii", $_SESSION["testIdToEdit"], $_SESSION["SID"]);
        $stmt->execute();
        $result = $stmt->get_result();        
        CloseCon($conn);
        if (mysqli_num_rows($result) > 0){
            return true;
        }
    }
    return false;
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
    echo "<table>";
    echo "<tr>";
    echo "<th>Otázka</th>";

    $answerXML = $xml->xpath("//answer[@id=".$answerID."]/question");
    //generuj tabulku podľa otázok v teste
    foreach ($answerXML as $question)
    {
        echo "<th>".$question["qId"].". ".$question->questionName."</th>";
    }
    echo "<th>Celkovo</th>";
    echo "</tr>";
    echo "<tr>"; 
    echo "<td>Odpoveď</td>";
    //generuj tabulku podľa odpovedí študenta
    //tried odpovede
    $sort_qID = [];
    foreach ($answerXML as $question)
    {
        array_push($sort_qID,$question["qId"]);
    }
    sort($sort_qID, SORT_NUMERIC);

    //najdi otázky podla ID v XML a pretriedenom zozname
    foreach($sort_qID as $qID){
        $answerXML = $xml->xpath("//answer[@id=".$answerID."]/question[@qId=".$qID."]");
        
        echo "<td>";
        //generuj aj viaceré odpovede ak bol typ checkbox
        foreach ($answerXML as $question){
            foreach($question->option as $option){
                echo $option." ".$option->correct;
                echo "<br>";
            }            
        }
        echo "</td>";

    }
    echo "<td></td>";
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