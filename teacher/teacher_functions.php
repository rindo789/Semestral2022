<?php
function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "usbw";
    $db = "projekt";
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Napojenie zlyhalo: %s\n". $conn -> error);
    return $conn;
}

function CloseCon($conn)
{
 $conn -> close();
}

function checkID()
{
    $conn = OpenCon();

    $stmt1 = $conn->prepare("SELECT MAX(id_test) AS max_id FROM testy");
    $stmt1->execute();
    $result = $stmt1->get_result();

    CloseCon($conn);
    
    while ($row = $result->fetch_assoc()) {
        $next_id = $row['max_id'];
        $next_id = $next_id+1;

           return $next_id;
    }
}

function newTest($testName,$next_id)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO testy (id_test, nazov_testu) VALUES (?,?)");
    $stmt->bind_param("is",$next_id,$testName);
    $stmt->execute();

    echo "test:". $testName . " bol vytvorený!";

    CloseCon($conn);
}

function showTests(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test, nazov_testu FROM testy");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id_test']."</td><td>".$row['nazov_testu']."</td></tr>";
    }
    CloseCon($conn);
}

//skus pravit tuto funckie ze js bude passovat pocet otazok a moznosti do nejakeho inputu
function saveTest($testName,$testID){
    $xml = simplexml_load_file("tests.xml");

    foreach($xml->test as $seg)
    {
        if($seg->id == $testID) {
            $dom=dom_import_simplexml($seg);
            $dom->parentNode->removeChild($dom);
        }
    }

    $test = $xml->addChild('test');
    //$test = $xml->test;
    //$test = current($xml->xpath('//test[last()]'));
    $test->addchild('id',$testID);
    $test->addchild('name',$testName);
    $test->addchild('description',$_POST["opis"]);

    foreach ($_POST["test"] as $value) {
        $question = $test->addchild('question',$value["moznost"]);
        //$question = current($xml->xpath('//question[last()]'));
        //$question = $test->question;
        $question->addchild('type',$value['type']);
        if (isset($value["type"])){
            $question->addchild('correct', 'yes');
        } else {
            $question->addchild('correct', 'no');
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

function loadTestTeacher($testID){
    
}
?>
