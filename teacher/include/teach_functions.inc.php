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

    //ak boli hodnoty datumu a času settnuté
    if(isset($_POST["date_time_on"]) && isset($_POST["date_time_off"])){
        //ulozenie času začatia a konca
        $date_time_on = $_POST["date_time_on"];
        $date_time_off = $_POST["date_time_off"];

        //pozri či test nebol už naplánovaný a planúje sa ešte pred začatím testu
        //tak zmen najnovší záznam z testu
        //ak sa plánuje po teste pridaj nový záznam

        //kontrola či test ešte nezačal
        $start_time = null;
        $end_time = null;

        $conn = OpenCon();
        $stmt = $conn->prepare("SELECT zaciatok, koniec FROM hotovo WHERE id_hotovo = (SELECT MAX(id_hotovo) FROM hotovo WHERE id_test = ?)");
        $stmt->bind_param("i",$testID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        CloseCon($conn);

        //ak najde nejaký zaznam
        //muselo zo ist cez isset lebo ukozovalo zlý počet riadkov ak nebol žiaden záznam nájdený
        if (isset($row["zaciatok"])){
            $start_time = $row["zaciatok"];
            $end_time = $row["koniec"];

            //ak čas našlo a test ešte nezačal
            if ($start_time > date("Y-m-d H:i:s", time()))
            {
                $conn = OpenCon();
                $stmt = $conn->prepare("UPDATE hotovo SET zaciatok = ?, koniec = ? WHERE id_hotovo = (SELECT MAX(id_hotovo) FROM hotovo WHERE id_test = ?)");
                $stmt->bind_param("ssi",$date_time_on, $date_time_off, $testID);
                $stmt->execute();
                CloseCon($conn);
            //ak skončil test
            }else if ($end_time < date("Y-m-d H:i:s", time())){
                $conn = OpenCon();
                $stmt = $conn->prepare("INSERT INTO hotovo (id_test, zaciatok, koniec) VALUES (?,?,?)");
                $stmt->bind_param("iss",$testID,$date_time_on,$date_time_off);
                $stmt->execute();
                CloseCon($conn);
        }       
        } else {
            $conn = OpenCon();
            $stmt = $conn->prepare("INSERT INTO hotovo (id_test, zaciatok, koniec) VALUES (?,?,?)");
            $stmt->bind_param("iss",$testID,$date_time_on,$date_time_off);
            $stmt->execute();
            CloseCon($conn);
        }
    }    

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

//vypisanie skupin pri vytváraní testu
function echoGroups(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_group, group_name FROM groups WHERE teacher_id = ?");
    $stmt->bind_param("i",$_SESSION["TID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);

    echo "<select name='group'>";
    echo "<option value='none'>none</option>";

    while ($row = $result->fetch_assoc()){
        echo "<option value=".$row["id_group"].">".$row["group_name"]."</option>";
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
    if ($stmt->execute() == false){
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
        <td><a href='../include/teacher.inc.php?groupId=".$row['id_group']."&state=show'>Ukáž</a></td>
        <td><a href='../include/teacher.inc.php?groupId=".$row['id_group']."&state=delete'>Zmaž</a></td>
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
    //vymazanie skupiny
    foreach($xml->group as $seg)
    {
        if($seg["id"] == $groupId) {
            $dom=dom_import_simplexml($seg);
            $dom->parentNode->removeChild($dom);
        }
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

//HODNOTENIA

//vypis tabulku testov ktoré boli vykonané
function scoreTable(){
echo "<table>";

//vypis vsetky zaznamy pre naplánované testy
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_hotovo, h.id_test, nazov_testu, zaciatok, koniec FROM hotovo h
                            JOIN testy t ON h.id_test = t.id_test 
                            WHERE ucitel_id_uci = ?");

    $stmt->bind_param("i",$_SESSION["TID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) != 0){        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <th>id plan</th>
            <th>ID testu</th>
            <th>Nazov</th>
            <th>Datum začatia</th>
            <th>Datum ukončenia</th>
            </tr>";
            
            echo "<tr>
            <td>".$row['id_hotovo']."</td>
            <td>".$row['id_test']."</td>
            <td>".$row['nazov_testu']."</td>
            <td>".$row['zaciatok']."</td>
            <td>".$row['koniec']."</td>
            </tr>";

            scoreAllStudents($row['id_hotovo']);
        }
    }    
    CloseCon($conn);


echo "</table>";
}

//vypis odpovede študentov v tabulke hodnotení
function scoreAllStudents($scheduleID){
    $xml = simplexml_load_file("../../xml/answers.xml");
    $answerXML = $xml->xpath("//answer[schedule=".$scheduleID."]");
    echo "<tr id='test'>
            <th>Odpoved</th>
            <th>Študent</th>
            <th>Hodnotenie</th>
        </tr>";

    foreach ($answerXML as $answer){
        $odpovedID = $answer["id"];

        $conn = OpenCon();
        $stmt = $conn->prepare("SELECT meno_priezvysko from uzivatelia where  id_uzivatel =
                                 (SELECT user_student_id FROM student WHERE id_student = 
                                 (SELECT id_student FROM odpoved WHERE id_odp = ?))");
        $stmt->bind_param("i", $odpovedID);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result)==0){
            CloseCon($conn);
            echo "no student";
            return;
        }
        
        $row = $result->fetch_assoc();
        CloseCon($conn);        

        echo "<tr>
        <td>".$odpovedID."</td>
        <td>".$row["meno_priezvysko"]."</td>
        <td>".$answer->mark."</td>
        </tr>";
        
    }

}
?>