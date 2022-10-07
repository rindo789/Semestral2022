<?php
require_once "../../main/dbh.inc.php";

//funckia na kontrolu či sa uloženie testu uskutočnuje v čase testu
function testActive(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_schedule FROM schedule WHERE id_schedule = ?");
    $stmt->bind_param("i",$_SESSION["schedule"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    CloseCon($conn);
    if (isset($row["id_schedule"]))
    {
        return $row["id_schedule"];
    }    
}

//funckia na kontrolu či študent už test spravil
function testTaken($testID){

    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test FROM odpoved WHERE id_test = ? AND id_student = ?");
    $stmt->bind_param("ii", $testID, $_SESSION["SID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    CloseCon($conn);

    if(mysqli_num_rows($result)>0){
        return true;
    } else return false;
}

//skontroluj či test alebo score je pre študenta
function studentBelong($type){
    if ($type == "test"){
        $xml = simplexml_load_file("../../xml/tests.xml");
        $testXML = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]");
        $group_id = 0;

        foreach ($testXML as $test){
            $group_id = (int) $test->group;
            //echo $group_id;
        }        
        
        $xml = simplexml_load_file("../../xml/groups.xml");
        $groupXML = $xml->xpath("//group[@id=".$group_id."]/students/student");
        foreach ($groupXML as $student){            
            if ($_SESSION["SID"] == $student){                
                return true;
            }
        }
    } else if ($type == "score"){
        $conn = OpenCon();
        $stmt = $conn->prepare("SELECT id_student FROM odpoved WHERE id_odp = ? AND id_student = ?");
        $stmt->bind_param("ii", $_SESSION["testIdToEdit"], $_SESSION["SID"]);
        $stmt->execute();
        $result = $stmt->get_result();        
        CloseCon($conn);
        if (mysqli_num_rows($result) > 0){
            return true;
        }
    }
    return false;
}
?>