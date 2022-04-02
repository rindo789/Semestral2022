<?php
header('Content-type: application/json');
session_start();
$request_json = json_decode($_POST["x"],true);

$xml = simplexml_load_file("../../xml/tests.xml");
//typ otázky
$type = null;

//vlez do otázky odpovede
foreach($request_json as $question_id => $option) {
    //pozri aký je typ otázky
    $typeXML  = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$question_id."]");
    foreach ($typeXML as $type){
      $type = $type->type;
    }
    //pre checkbox
    $correct = [];
    $options = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$question_id."]/option[correct='yes']");
    //vlez do mozností odpovede
    foreach ($option as $id => $value) {
      //vlez do moznosti testu
      $optionXML = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$question_id."]/option[@oId=".$id."]");
      foreach ($optionXML as $option_test){
        //ak je otázka typu text porovnaj texty odpovedí
        if ($type == "text"){
          if ($value == $option_test->optionName){
            $send["odpoved"] = "yes";
            echo json_encode($send);
          } else {
            $send["odpoved"] = "no";
            echo json_encode($send);
          }       
          //ak je typ checkbox pridaj id odpovede do zoznamu na neskoršiu kontrolu
        } else if ($type == "checkbox"){
          array_push($correct, $id);
         //pre ostatné (radio) odpoved
        } else if ($option_test->correct == "yes"){
           $send["odpoved"] = "yes";
           echo json_encode($send);
          //echo "yes";
        } else{
          $send["odpoved"] = "no";
           echo json_encode($send);
        }
      }
    }
    if ($type == "checkbox"){
      checkYes($correct, $options);
    }
  }

  function checkYes($correct, $options){
    $yes = [];
        
    foreach ($options as $option){
      array_push($yes, (int)$option->attributes());
    }

    if ($correct == $yes)
    {
      $send["odpoved"] = "yes";
      echo json_encode($send);
      exit();
    } else {
      $send["odpoved"] = "no";
      echo json_encode($send);
      exit();
    }
  }

?>