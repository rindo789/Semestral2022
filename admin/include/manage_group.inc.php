<?php
include_once "../../main/dbh.inc.php";

function deleteGroup($groupID){
    $xml = simplexml_load_file("../../xml/groups.xml");

    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM groups WHERE id_group = ?");
    $stmt->bind_param("i",$groupID);
    if (!$stmt->execute()){
        CloseCon($conn);
        return;
    }
    CloseCon($conn);

    //najdi a vymaz testy ucitela v xml      
    $tests = $xml->xpath("//group[@id=".$groupID."]");
    
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
    $dom->save("../../xml/groups.xml");
}

function saveGroup($studentArray){
    $xml = simplexml_load_file("../../xml/groups.xml");
    //vymazanie študentov
    foreach($xml->group as $seg)
    {
        if($seg["id"] == $_SESSION["groupEdit"]) {
            $dom=dom_import_simplexml($seg->students);
            $dom->parentNode->removeChild($dom);
        }
    }

    //nájdenie skupiny
    $groups = $xml->xpath("//group[@id=".$_SESSION["groupEdit"]."]");
    //pridanie skupiny študentov
    $studentXML = $groups[0]->addChild('students');
    foreach($studentArray as $students) {
        //check ak to je iné ako čislo
        if (!preg_match("/^[0-9]*$/",$students) || empty($students)){
            //echo "zlý input<br>";
            continue;
        }
        //hladanie či uzivatel existuje
        $conn = OpenCon();
        $stmt = $conn->prepare("SELECT id_student from student where id_student = ?");
        $stmt->bind_param("i", $students);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        CloseCon($conn);

        if(mysqli_num_rows($result) == 0){
            continue;
        }

        $studentXML[0]->addchild('student',$row["id_student"]);
    }
    //uloženie
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/groups.xml');
}

function loadStudents(){
    $xml = simplexml_load_file("../../xml/groups.xml");
    $group = $xml->xpath("//group[@id=".$_GET["group_id"]."]/students/student");
    if (count($group) == 0) return;
    //zistenie viacerých mien pre studentov
    //napln array
    $ids=[];
    foreach($group as $students){
        array_push($ids, (int)$students);
    }

    //vytvor string, ktorý pripojí kolko mien študentov podla ich ID sa má vyhladat
    //vytvor string do SQL query s niekolkymi ?
    $otazniky = implode(",",array_fill(0, count($ids), "?"));
    //vytvor string s parametramy pr bind parameters
    $parameters = str_repeat("i",count($ids));
    //zorad IDcka v array, SQL si tieto hodnoty zoraduje sám???
    sort($ids, SORT_NUMERIC);

    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT meno_priezvysko from uzivatelia where id_uzivatel IN 
    ( SELECT user_student_id from student WHERE id_student IN (".$otazniky."))");
    $stmt->bind_param($parameters, ...$ids);
    if (!$stmt->execute()) return;    
    $result = $stmt->get_result();
    CloseCon($conn);

    //vypíš podla poctu najdených IDcok mená a ich idecka na editovanie
    for ($i = 0; $i<count($ids);$i++){
        $row = $result->fetch_assoc();
        $para_id = "para".$i;
        echo "<fieldset id=field".$i.">";
            echo "<p id=para".$i.">".$row["meno_priezvysko"]."</p>";
            echo "<input type='text' value='$ids[$i]' name='student[]' placeholder='Vložte id študenta'"; 
            echo 'onkeyup=searchName(this.value,'."'".$para_id."'".')>';
            echo "<button type='button' onClick='DeleteStudent(this.value)' value=".$i.">x</button>
            <br>";
            echo "</fieldset>";
    }
}
?>