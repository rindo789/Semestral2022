<?php
include_once "../../main/dbh.inc.php";

function showUsers(){
    //string na vrátenie riadkov tabulky
    $return_string = "";

    $conn = OpenCon();
    $sql = 'SELECT id_uzivatel, nickname, meno_priezvysko, manager_id,
    CASE
    WHEN (SELECT s.user_student_id FROM student s WHERE s.user_student_id = u.id_uzivatel) THEN "ŠTUDENT"
    WHEN (SELECT t.uzivatelia_id_uzivatel FROM ucitel t WHERE t.uzivatelia_id_uzivatel = u.id_uzivatel) THEN "UČITEL"
    END AS type,
    CASE
    WHEN (SELECT s.user_student_id FROM student s WHERE s.user_student_id = u.id_uzivatel) THEN (SELECT s.id_student FROM student s WHERE s.user_student_id = u.id_uzivatel)
    WHEN (SELECT t.uzivatelia_id_uzivatel FROM ucitel t WHERE t.uzivatelia_id_uzivatel = u.id_uzivatel) THEN (SELECT t.id_uci FROM ucitel t WHERE t.uzivatelia_id_uzivatel = u.id_uzivatel)
    END AS type_id
    FROM uzivatelia u WHERE id_uzivatel != ? AND manager_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$_SESSION["UID"],$_SESSION["AID"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    //vytvor array s klucami pre vsetkych najdených užívateľov
    $row = $result->fetch_all(MYSQLI_ASSOC);
    if (empty($row)){
        return "Nemáte žiadnych užívateľov";
    }
    //pridaj do stringu elementy riadkov a buniek s údajmi
    foreach ($row as $element){
        $return_string = $return_string."<tr>";
            $return_string = $return_string."<td>".$element["id_uzivatel"]."</td>";
            $return_string = $return_string."<td>".$element["nickname"]."</td>";
            $return_string = $return_string."<td>".$element["meno_priezvysko"]."</td>";
            $return_string = $return_string."<td>".$element["manager_id"]."</td>";
            $return_string = $return_string."<td>".$element["type"]."</td>";
            $return_string = $return_string."<td>".$element["type_id"]."</td>";
            $return_string = $return_string."<td><a href='../include/manage_user_check.php?user_id=".$element["id_uzivatel"]."&state=delete'>Delete</a></td>";
            $return_string = $return_string."<td><a href='../index/manage_userEdit.php?user_id=".$element["id_uzivatel"]."'>Upraviť</a></td>";
        $return_string = $return_string."</tr>";
    }
    return $return_string;

}

//funckia, ktorá pridáva uzivatela pod manažera
function addUser(){

    //skontroluj ci uzivatel existuje
    if (existsUser($_POST["nick"], $_POST["email"])){
        header("location: ../manage_userNew.php");
        exit();
    }

    //vygeneruj heslo pre uzivatela
    $password = random_str();
    $_SESSION["new_pass"] = $password;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $one_time = 1;

    //vloz nového uzivatela do DB
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO uzivatelia (nickname, meno_priezvysko, email, heslo, manager_id, one_time_psw ) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssii",$_POST["nick"],$_POST["name"],$_POST["email"], $password, $_SESSION["AID"],$one_time);
    if (!$stmt->execute()) {
        echo "Chyba pri pridaní nového užívateľa";
        CloseCon($conn);
        return;
    }

    $last_id = mysqli_insert_id($conn);
    $type = "";
    $id_query = "";
    if ($_POST["type"] == "student"){
        $type = "student";
        $id_query = "user_student_id";
    }else if ($_POST["type"] == "teacher"){
        $type = "ucitel";
        $id_query = "uzivatelia_id_uzivatel";
    }

    $sql = 'INSERT into '.$type.' ('.$id_query.') VALUES (?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$last_id);
    if (!$stmt->execute()) {
        echo "Chyba pri pridaní nového užívateľa";
        CloseCon($conn);
        return;
    }

    CloseCon($conn);
}

//funckia ktora nahodne generuje heslo nového uzivatela
function random_str(
    $length = 8,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

//funkcia, ktorá kontroluje či už užívatel s podobným menom už neexistuje
function existsUser($nick, $email){
    $conn = OpenCon();
    $stmt = $conn->prepare('SELECT nickname, email FROM uzivatelia WHERE nickname = ? OR email = ?');
    $stmt->bind_param("ss",$_POST["nick"], $_POST["email"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    //vytvor array s klucami pre vsetkych najdených užívateľov
    $row = $result->fetch_assoc();
    CloseCon($conn);
    if (empty($row)){
        return false;
    } else true;

}

function deleteUser($userID){
    $conn = OpenCon();

    $stmt = $conn->prepare("SELECT id_uci FROM ucitel WHERE uzivatelia_id_uzivatel = ?");
    $stmt->bind_param("i",$userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt1 = $conn->prepare("SELECT id_student FROM student WHERE user_student_id = ?");
    $stmt1->bind_param("i",$userID);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result->fetch_assoc();

    if (mysqli_num_rows($result)==1)
    {
        CloseCon($conn);
        deleteAllTests($row["id_uci"]);
    } 
    else if (mysqli_num_rows($result1)==1)
    {
        CloseCon($conn);
        deleteAllMarks($row1["id_student"]);

    } else {
        CloseCon($conn);
        return;
    }

    $conn = OpenCon();
    $stmt = $conn->prepare('DELETE FROM uzivatelia WHERE id_uzivatel = ? AND manager_id = ?');
    $stmt->bind_param("ii",$userID, $_SESSION["AID"]);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
}

function deleteAllTests($userID){

    $xml = simplexml_load_file("../../xml/tests.xml");

    $conn = OpenCon();
    $stmt = $conn->prepare('SELECT id_test FROM testy WHERE ucitel_id_uci = ?');
    $stmt->bind_param("i",$userID);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();

    //najdi a vymaz testy ucitela v xml
    while ($row = $result->fetch_assoc()) {        
        $tests = $xml->xpath("//test[id=".$row["id_test"]."]");
    
        //vymazanie testu z xml
        foreach($tests as $seg)
        {
            $dom=dom_import_simplexml($seg);
            $dom->parentNode->removeChild($dom);
        }
    }
    CloseCon($conn);

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save("../../xml/tests.xml");
}

function deleteAllMarks($userID){
    $xml = simplexml_load_file("../../xml/answers.xml");

    $conn = OpenCon();
    $stmt = $conn->prepare('SELECT id_odp FROM odpoved WHERE id_student = ?');
    $stmt->bind_param("i",$userID);
    if (!$stmt->execute()) {
        echo "Chyba pri hladaní";
        CloseCon($conn);
        return;
    }
    $result = $stmt->get_result();
    
    //najdi a vymaz testy ucitela v xml
    while ($row = $result->fetch_assoc()) {        
        $tests = $xml->xpath("//answer[@id=".$row["id_test"]."]");
    
        //vymazanie testu z xml
        foreach($tests as $seg)
        {
            $dom=dom_import_simplexml($seg);
            $dom->parentNode->removeChild($dom);
        }
    }
    CloseCon($conn);

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save("../../xml/answers.xml");

}

function formUser($userID){

    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT * FROM uzivatelia WHERE id_uzivatel = ?");
    $stmt->bind_param("i",$userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (empty($row)){
        return "Uzivatel sa nedal najst";
    }
    CloseCon($conn);


    return "<form action='../include/manage_user_check.php?user_id=".$userID."' method='post'>
    <label for='nick'>Nickname</label>
    <input type='text' name='nick' value=".$row["nickname"].">

    <label for='name'>Meno a priezvisko</label>
    <input type='text' name='name' value=".$row["meno_priezvysko"].">

    <label for='email'>E-mail</label>
    <input type='email' name='email' value=".$row["email"].">

    <label for='type'>Typ</label>
    <p>Typ užívateľa sa nedá zmeniť</p>
    <br>

    <input type='submit' value='Zmeniť' name='update'>
    </form>
    <br><br>
    <form action='../include/manage_user_check.php?user_id=".$userID."' method='post' name='reset_pass'>
    <input type='submit' for='reset_pass' name='pass' value='Reset heslo'>
    </form>
    ";

}

function updateUser($userID){
    $conn = OpenCon();
    $stmt = $conn->prepare("UPDATE uzivatelia SET nickname = ?, meno_priezvysko = ?, email = ? WHERE id_uzivatel = ?");
    $stmt->bind_param("sssi",$_POST["nick"],$_POST["name"],$_POST["email"], $userID);
    if($stmt->execute()){
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (empty($row)){
        return "Uzivatel sa nedal najst";
    }
    CloseCon($conn);
}

function resetUser($userID){
    $password = random_str();
    $_SESSION["new_pass"] = $password;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $one_time = 1;

    $conn = OpenCon();
    $stmt = $conn->prepare("UPDATE uzivatelia SET heslo = ?, one_time_psw = ? WHERE id_uzivatel = ?");
    $stmt->bind_param("sii",$password, $one_time ,$userID);
    if($stmt->execute()){
        return;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (empty($row)){
        return "Uzivatel sa nedal najst";
    }
    CloseCon($conn);
}
?>