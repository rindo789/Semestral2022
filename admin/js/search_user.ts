function searchUser(){
    var user_name = document.getElementById("user_name")!.value;
    var table = <HTMLTableElement>document.getElementById("search_table");

    var responseJSON;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {        
        //console.log(JSON.parse(this.responseText));
        responseJSON = JSON.parse(this.responseText);
        
        //console.log(JSON.parse(this.responseText));
        //document.getElementById("result_text")!.innerHTML = this.responseText;
    };
    xmlhttp.open("GET", "../include/search_user_ajax.php?user_name="+user_name, false);
    xmlhttp.send();

    //chod do arraya najdených osôb
    responseJSON.forEach(element => {
        //vytvor nový riadok
        var row = table.insertRow();

        //pre každy stlpec vytvor novú bunku
        for (var key in element){
            var cell = row.insertCell();
            cell.innerHTML = element[key];
        }        
        //vytvor tlacidlo pre vymazanie pouzivatela a pridaj ho do novej
        var link_to = document.createElement("a");
        link_to.href = "../include/manage_user.inc.php?user_id="+element["id_uzivatel"];
        link_to.innerHTML = "Delete";
        var cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);
    });
}