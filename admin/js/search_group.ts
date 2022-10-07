let y;

function countFields(){
    var x = document.getElementsByTagName("fieldset");
    y = x.length-1;
    console.log(y);
}

function searchGroup(){
    var group_name = document.getElementById("group_name")!.value;
    var table = <HTMLTableElement>document.getElementById("group_table");
    
    //vymaz na zaciatku vyhladavanie predoslí výsledok
    for (let i = 1; i < table.children.length; i++) {
        table.children[i].remove();
      }

    var responseJSON;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {      
        if (this.responseText == "Nenašla sa žiadna skupina" || this.responseText == "Chyba pri hladaní"){
            return;
        }
        
        responseJSON = JSON.parse(this.responseText);
    };
    xmlhttp.open("GET", "../include/search_group.ajax.php?group_name="+group_name, false);
    xmlhttp.send();
    
    if (responseJSON == undefined){
        return;
    }

    //vytvor telo tabulky kde pojde výsledok
    var table_body = table.createTBody();
    //chod do arraya najdených osôb
    responseJSON.forEach(element => {
        //vytvor nový riadok
        var row = table_body.insertRow();

        //pre každy stlpec vytvor novú bunku
        for (var key in element){
            var cell = row.insertCell();
            cell.innerHTML = element[key];
        }        
        //vytvor tlacidlo pre vymazanie pouzivatela a pridaj ho do novej bunky
        var link_to = document.createElement("a");
        link_to.href = "../include/manage_group_check.php?group_id="+element["id_group"]+"&state=delete";
        link_to.innerHTML = "Delete";
        var cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);

        //vytvor tlacidlo na prechod do editácie užívatela
        link_to = document.createElement("a");
        link_to.href = "../index/manage_groupEdit.php?group_id="+element["id_group"];
        link_to.innerHTML = "Upraviť";
        cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);
    });
}

function addField(): void{

    var field = document.createElement("fieldset");
    var student_id = document.createElement("input");
    var para = document.createElement("p");
    var delete_button = document.createElement("button");
    field.id = 'field'+studentIncrement();

    para.id = "para" + y;

    student_id.type = "text";
    student_id.name = 'student[]';
    student_id.placeholder = "Vložte id študenta";
    student_id.setAttribute("onkeyup", "searchName(this.value,'"+para.id+"')");

    delete_button.setAttribute("onclick", "DeleteStudent(this.value)");
    delete_button.value = y;
    delete_button.type = "button";
    delete_button.innerText = "x";

    
    document.getElementById("flex_buttons").insertAdjacentElement("beforebegin", field);
    field.insertAdjacentElement("beforeend",para);
    field.insertAdjacentElement("beforeend",student_id);
    field.insertAdjacentElement("beforeend",delete_button);
}

function searchName(str, para_id){
    if (str.length == 0) {
        document.getElementById(para_id).innerHTML = "";
        return;
      } else {
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
          document.getElementById(para_id).innerHTML = this.responseText;
        }
      xmlhttp.open("GET", "../include/search_name.ajax.php?q=" + str);
      xmlhttp.send();
      }
}

function DeleteStudent(value){
    document.getElementById("field"+value).remove();
}
function studentIncrement(){
    y += 1;
    return y;
}
function studentDecrese(){
    y -= 1;
}