var teacher;

function showTeacherSelection(){
    document.getElementById("teacher_select_modal").style.display = "block";
}

function closeTeacherSelection(){
    document.getElementById("teacher_select_modal").style.display = "none";
}

function selectTeacher(teacher_id, teacher_name: string){
    teacher = "not_null";
    document.getElementById("selected_teacher").innerText = teacher_name;
    document.getElementById("teacher_selected_value").value = teacher_id;
    document.getElementById("teacher_select_modal").style.display = "none";

    document.getElementById("selected_group").innerText = "";
    document.getElementById("group_selected_value").value = null;
}
//groups
function showGroupSelection(){
    console.log(teacher);
    
    
    if (teacher == undefined) {
        window.alert("Vyberte najprv učiteľa!");
        return;
    }
    searchGroupModal();

    document.getElementById("group_select_modal").style.display = "block";
}

function closeGroupSelection(){
    document.getElementById("group_select_modal").style.display = "none";
}

function selectGroup(Group_id, Group_name:string){
    document.getElementById("selected_group").innerText = Group_name;
    document.getElementById("group_selected_value").value = Group_id;
    document.getElementById("group_select_modal").style.display = "none";
}

function searchGroupModal(){
    var teacher_id = document.getElementById("teacher_selected_value")!.value;
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
    xmlhttp.open("GET", "../include/create_test.ajax.php?teacher_id="+teacher_id, false);
    xmlhttp.send();
    
    if (responseJSON == undefined){
        return;
    }

    //vytvor telo tabulky kde pojde výsledok
    var table_body = table.createTBody();
    //chod do arraya najdených skupin
    responseJSON.forEach(element => {
        //vytvor nový riadok
        var row = table_body.insertRow();

        //pre každy stlpec vytvor novú bunku
        for (var key in element){
            var cell = row.insertCell();
            cell.innerHTML = element[key];
        }
        //vytvor tlacidlo na výber skupiny
        var cell = row.insertCell();
        var button = document.createElement("button")
        button.type = "button";
        button.setAttribute("onclick","selectGroup("+element["id_group"]+",'"+element["group_name"]+"');");
        cell.insertAdjacentElement("beforeend",button);
        button.innerText = "Vyber";
    });

    
}