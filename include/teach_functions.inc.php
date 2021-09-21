<?php
require_once "dbh.inc.php";
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
        <td><a href='editTest.php?testId=".$row['id_test']."&state=show'>Ukáž</a></td>
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
    $xml = simplexml_load_file("tests.xml");

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

    
    //$topology =new SimpleXMLElement("<Topology_Configuration/>");
    /*$xml->addChild('test');
    $test = $xml->test;
    $test->addAttribute('id',$i);*/
    //$test->addChild('id',3);
    /*$flavor=$xml->Topology_Configuration->Flavor;
    $flavor->addChild('abc');*/

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('tests.xml');

    //$xml->asXML("tests.xml");    
}

//ukaz vytvorený test
function loadTestTeacher($testID){
    $xml = simplexml_load_file("tests.xml");
    $qType = "";
    foreach ($xml->test as $test) {
        if ($test->id == $testID){
            echo    $test->name."<br>
                    <textarea>".$test->description."</textarea>";
            foreach ($test->question as $question){
                
                if ($question->type == "radio")
                {
                    $qType = "one";
                }
                else if ($question->type == "checkbox")
                {
                    $qType = "multi";
                }
                else {
                    $qType = "text";
                }
                echo "<script type='text/javascript'>CreateQuestionShow('".$qType."','".$question->questionName."');</script>";
                
                foreach ($question->option as $option){
                    echo "<script type='text/javascript'>CreateOptionShow('".$qType."','".$option->optionName."','".$option->correct."');</script>";
                }
            }
        }
    }
}

//vymaz test zo zoznamu testov
function TeacherDeleteTest($testID)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM testy WHERE id_test = ?");
    $stmt->bind_param("i",$testID);
    $stmt->execute();
    CloseCon($conn);

    /*$xml = simplexml_load_file("tests.xml");

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
    $dom->save('tests.xml');    */
}
?>
