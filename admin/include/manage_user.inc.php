<?php
session_start();
include_once "../../login/include/loginFunctions.inc.php";
include_once "../../main/dbh.inc.php";
loginCheck();

function showUsers(){
    //string na vrátenie riadkov tabulky
    $return_string = "";

    $conn = OpenCon();
    $sql = 'SELECT id_uzivatel, nickname, meno_priezvysko, manager_id,
    CASE
    WHEN (SELECT s.user_student_id FROM student s WHERE s.user_student_id = u.id_uzivatel) = u.id_uzivatel THEN "ŠTUDENT"
    WHEN (SELECT t.uzivatelia_id_uzivatel FROM ucitel t WHERE t.uzivatelia_id_uzivatel = u.id_uzivatel) = u.id_uzivatel THEN "UČITEL"
    END AS type
    FROM uzivatelia u WHERE id_uzivatel != ? AND manager_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$_SESSION["UID"],$_SESSION["UID"]);
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
            $return_string = $return_string."<td><a href='../include/manage_user_check.php?user_id=".$element["id_uzivatel"]."&state=delete'>Delete</a></td>";
            $return_string = $return_string."<td><a href='../include/manage_user_check.php?user_id=".$element["id_uzivatel"]."&state=show'>Upraviť</a></td>";
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
    $password = password_hash($password, PASSWORD_DEFAULT);

    //vloz nového uzivatela do DB
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO uzivatelia (nickname, meno_priezvysko, email, heslo, manager_id ) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssi",$_POST["nick"],$_POST["name"],$_POST["email"], $password, $_SESSION["UID"]);
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
?>