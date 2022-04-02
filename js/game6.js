var question_list = document.getElementsByTagName("fieldset");
var question_now;
var score = 0;
var score_before = 0;

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
        //ak už nie je dalsia otázka tak nič nerob
        if (question_list[i+1] == null){
            document.getElementById("submit").hidden = false;
            document.getElementById("next").hidden = true;
            console.log("The end");
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
    
    addScore(responsteJSON.odpoved);
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

function addScore(odpoved){
    if (odpoved == "yes"){
        score += 100;
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
