<?php
include_once "../../main/dbh.inc.php";

function AdminDeleteTest($testID)
{
    //vymaz test z databázy
    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM testy WHERE id_test = ?");
    $stmt->bind_param("i",$testID);
    if (!$stmt->execute()){
        CloseCon($conn);
        return;
    }
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

function checkRights($testID){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test from testy
    JOIN ucitel on ucitel_id_uci = id_uci
    JOIN uzivatelia on uzivatelia_id_uzivatel = id_uzivatel
    WHERE id_test = ? AND manager_id = ?");
    $stmt->bind_param("ii",$testID, $_SESSION["AID"]);
    if (!$stmt->execute()){
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) == 0) {
        CloseCon($conn);
        return false;
    }
    CloseCon($conn);
    return true;
}

function adminGroups(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_group, group_name FROM groups WHERE teacher_id = (SELECT ucitel_id_uci FROM testy WHERE id_test = ?)");
    $stmt->bind_param("i",$_SESSION["testIdToEdit"]);
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

function echoTestName(){
    $xml = simplexml_load_file("../../xml/tests.xml");
    $testXml = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/name");
    foreach ($testXml as $value) {
        $_SESSION["testName"] = (string)$value;
        echo $value;
    }
}

function createNewTest($testName,$teacherId){
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO testy (nazov_testu,ucitel_id_uci) VALUES (?,?)");
    $stmt->bind_param("si",$testName,$teacherId);
    $stmt->execute();
    $new_id = mysqli_insert_id($conn);
    CloseCon($conn);
    return $new_id;

    
}

function generateTest($testName,$testID){
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

function echoTeachers(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT uc.id_uci, uz.meno_priezvysko, uz.id_uzivatel FROM ucitel uc
    JOIN uzivatelia uz ON uc.uzivatelia_id_uzivatel = uz.id_uzivatel
    WHERE uz.manager_id = ?");
    $stmt->bind_param("i",$_SESSION["AID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);

    while ($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$row["id_uzivatel"]."</td>";
        echo "<td>".$row["meno_priezvysko"]."</td>";
        echo "<td>".$row["id_uci"]."</td>";
        echo '<td><button type="button" onclick="selectTeacher('.$row["id_uci"].','."'".$row["meno_priezvysko"]."'".')">Vyber</button></td>';
        echo "</tr>";
    }
}
?>