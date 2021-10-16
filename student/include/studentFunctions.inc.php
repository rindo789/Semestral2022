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

function RandTest($test)
{
    //echo count($test)-3;
    $array = [];
    $qNumber = 0;
    foreach ($test->question as $question)
    {
        $qNumber++;
        $array[$qNumber]=[];
        $numbers = [];
        //echo count($question)-2;
        for ($i = 1; $i < count($question)-1;$i++)
        {
            array_push($numbers, $i);
        }
        shuffle($numbers);
        $array[$qNumber] = $numbers;
        $quest=[];
        for ($i = 1; $i < count($test)-2;$i++)
        {
            array_push($quest, $i);
        }
    }
    shuffle($quest);
    //print_r($quest);
    //print_r($array);
    $shuffled["questions"] = $quest;
    $shuffled["options"] = $array;
    print_r($shuffled);
    return $shuffled;
    /*foreach($shuffled["questions"] as $item)
    {
        echo $item;
    }*/
}

function loadTestStudent($testID){
    $xml = simplexml_load_file("../../xml/tests.xml");
    //$thequestion = $xml->xpath("//test[id=".$testID."]/question[@qId='1']/option[@oId=1]/correct");
    //print_r($thequestion);
    //AKO TO BUDE VYTAHOVAT Z TOHO INFO!!!???
    $qCounter = 0;
    $oCounter = 0;
    $returnString = "";
    
    foreach ($xml->test as $test) {
        if ($test->id == $testID){
            $shuffled = RandTest($test);

            $returnString = $returnString.
            "<p>".$test->description."</p>";
            //ukazanie otazky
            $popped = 1;
        while ($popped != NULL) {
            $popped = array_pop($shuffled["questions"]);
            echo $popped;
            # code...
        

            foreach ($test->question as $question){
                
                $qCounter++;

                $returnString = $returnString.
                "<fieldset id='fieldset".$qCounter."'>
                <p>".$question->questionName."</p>
                <br>";
                
                //ukazanie moznosti v otazke
                if($question->type == "checkbox")
                {
                    foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type='checkbox' name='answer[".$qCounter."][moznost][correct".$oCounter."]'>";
                        
                        $returnString = $returnString.
                        "<p>".$option->optionName."</p>
                        <input type='hidden' name='answer[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'>
                        <br>";
                    }  
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='answer[".$qCounter."][type]'>";
                    $oCounter = 0;
                } else if ($question->type == "text") {
                        foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type='text' placeholder='odpoved' name='answer[".$qCounter."][moznost][".$oCounter."]'>";
                    }
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='answer[".$qCounter."][type]'>";                    
                    $oCounter = 0;
                } else {
                    foreach ($question->option as $option){
                        $oCounter++;
                        $returnString = $returnString.
                        "<input type=".$question->type." name='answer[".$qCounter."][moznost][correct]'>";
                        
                        $returnString = $returnString.
                        "<p>".$option->optionName."</p>
                        <input type='hidden' placeholder='možnosť' name='answer[".$qCounter."][moznost][".$oCounter."]' value='".$option->optionName."'>
                        <br>";
                    } 
                    $returnString = $returnString.
                    "<input type='hidden' value='".$question->type."' name='answer[".$qCounter."][type]'>";                    
                    $oCounter = 0;                    
                }
                $returnString = $returnString.
                "</fieldset>";
            }
        }
        }
    }
    echo $returnString;
}

function checkAnsw($testID,$array){
    $xml = simplexml_load_file("../../xml/tests.xml");
    foreach ($xml->test as $test) {
        if ($test->id == $testID){

        }
    }
    $qCount = count($test);
    $question = $test[0][0];
    //print_r($test);
    print_r($question);
    /*
    //pridanie question nodu
    foreach ($array["test"] as $value) {
        
        //pridanie option a correct nodu
        $correct = false;
        foreach ($value["moznost"] as $key) {
            //preskoc correct node v option zozname
            if ($key=="on") {$correct = true; continue;}

            if ($correct == true){
                $option->addchild('correct', 'yes');
                $correct = false;
            } else {
                $option->addchild('correct', 'no');
            }
        }
    }
    */
}
?>