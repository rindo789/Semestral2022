<?php
require_once "../../main/dbh.inc.php";

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
        "<h2>".$test->name."</h2>";

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

function saveAnswer($answers, $active_test){
    //pridanie odpovede do databazy
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO odpoved (id_test,id_student,schedule_id) VALUES (?,?,?)");
    $stmt->bind_param("iii",$_SESSION["testIdToEdit"],$_SESSION["SID"],$active_test);
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

    $testXML = $xml2->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/name");
    foreach ($testXML as $name)
    {
        $answerXml->addChild('name',$name);
    }
    
    //$addname = false;
    //pridanie otázky
    //vkročenie do arrayu otázky
    foreach ($answers as $key => $question) {
        $questionXml = $answerXml->addChild('question');
        $questionXml->addAttribute('qId',$key);

        //pridanie nazvu testu
        /*if ($addname == false){
            $testXML = $xml2->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/name");
            foreach ($testXML as $name)
            {
                $answerXml->addChild('name',$name);
            }
            $addname = true;
        }        */
        
        //najdi správny test, otázku a jej znenie a pridaj ho do xml odpovede
        $testXML = $xml2->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$key."]/questionName");
        foreach ($testXML as $qName)
        {
            $questionXml->addChild('questionName', $qName);
        }

        //skontrolovat či je v arrayi na odpoved aj typ otázky, ak nie je (čo sa rovná 1) znamená to
        //že typ je text a môže sa pridať odpoveď hneď
        $text_type = false;
        if (count($question) == 1){
            foreach ($question["moznost"] as $key => $value){
                //ak nebola zodpovedaná otázka
                if (empty($value)){
                    echo "text je prázdy";
                    $text_type = true;
                    $optionXml = $questionXml->addChild('option',"/*empty*/");
                    $optionXml->addAttribute('oId',$key);
                    break;
                } else {
                    $text_type = true;
                    $optionXml = $questionXml->addChild('option', $value);
                    $optionXml->addAttribute('oId',$key);
                    break;
                }
            }
        }
        //kontrola či je typ text aby mohol toto vynachat
        if ($text_type == false){
            //skontroluj či študent odpovedal na otázku
            $empty_asnwer = true;
            foreach ($question["moznost"] as $key => $value) {
                if (preg_match("/correct/",$key)){
                    echo "študent odpovedal";
                    $empty_asnwer = false;
                    break;
                }
            }

            $chosen = false;
            //vkročenie do vybraných možností
            //najdi, ktoré študent vybral
            print_r($question["moznost"]);
            foreach ($question["moznost"] as $key => $value) {
                if ($empty_asnwer == true){
                    $optionXml = $questionXml->addChild('option',"/*empty*/");
                    $optionXml->addAttribute('oId',$key);
                    break;
                }
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
        }
    }
    //pridaj na ktorý naplanovaný test sa ma odpoved priradit
    $answerXml->addChild('schedule', $active_test);

    //uloženie xml
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/answers.xml');

    return $row['newAnswer'];
}
?>