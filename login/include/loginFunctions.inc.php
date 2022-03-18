<?php
//errory pre sign up
function missingInput($meno,$nick,$email,$heslo,$hesloZnova,$typ)
{
    if (empty($meno) || empty($nick) || empty($email) || empty($heslo) || empty($hesloZnova) || empty($typ))
    {
        return true;
    } else return false;
}

function invalidUserID($nick)
{
 if (preg_match("/^[a-zA-z0-9]*$/",$nick))
 {
     return false;
 } else return true;
}

function invalidEmail($email)
{
 if (filter_var($email, FILTER_VALIDATE_EMAIL))
 {
     return false;
 } else return true;
}

function pwdMissmatch($heslo,$hesloZnova)
{
    if ($heslo == $hesloZnova)
    {
        return false;
    } else return true;
}

function existUserID($nick)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT nickname FROM uzivatelia WHERE nickname = '$nick'");
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);
    if (mysqli_num_rows($result)>0)
    {
        return true;
    } else return false;
}

//errory pre login
function missingInputLogin($nick,$heslo)
{
    if (empty($nick) || empty($heslo))
    {
        return true;
    } else return false;
}

function invalidUserIDLogin($nick)
{
 if (preg_match("/^[a-zA-z0-9]*$/",$nick))
 {
     return false;
 } else return true;
}

//vypisanie erroru
function errorEcho()
{
    if (empty($_GET["error"])) exit();
    $error = $_GET["error"];
    if ($error == "missingInput") echo "<p>Nezadali ste údaj!</p>";
    else if ($error == "invalidID") echo "<p>Zle zadaný nick!</p>";
    else if ($error == "userExists") echo "<p>užívateľ už existuje</p>";
    else if ($error == "invalidEmail") echo "<p>Zlý email!</p>";
    else if ($error == "pwdMissmatch") echo "<p>Heslá sa nezhodujú</p>";
    else if ($error == "signUpUserIdNotFound") echo "<p>Niečo sa stalo zle!</p>";
    else if ($error == "pwdWrong") echo "<p>Zadali ste zlé heslo!</p>";
    else echo "<p>Úspešne ste sa registrovli!</p>";
}
//vytvorenie nového uzivatela
function createUser($meno,$nick,$email,$heslo,$typ)
{
    $hashedHeslo = password_hash($heslo, PASSWORD_DEFAULT);
    $conn = OpenCon();
    $stmt = $conn->prepare("INSERT INTO uzivatelia (nickname, meno_priezvysko,email,heslo) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $nick, $meno, $email, $hashedHeslo);
    $stmt->execute();
    createType($typ ,$nick, $conn);
    CloseCon($conn);
}
//a jeho typu
function createType($typ,$nick,$conn)
{
    $userID = 0;
    $stmt = $conn->prepare("SELECT id_uzivatel FROM uzivatelia WHERE nickname = '$nick'");
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result)==0)
    {
        CloseCon($conn);
        header("location: ../index/signup.php?error=signUpUserIdNotFound");
        exit();
    }
    $row = $result->fetch_assoc();
    $userID = $row['id_uzivatel'];
    
    if ($typ == "Teacher") {
        $stmt = $conn->prepare("INSERT INTO ucitel (uzivatelia_id_uzivatel) VALUES (?)");
    } else if ($typ == "Student") {
        $stmt = $conn->prepare("INSERT INTO student (user_student_id ) VALUES (?)");
    }
    
    $stmt->bind_param("i",$userID);
    $stmt->execute();
}

//prihlasenie uzivatela
function loginUser ($nick,$heslo)
{
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_uzivatel ,nickname,heslo FROM uzivatelia WHERE nickname = ?");
    $stmt->bind_param("s",$nick);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (mysqli_num_rows($result)==0)
    {
        CloseCon($conn);
        header("location: ../index/login.php?error=invalidID");
        exit();
    }
    
    $row = $result->fetch_assoc();
    $checkHeslo = password_verify($heslo,$row["heslo"]);
    if ($checkHeslo == false)
    {
        CloseCon($conn);
        header("location: ../index/login.php?error=pwdWrong");
        exit(); 
    }
    session_start();
    $_SESSION["Nick"] = $row["nickname"];
    $_SESSION["UID"] = $row["id_uzivatel"];
    CloseCon($conn);    
}

function checkUserType(){
    session_start();
    $conn = OpenCon();    

    //ak je uzivatel ucitel
    $stmt = $conn->prepare("SELECT id_uci FROM ucitel WHERE uzivatelia_id_uzivatel = ?");
    $stmt->bind_param("i",$_SESSION['UID']);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt1 = $conn->prepare("SELECT id_student FROM student WHERE user_student_id = ?");
    $stmt1->bind_param("i",$_SESSION['UID']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();


    if (mysqli_num_rows($result)==1)
    {
        $row = $result->fetch_assoc();
        CloseCon($conn);
        $_SESSION["TID"] = $row["id_uci"];
        header("location: ../../teacher/index/teacher.php");
        exit();
    } 
    else if (mysqli_num_rows($result1)==1)
    {
        $row1 = $result1->fetch_assoc();
        CloseCon($conn);
        $_SESSION["SID"] = $row1["id_student"];
        header("location: ../../student/index/student.php");
        exit();
    }
    else {
        header("location: ../index/login.php?error=userNotIdent");
        exit();
    }
}

//check či je uzivatel prihlaseny
function loginCheck(){
    if (empty($_SESSION["Nick"]) || empty($_SESSION["UID"])){
    session_unset();
    session_destroy();
    header("location: ../../login/index/login.php");
    exit();
    }
}

?>