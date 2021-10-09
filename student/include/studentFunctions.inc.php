<?php

require_once "../../main/dbh.inc.php";
function showTests(){
    $conn = OpenCon();
    $stmt = $conn->prepare("SELECT id_test, nazov_testu FROM testy");
    $stmt->execute();
    $result = $stmt->get_result();
    //vytvor tlacidla na vymazanie a ukazanie v teacher.php
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id_test']."</td>
        <td>".$row['nazov_testu']."</td>
        <td><a href='../include/takeTest.inc.php?testId=".$row['id_test']."'>Ukáž</a></td>
        </tr>";
    }
    CloseCon($conn);
}

function loadTestStudent($testID){
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
                        "<input type='checkbox' name='test[".$qCounter."][moznost][correct".$oCounter."]'>";
                        
                        $returnString = $returnString.
                        "<input type='text' placeholder='možnosť' name='test[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'><br>";
                    }  
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='test[".$qCounter."][type]'>";
                    $oCounter = 0;
                } else if ($question->type == "text") {
                        foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type='text' placeholder='možnosť' name='test[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'><br>";
                    }
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='test[".$qCounter."][type]'>";                    
                    $oCounter = 0;
                } else {
                    foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type=".$question->type." name='test[".$qCounter."][moznost][correct]'>";
                        
                        $returnString = $returnString.
                        "<input type='text' placeholder='možnosť' name='test[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'><br>";
                    } 
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='test[".$qCounter."][type]'>";                    
                    $oCounter = 0;                    
                }
                $returnString = $returnString.
                "</fieldset>";
            }
        }
    }
    echo $returnString;
}

function sendAnsw($array)
{
    
}
?>