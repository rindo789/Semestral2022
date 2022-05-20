<?php
require_once "../../main/dbh.inc.php";

//vypíš všetky testy v teacher.php
function showTests($teacherId){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test, nazov_testu FROM testy WHERE ucitel_id_uci = ?");
    $stmt->bind_param("i",$teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    //vytvor tlacidla na vymazanie a ukazanie v teacher.php
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['nazov_testu']."</td>
        <td><a href='../include/test_states.php?testId=".$row['id_test']."&state=show'>Zobraz</a></td>
        <td><a href='../include/test_states.php?testId=".$row['id_test']."&state=delete'>Vymaž</a></td>
        </tr>";
    }
    CloseCon($conn);
}

//vloz test do databázy este predtým ako sa bude upravovat
function newTest($testName,$teacherId)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO testy (nazov_testu,ucitel_id_uci) VALUES (?,?)");
    $stmt->bind_param("si",$testName,$teacherId);
    $stmt->execute();

    echo "test:". $testName . " bol vytvorený!";

    CloseCon($conn);
}

//najdi novo pridaný test
function checkNewTestId($teacherId)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT MAX(id_test) AS newTestId FROM testy WHERE ucitel_id_uci = ?");
    $stmt->bind_param("i",$teacherId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (mysqli_num_rows($result)==0){
        header("location: ..index/teacher.php?error=newtestnotfound");
        exit();
    }

    $row = $result->fetch_assoc();
    CloseCon($conn);

    return $row['newTestId'];    
}

//ukladanie testu do xml
function saveTest($testName,$testID){
    $qCounter = 0;
    $xml = simplexml_load_file("../../xml/tests.xml");  

    //vymazanie testu v xml ak tam je
    foreach($xml->test as $seg)
    {
        if($seg->id == $testID) {
            $dom=dom_import_simplexml($seg);
            $dom->parentNode->removeChild($dom);
        }
    }

    //pridanie test nodu
    $test = $xml->addChild('test');
    //$test = $xml->test;
    //$test = current($xml->xpath('//test[last()]'));
    $test->addchild('id',$testID);
    $test->addchild('name',$testName);
    $test->addchild('description',$_POST["opis"]);
    $test->addchild('group',$_POST["group"]);
    
    //pridanie question nodu
    foreach ($_POST["test"] as $value) {
        $question = $test->addchild('question');
        $qCounter++;
        $question->addAttribute('qId',$qCounter);
        $question->addchild('questionName',$value["QuestionText"]);
        $question->addchild('type',$value['type']);
        
        //pridanie option a correct nodu
        $oCounter=0;
        $correct = false;
        foreach ($value["moznost"] as $key) {
            //preskoc correct node v option zozname
            if ($key=="on") {$correct = true; continue;}
            $option = $question->addchild('option');
            $option->addchild('optionName',$key);
            $oCounter++;
            $option->addAttribute('oId',$oCounter);

            if ($correct == true){
                $option->addchild('correct', 'yes');
                $correct = false;
            } else {
                $option->addchild('correct', 'no');
            }
        }
    }

    //uloz xml
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/tests.xml'); 
}

//ukaz vytvorený test
function loadTestTeacher($testID){
    $xml = simplexml_load_file("../../xml/tests.xml");
    $qCounter = 0;
    $oCounter = 0;
    $returnString = "";
    foreach ($xml->test as $test) {
        if ($test->id == $testID){
            /*$returnString = $returnString.
            "<textarea name='opis' placeholder='opis' form='test_form'>".$test->description."</textarea>";*/
            //ukazanie otazky
            foreach ($test->question as $question){
                $qCounter++;

                $returnString = $returnString.
                "<fieldset id='fieldset".$qCounter."'>
                <input type='text' placeholder='Polož otázku' name='test[".$qCounter."][QuestionText]' value='".$question->questionName."'><br>";
                
                //ukazanie moznosti v otazke
                if($question->type == "checkbox")
                {
                    foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type='checkbox' name='test[".$qCounter."][moznost][correct".$oCounter."]'";
                        if ($option->correct == "yes") $returnString = $returnString." checked>";
                        else $returnString = $returnString.">";
                        
                        $returnString = $returnString.
                        "<input type='text' placeholder='možnosť' name='test[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'><br>";
                    }  
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='test[".$qCounter."][type]'>
                    <button onclick='CreateOption('multi',this.value)' type='button' value=".$qCounter." id='more".$qCounter."'>Ďaľšia možnosť</button>";
                    $oCounter = 0;
                } else if ($question->type == "text") {
                        foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type='text' placeholder='možnosť' name='test[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'><br>";
                    }
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='test[".$qCounter."][type]'>
                    <button onclick='CreateOption('text',this.value)' type='button' value=".$qCounter." id='more".$qCounter."'>Ďaľšia možnosť</button>";                    
                    $oCounter = 0;
                } else {
                    foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type=".$question->type." name='test[".$qCounter."][moznost][correct]'";
                        if ($option->correct == "yes") $returnString = $returnString."checked>";
                        else $returnString = $returnString.">";
                        
                        $returnString = $returnString.
                        "<input type='text' placeholder='možnosť' name='test[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'><br>";
                    } 
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='test[".$qCounter."][type]'>
                    <button onclick='CreateOption('one',this.value)' type='button' value=".$qCounter." id='more".$qCounter."'>Ďaľšia možnosť</button>";                    
                    $oCounter = 0;                    
                }
                $returnString = $returnString.
                "<button type='button' value=".$qCounter." onclick='DeleteQuestion(this.value)'>Vymaž otázku</button>
                </fieldset>";
            }
        }
    }
    echo $returnString;
}

function echoDescription(){
    $testID = $_SESSION["testIdToEdit"];
    $xml = simplexml_load_file("../../xml/tests.xml");
    $testXml = $xml->xpath("//test[id=".$testID."]");
    foreach ($testXml as $test){
       echo "<textarea name='opis' placeholder='opis' form='test_form'>".$test->description."</textarea>";
    }
}

//vypisanie skupin pri vytváraní testu
function echoGroups(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_group, group_name FROM groups WHERE teacher_id = ?");
    $stmt->bind_param("i",$_SESSION["TID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);

    $testID = $_SESSION["testIdToEdit"];
    $xml = simplexml_load_file("../../xml/tests.xml");
    $testXml = $xml->xpath("//test[id=".$testID."]/group");

    echo "<select name='group'>";
    echo "<option value='none'>none</option>";

    while ($row = $result->fetch_assoc()){
        if ($testXml[0] == $row["id_group"]){
            echo "<option value=".$row["id_group"]." selected>".$row["group_name"]."</option>";
        }else echo "<option value=".$row["id_group"].">".$row["group_name"]."</option>";
    }

    echo "</select>";
}

//vymaz test zo zoznamu testov, ale zanechaj metadata ak zostali nejake výsledky
function TeacherDeleteTest($testID)
{
    //vymaz test z databázy
    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM testy WHERE id_test = ?");
    $stmt->bind_param("i",$testID);
    $stmt->execute();
    CloseCon($conn);

    //najdi test v xml
    $xml = simplexml_load_file("../../xml/tests.xml");
    $tests = $xml->xpath("//test[id=".$testID."]");

    //vymazanie testu z xml
    foreach($tests as $seg)
    {
        $dom=dom_import_simplexml($seg);
        $dom->parentNode->removeChild($dom);
    }

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save("../../xml/tests.xml");
}

?>