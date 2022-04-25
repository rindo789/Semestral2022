function searchUser(){
    var user_name = document.getElementById("user_name")!.value;
    var table = <HTMLTableElement>document.getElementById("search_table");
    
    //vymaz na zaciatku vyhladavanie predoslí výsledok
    for (let i = 1; i < table.children.length; i++) {
        table.children[i].remove();
      }

    var responseJSON;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {        
        responseJSON = JSON.parse(this.responseText);
    };
    xmlhttp.open("GET", "../include/search_user_ajax.php?user_name="+user_name, false);
    xmlhttp.send();

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
        link_to.href = "../include/manage_user_check.php?user_id="+element["id_uzivatel"]+"&state=delete";
        link_to.innerHTML = "Delete";
        var cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);

        //vytvor tlacidlo na prechod do editácie užívatela
        link_to = document.createElement("a");
        link_to.href = "../include/manage_user_check.php?user_id="+element["id_uzivatel"]+"&state=show";
        link_to.innerHTML = "Upraviť";
        cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);
    });
}