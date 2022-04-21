//najdene vsetkych fieldsetov
var question_list = document.getElementsByTagName("fieldset");
//fieldset, ktorý označuje, ktorá otázka je na teraz
var question_now;
//pocitadlo skore
var score = 0;
//pocitadlo skore ktore bolo predtým
var score_before = 0;
//nasobitel skore
var multyplier = 1;
//najvacsi nasobitel
var max_multiplier = 0;
//pocitadlo spravnych odpovedi
var right_answers = 0;
//pocet spravnych odpovedi celkovo
var max_right_answers = 0;
//casovac
var intervarID;
//cas riesenia jednej otazky
var cas = 0;
//cas riesenie testu
var all_time = 0;
//najkratsi cas riesenia ulohy
var short_time = 999;

//schovaj fieldsety okrem prvého
function hideAll (){
    for (let i = 1; i < question_list.length; i++){
        document.getElementById(question_list[i].id).hidden = true;
    }
    question_now = question_list[0];
    document.getElementById("submit").hidden = true;
}

//schovaj aktualnu otázku a ukaz dalsiu
function nextQuestion(){
    for (let i = 0; i < question_list.length; i++){
        //ak už nie je dalsia otázka tak ukáž submit button
        if (question_list[i+1] == null){
            document.getElementById("submit").hidden = false;
            document.getElementById("next").hidden = true;
            clearInterval(intervarID);
            console.log("the end");
            return;
        }
        //schovaj aktualnu otázku a ukáž dalšiu
        if (question_list[i].id == question_now.id){
            question_now.hidden = true;
            document.getElementById(question_list[i+1].id).hidden = false;
            question_now = question_list[i+1];
            return;
        }
    }
}

//odosli odpoveď na otázku
function sendData(){
 //zober vsetky deti fieldsetu
 var children = question_now.childNodes;
 //zacni JSON file
 var answer = '{' ;
 //kontrola či bola odpoved vybraná
 var was_checked = false;
 var question_created = false;
 //počitadlo kolko moznosti bolo v otázke vybraných
 var option_num = 0;
 //pocitadlo kolko bolo zapísaných
 var option_writen = 0;

 //najde pocet elementov, ktoré boli vybrané na porovnanie potom
 for (let i = 0; i < children.length; i++){
    if (children[i].tagName == "INPUT" && children[i].type != "hidden" && children[i].checked) {
        option_num+=1;
    }
 }

 for (let i = 0; i < children.length; i++){
    if (children[i].tagName == "INPUT"){
        if (children[i].type == "text"){
            answer += '"' + getQuestionID(children[i].name) + '"'+ 
            ":{" +  '"' + getOptionID(children[i].name) + '":"' + children[i].value + '"';
        }

        if (was_checked == true){
            option_writen += 1;
            answer +='"' + getOptionID(children[i].name) + '":"';

            if (option_num == option_writen){
                answer += children[i].value + '"';
            } else {
                answer += children[i].value + '",'
            }
            was_checked = false;
        }
        //NAJPRV IDE TOTO
        //ak je odpoved vybratá uloz meno odpovede
        if (children[i].checked == true){
            if (question_created == false){
                answer += '"' + getQuestionID(children[i].name) + '":{';
            }
            question_created = true;
            was_checked = true;
         }
    }
 }

 answer = answer + '}}';
 if (answer == '{}' || answer== '{}}') answer = null;
 console.log(answer);

var jason = JSON.parse(answer);

const answer_json = JSON.stringify(jason);
const xmlhttp = new XMLHttpRequest();
xmlhttp.onload = function() {
    
    var responsteJSON = JSON.parse(this.responseText);
    
    timerEnd();
    rightCounter(responsteJSON.odpoved);
    multyEval();
    timeScore(responsteJSON.odpoved);
    addScore(responsteJSON.odpoved);
    sendGameInfo();
    timerStart();
    console.log(score);

}
xmlhttp.open("POST", "../../student/include/gameAjax.php");
xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xmlhttp.send("x=" + answer_json);
}

//najdi v mene moznosti, o ktorú otázku sa jedná
function getQuestionID(string) {
var question_id = "";
var number_found = false
    for (let i = 0; i<string.length; i++){
        if(string[i]=="["){
            number_found = true;
        } else if (string[i]=="]"){
            return question_id;
        } else if (number_found == true) {
            question_id += string[i];
        } else {
            continue;
        }
    }    
}

//najdi v mene moznosti, o ktorú možnosť sa jedná
function getOptionID(string) {
var option_id = "";
var number_found = false
    for (let i = string.length; i>0; i--){
        if(string[i]=="]"){
            number_found = true;
        } else if (string[i]=="["){
            return option_id;
        } else if (number_found == true) {
            option_id += string[i];
        } else {
            continue;
        }
    }    
}

//zisti kolko bolo spravnych odpovedi, na zaklade toho ukaz aky je nasobitel
function multyEval(){
    if (right_answers >= 6){
        multyplier = 8
    }else if (right_answers >= 4){
        multyplier = 4;
    }else if (right_answers >= 2){
        multyplier = 2;
    } else {
        multyplier = 1;
    }

    if (max_multiplier < multyplier){
        max_multiplier = multyplier;
    }

    console.log("nasobic:" + multyplier);
    
    document.getElementById("multyplier").innerHTML = "násobok: " + multyplier;
}

//zisti ci bolo odpoved dobre, ak ano zvys pocitadlo ak nie daj 0
function rightCounter(odpoved){
    if (odpoved == "yes"){
        right_answers += 1;
        max_right_answers += 1;
    } else right_answers = 0;

    console.log("spravne odpoveede:" + right_answers);
    
}

//animacia na pridanie score ak odpoved bolo dobrá, ide len o 100 bodov +-
function addScore(odpoved){
    if (odpoved == "yes"){
        score += 100*multyplier;
    } else score -= 100;
    
    let counts=setInterval(updated);
        function updated(){
            var count = document.getElementById("score");
            if (odpoved == "yes"){
                count.innerHTML=++score_before;
            } else {
                count.innerHTML=--score_before;
            }
            if(score_before===score)
            {
                clearInterval(counts);
            }
        }
}

//zacni timer, timer sa skonci po 60 sekundach
function timerStart() {
    cas = 0;
    intervarID = setInterval(addTime, 1000);

    function addTime(){
        if (cas == 60){
            clearInterval(intervarID);
        }
        cas++;
    }
}

function timerEnd() {
    all_time += cas;
    if (cas < short_time){
        short_time = cas;
    }
    console.log("celkovy cas: " + (all_time/60));
    
    clearInterval(intervarID);
}

function timeScore(answer){
    if (answer == "yes"){
        score += (60-cas)*multyplier;
    }
}

function sendGameInfo(){
    document.getElementById("score_send").value = score;
    document.getElementById("multiply_send").value = max_multiplier;
    document.getElementById("full_time").value = all_time;
    document.getElementById("short_time").value = short_time;
    document.getElementById("max_answers").value = max_right_answers;
}