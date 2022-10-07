<?php
session_start();
include_once "../../main/dbh.inc.php";

function deleteAnswer($id){
    $xml = simplexml_load_file("../../xml/answers.xml");

    $conn = OpenCon();
    $stmt = $conn->prepare("DELETE FROM odpoved WHERE id_odp = ?");
    $stmt->bind_param("i",$id);
    if (!$stmt->execute()){
        CloseCon($conn);
        return;
    }
    CloseCon($conn);

    //najdi a vymaz testy ucitela v xml      
    $tests = $xml->xpath("//answer[@id=".$id."]");
    
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
    $dom->save("../../xml/answers.xml");
}

?>