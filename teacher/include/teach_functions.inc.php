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
        echo "<tr><td>".$row['id_test']."</td>
        <td>".$row['nazov_testu']."</td>
        <td><a href='../include/teacher.inc.php?testId=".$row['id_test']."&state=show'>Ukáž</a></td>
        <td><a href='../include/teacher.inc.php?testId=".$row['id_test']."&state=delete'>Zmaž</a></td>
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

function checkNewTestId($teacherId)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT MAX(id_test) AS newTestId FROM testy WHERE ucitel_id_uci = ?");
    $stmt->bind_param("i",$teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);

    return $row['newTestId'];    
}

//ukladanie testu do xml
function saveTest($testName,$testID){
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
    
    //pridanie question nodu
    foreach ($_POST["test"] as $value) {
        $question = $test->addchild('question');
        $question->addchild('questionName',$value["QuestionText"]);
        $question->addchild('type',$value['type']);
        
        //pridanie option a correct nodu
        $correct = false;
        foreach ($value["moznost"] as $key) {
            //preskoc correct node v option zozname
            if ($key=="on") {$correct = true; continue;}
            $option = $question->addchild('option');
            $option->addchild('optionName',$key);

            if ($correct == true){
                $option->addchild('correct', 'yes');
                $correct = false;
            } else {
                $option->addchild('correct', 'no');
            }
        }
    }

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/tests.xml');

    //$xml->asXML("tests.xml");    
}

//ukaz vytvorený test
function loadTestTeacher($testID){
    $xml = simplexml_load_file("../../xml/tests.xml");
    $qCounter = 0;
    $oCounter = 0;
    $returnString = "";
    foreach ($xml->test as $test) {
        if ($test->id == $testID){
            $returnString = $returnString.
            "<textarea name='opis' placeholder='opis' form='test_form'>".$test->description."</textarea>";
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

//vymaz test zo zoznamu testov
function TeacherDeleteTest($testID)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM testy WHERE id_test = ?");
    $stmt->bind_param("i",$testID);
    $stmt->execute();
    CloseCon($conn);

    $xml = simplexml_load_file("../../xml/tests.xml");

    foreach($xml->test as $seg)
    {
        if($seg->id == $testID) {
            $dom=dom_import_simplexml($seg);
            $dom->parentNode->removeChild($dom);
        }
    }  

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save("../../xml/tests.xml");
}

//funkcia na vytvorenie novej skupiny žiakov
function newGroup($groupName,$teacherId)
{
    $conn = OpenCon();

    $stmt = $conn->prepare("INSERT INTO groups (group_name,teacher_id) VALUES (?,?)");
    $stmt->bind_param("si",$groupName,$teacherId);
    $stmt->execute();

    CloseCon($conn);  
}

function showGroups($teacherId){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_group, group_name FROM groups WHERE teacher_id = ?");
    $stmt->bind_param("i",$teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    //vytvor tlacidla na vymazanie a ukazanie v studentList.php
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id_group']."</td>
        <td>".$row['group_name']."</td>
        <td><a href='../include/newGroup.php'>Ukáž</a></td>
        <td><a href='../include/newGroup.php'>Zmaž</a></td>
        </tr>";
    }
    CloseCon($conn);
}


?>