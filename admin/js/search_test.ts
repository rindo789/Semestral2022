function searchTest(){
    var search_prompt = document.getElementById("search_field")!.value;
    var table = <HTMLTableElement>document.getElementById("search_table");
    
    //vymaz na zaciatku vyhladavanie predoslí výsledok
    for (let i = 1; i < table.children.length; i++) {
        table.children[i].remove();
      }

    var responseJSON;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {    
        //console.log(this.responseText);
            
        if (this.responseText == "Nenašla sa žiadna skupina" || this.responseText == "Chyba pri hladaní"){
            return;
        }
        responseJSON = JSON.parse(this.responseText);
    };
    xmlhttp.open("GET", "../include/search_tests.ajax.php?search_prompt="+search_prompt, false);
    xmlhttp.send();

    if (responseJSON == undefined) return;
    
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
        link_to.href = "../include/manage_tests_check.php?test_id="+element["id_test"]+"&state=delete";
        link_to.innerHTML = "Delete";
        var cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);

        //vytvor tlacidlo na prechod do editácie užívatela
        link_to = document.createElement("a");
        link_to.href = "../index/manage_testsEdit.php?test_id="+element["id_test"];
        link_to.innerHTML = "Upraviť";
        cell = row.insertCell();
        cell.insertAdjacentElement("beforeend",link_to);
    });
}