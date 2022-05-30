function searchResult() {
    //najdi vyhladavac v mnazerovy hodnotení a vyber z neho hodnotz
    var search_prompt = document.getElementById("search_field").value;
    //najdi tabulku do ktorej sa budu davat vysledky hladania
    var table = document.getElementById("search_table");
    //vymaz na zaciatku vyhladavania predoslí výsledok
    for (var i = 1; i < table.children.length; i++) {
        table.children[i].remove();
    }
    var responseJSON;
    //AJAX funkcia
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        //console.log(this.responseText);
        if (this.responseText == "Nič nenašlo" || this.responseText == "Chyba pri hladaní") {
            return;
        }
        responseJSON = JSON.parse(this.responseText);
    };
    xmlhttp.open("GET", "../include/search_result.ajax.php?search_prompt=" + search_prompt, false);
    xmlhttp.send();
    //ak je JSON prázdy ukonči predčasne funkciu
    if (responseJSON == undefined)
        return;
    //vytvor telo tabulky kde pojde výsledok
    var table_body = table.createTBody();
    //chod do arraya najdených odpovedí
    responseJSON.forEach(function (element) {
        //vytvor nový riadok
        var row = table_body.insertRow();
        //pre každy stlpec vytvor novú bunku
        for (var key in element) {
            var cell = row.insertCell();
            cell.innerHTML = element[key];
        }
        //vytvor tlacidlo pre vymazanie odpovede a pridaj ju do novej bunky
        var link_to = document.createElement("a");
        link_to.href = "../include/manage_result_check.php?result_id=" + element["id_odp"] + "&state=delete";
        link_to.innerHTML = "Delete";
        var cell = row.insertCell();
        cell.insertAdjacentElement("beforeend", link_to);
        //vytvor tlacidlo na zobrazenie odpovede študenta
        var show_button = document.createElement("button");
        show_button.type = "button";
        show_button.innerText = "Zobraziť";
        show_button.setAttribute("onclick", "showResult(" + element["id_odp"] + ");");
        show_button.id = "button_show_modal";
        cell = row.insertCell();
        cell.insertAdjacentElement("beforeend", show_button);
    });
}
//funkcia na otvorenie modalu pre odpoved študenta
function showResult(id_odpovede) {
    //zobraz modal
    document.getElementById("result_modal").style.display = "flex";
    //najdi tabulku modalu
    var table = document.getElementById("table_results");
    //vymaz predošlé výsledky hladania
    for (var i_1 = 1; i_1 < table.children.length; i_1++) {
        table.children[i_1].remove();
    }
    //vymaz celú hlavicku tabulky
    table.deleteTHead();
    var responseJSON;
    //AJAX funkcia
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        console.table(this.responseText);
        if (this.responseText == "Nič nenašlo" || this.responseText == "Chyba pri hladaní") {
            return;
        }
        responseJSON = JSON.parse(this.responseText);
    };
    xmlhttp.open("GET", "../include/search_result.ajax.php?show_result=" + id_odpovede, false);
    xmlhttp.send();
    if (responseJSON == undefined)
        return;
    //vytvor hlavicku tabulky a riadok
    var table_head = table.createTHead();
    var head_row = table_head.insertRow();
    //Vytvor do hlavicky znenia otázok, cyklovaním cez JSON
    var head_cell;
    var i = 1;
    while (responseJSON["questions"][i]) {
        head_cell = head_row.insertCell();
        head_cell.textContent = responseJSON["questions"][i]["name"];
        i++;
    }
    //bunka pre body
    head_cell = head_row.insertCell();
    head_cell.textContent = "Body";
    //bunka pre známku
    head_cell = head_row.insertCell();
    head_cell.textContent = "Známka";
    //vytvor telo tabulky a nový riadok
    var table_body = table.createTBody();
    var body_row = table_body.insertRow();
    //cykluj cez odpovede a vypíš nazov otázky,odpoved a správnos´t odpovede
    i = 1;
    while (responseJSON["questions"][i]) {
        var body_cell = body_row.insertCell();
        var j = 1;
        while (responseJSON["questions"][i]["answer" + j]) {
            var text_pole = document.createElement("p");
            if (responseJSON["questions"][i]["answer" + j] == "/*empty*/") {
                text_pole.innerHTML = "-";
            }
            else
                text_pole.innerHTML = responseJSON["questions"][i]["answer" + j];
            body_cell.insertAdjacentElement("beforeend", text_pole);
            //body_cell.textContent += responseJSON["questions"][i]["answer"+j];
            j += 1;
        }
        if (responseJSON["questions"][i]["correct"] == 0) {
            body_cell.className = "incorrect";
        }
        else
            body_cell.className = "correct";
        //body_cell.textContent = responseJSON["questions"][i]["name"] + " " + responseJSON["questions"][i]["answer"+i];
        i++;
    }
    //vypis body
    var body_cell = body_row.insertCell();
    body_cell.textContent = responseJSON["score"];
    //vypis známku
    var body_cell = body_row.insertCell();
    body_cell.textContent = responseJSON["mark"];
}
function closeModal() {
    document.getElementById("result_modal").style.display = "none";
}
