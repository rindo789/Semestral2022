<?php
require_once "../../main/dbh.inc.php";


function checkAnsw($array, $answerId){
    $xml = simplexml_load_file("../../xml/tests.xml");
    $test = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]");

    $xml2 = simplexml_load_file("../../xml/answers.xml");
    $answer = $xml2->xpath("//answer[@id=".$answerId."]/question");

    //vlez do otázok odpoveede študenta
    foreach($answer as $question){
        //echo $question->attributes();

        //uloz atribut otázky
        $qID = (int)$question->attributes();
        //vlezenie do mozností otázky odpovede študenta
        foreach($question as $option)
        {
            //echo "<br>". $option->attributes()." ". $option."<br>";
            //ulozenie ID možnosti
            $oID = (int)$option->attributes();
            //najdenie typu otázky
            $type = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/type");
            foreach ($type as $typename) {
                $type = $typename;
            }
            //najdenie údajov v teste
            //ak je otázka typ text potrebujeme nájsť jeho optionName na porovnávanie
            $checkOption = null;
            if ($type == "text") {
                $checkOption = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/option[@oId=".$oID."]/optionName");
            } else {
                $checkOption = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/option[@oId=".$oID."]");
            }
            
            
            //kontrola odpovede
            foreach ($checkOption as $opt){
                //ak šutdent neopovedal, tak daj mu automaticky odpoved zlú
                if ((string) $option == "/*empty*/"){
                    $option->addChild("correct", "no");
                    continue;
                }
                //ak je odpoved text, porovnaj odpovede
                //pretypuj z arrayu na string
                if ($type == "text"){
                    $text1 = (string) $option;
                    $text2 = (string) $opt;
                    if ($text1 == $text2){
                        $option->addChild("correct", "yes");
                        break;
                    } else {
                        $option->addChild("correct", "no");
                        break;
                    }
                }
                //pridaj normalne ak iné
                $option->addChild("correct", $opt->correct);
            }
        }
    }

    //uloz xml
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml2->asXML());
    $dom->save('../../xml/answers.xml');
}

//oboduj jednotlivé odpovede
//obodobanie je len zatial 0-1
function scoreAns($answerId){
    //nacitaj odpoved
    $xml2 = simplexml_load_file("../../xml/answers.xml");

    //nacitaj test
    $xml = simplexml_load_file("../../xml/tests.xml");
    $test = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question");

    //prejdenie do otázok testu
    foreach ($test as $question){
        //zober ID otázok
        $qID = (int)$question->attributes();

        //ak je viac výberová otázka
        if ($question->type == "checkbox"){
            //najdi kolko spravnych moznosti je v otázke
            $tp_count = $xml->xpath("//test[id=".$_SESSION["testIdToEdit"]."]/question[@qId=".$qID."]/option[correct='yes']");
            $tp_count = (int)count($tp_count);
            //počítadlo správnych odpovedí študenta
            $student_asnwer = 0;
            $options = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]/option");
            foreach($options as $correct){
                if ($correct->correct == "yes"){
                    $student_asnwer++;
                } else {
                    $student_asnwer = false;
                    break;
                }
            }
            
            //zisti študent vybral všetky správne možnosti a priraď mu hodnotenie za otázku
            $question_student = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]");
            foreach($question_student as $que){
                if ($student_asnwer == false){
                    $que->addChild("score", 0);
                } else if ($tp_count == $student_asnwer){
                    $que->addChild("score", 1);
                }                
            }

        } else {
        //najdi či študent správne alebo nesprávne odpovedal a pridaj mu ohodnotenie 1 alebo 0
        $answer = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]/option");
        foreach($answer as $correct){
            $question_student = $xml2->xpath("//answer[@id=".$answerId."]/question[@qId=".$qID."]");
            if ($correct->correct == "yes"){
                foreach($question_student as $que){
                    $que->addChild("score", 1);
                }
            } else {
                foreach($question_student as $que){
                    $que->addChild("score", 0);
                }
            }
        }
    }
    }

    //sprav percentualne vyhodnotenie testu a pripis do xml odpovede známku
    $sum = 0;
    $percent = 0;
    $answerXML = $xml2->xpath("//answer[@id=".$answerId."]/question");
        
        echo "<td>";
        //generuj spocitaj body za odpovede
        foreach ($answerXML as $question){
                $sum += $question->score;
        }
        echo "</td>";
    //vypocitaj kolko percent je odpoved
    if ($sum == 0) {
            $percent = 0;
    }else {
        $percent = ($sum*100)/count($answerXML);
    }
    $answerXML = $xml2->xpath("//answer[@id=".$answerId."]");
    //pridaj do xml odpovede
    foreach ($answerXML as $answer){
        if ($percent < 56) {
            $answer->addChild("mark","FX");
        } else if ($percent > 56 && $percent < 65) {
            $answer->addChild("mark","E");
        } else if ($percent > 65 && $percent < 74){
            $answer->addChild("mark","D");
        } else if ($percent > 74 && $percent < 83){
            $answer->addChild("mark","C");
        } else if ($percent > 83 && $percent < 92){
            $answer->addChild("mark","B");
        } else if ($percent > 92 && $percent <= 100){
            $answer->addChild("mark","A");
        }
    }

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml2->asXML());
    $dom->save('../../xml/answers.xml');
}
?>