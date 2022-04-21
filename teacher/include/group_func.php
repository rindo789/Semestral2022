<?php
require_once "../../main/dbh.inc.php";
//SKUPINY
//vytvorenie skupiny
function newGroup($groupName, $teacherId){
    //vytvor nový záznam
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT into groups (group_name, teacher_id) VALUES (?, ?)");
    $stmt->bind_param("si", $groupName, $teacherId);
    $stmt->execute();

    //nájdi nový záznam
    $stmt = $conn->prepare("SELECT MAX(id_group) as newgroup from groups where teacher_id = ?");
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);

    //pridaj zatial prázdy záznam do xml
    $xml = simplexml_load_file("../../xml/groups.xml");
    $group = $xml->addChild('group');
    $group->addAttribute('id',$row["newgroup"]);
    $group->addchild('name',$groupName);
    $group->addchild('students');

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/groups.xml');

    $_SESSION["groupEdit"] = $row["newgroup"];
}

function showGroups(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_group, group_name from groups where teacher_id = ?");
    $stmt->bind_param("i", $_SESSION["TID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);

    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id_group']."</td>
        <td>".$row['group_name']."</td>
        <td><a href='../include/test_states.php?groupId=".$row['id_group']."&state=show'>Ukáž</a></td>
        <td><a href='../include/test_states.php?groupId=".$row['id_group']."&state=delete'>Zmaž</a></td>
        </tr>";
    }
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
        //check ak to je iné ako čiílo
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

//nacitanie mien a id v editore skupín
function loadStudents(){
    $xml = simplexml_load_file("../../xml/groups.xml");
    $group = $xml->xpath("//group[@id=".$_SESSION["groupEdit"]."]/students");
    if (count($group) == 0) return;
    //zistenie viacerých mien pre studentov
    //pridanie idčiek do stringu
    $idstring = "";
    //pri prve nedaj čiarku, robí to až druhé id
    $firstout = false;
    foreach ($group[0] as $x) {
        if ($firstout == true) {
            $idstring = $idstring .", ". $x;
        }
        else {
            $idstring = $x;
            $firstout = true;
        }
    }
    //hladanie všetkých užívateľov
    $conn = OpenCon();
    $sql = "SELECT meno_priezvysko from uzivatelia 
            where id_uzivatel = (
            SELECT user_student_id from student WHERE id_student IN ('$idstring'))";
        if ($result = $conn->query($sql))
        {
            $row = $result->fetch_assoc();
        }
    CloseCon($conn);

    //znovu pridanie inputov
    $sCounter = 0;
    foreach ($group[0] as $x) {
        $sCounter++;
        echo "<fieldset id=field".$sCounter.">";
        echo $row["meno_priezvysko"].
        " <input type='text' value='$x' name='student[]'> 
        <button type='button' onClick='DeleteStudent(this.value)' value=".$sCounter.">x</button>
        <br>";
        echo "</fieldset>";
    }
}

function groupDelete($groupId){
    $xml = simplexml_load_file("../../xml/groups.xml");
    $group = $xml->xpath("//group[@id=".$groupId."]");

    //vymazanie skupiny
    foreach($group as $seg)
    {
        $dom=dom_import_simplexml($seg);
        $dom->parentNode->removeChild($dom);
    }

    //uloženie xml
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save('../../xml/groups.xml');

    //vymazanie z databázy
    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM groups WHERE id_group = ?");
    $stmt->bind_param("i", $groupId);
    $stmt->execute();
    CloseCon($conn);
}
?>