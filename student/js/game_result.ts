function gameLeaderboard(){

    var game_result;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {   
        game_result = JSON.parse(this.responseText);
        console.log(game_result);
        
    };
    xmlhttp.open("GET", "../include/leaderboard.ajax.php", false);
    xmlhttp.send();

    var place_number = game_result["scoring"].length
    if (place_number==0) return;

    //pridaj score do správneho policka
    //prvý
    var placement = document.getElementById("first").children;
    if (place_number > 0) 
        placement[1].innerHTML = game_result["scoring"][0]["score"];
    //druhý
    placement = document.getElementById("second").children;
    if (place_number > 1)
        placement[1].innerHTML = game_result["scoring"][1]["score"];
    //tretí
    placement = document.getElementById("third").children;
    if (place_number > 2)
        placement[1].innerHTML = game_result["scoring"][2]["score"];
    //načítaj tabulku ostatných hráčov
    placement = document.getElementById("other_participants").children;
    var table =<HTMLTableElement> placement.item(0);
    var table_body = table.createTBody();
    var new_row = table_body.insertRow()

    //štvrtý
    var new_cell = new_row.insertCell();
        new_cell.innerHTML = "4.";
    if (place_number > 3){
        new_cell = new_row.insertCell();
        new_cell.innerHTML = game_result["scoring"][3]["name"];
        new_cell = new_row.insertCell();
        new_cell.innerHTML = game_result["scoring"][3]["score"];
    } else {
        new_cell = new_row.insertCell();
        new_cell.innerHTML = "-";
        new_cell = new_row.insertCell();
        new_cell.innerHTML = "-";
    }
    //piaty
    new_row = table_body.insertRow()
    new_cell = new_row.insertCell();
    new_cell.innerHTML = "5.";
    if (place_number > 4){
        new_cell = new_row.insertCell();
        new_cell.innerHTML = game_result["scoring"][4]["name"];
        new_cell = new_row.insertCell();
        new_cell.innerHTML = game_result["scoring"][4]["score"];
    } else {
        new_cell = new_row.insertCell();
        new_cell.innerHTML = "-";
        new_cell = new_row.insertCell();
        new_cell.innerHTML = "-";
    }
    
    //nasobitel
    placement = document.getElementById("multiplier").children;
    if (place_number > 0){
        placement[1].innerHTML = game_result["multiplier"][0]["name"];
        placement[2].innerHTML = game_result["multiplier"][0]["score"];

        //odpovede
        placement = document.getElementById("right_asnwer").children;
        placement[1].innerHTML = game_result["answers"][0]["name"];
        placement[2].innerHTML = game_result["answers"][0]["score"];
        //cas testu
        placement = document.getElementById("timer").children;
        placement[1].innerHTML = game_result["full_time"][0]["name"];
        placement[2].innerHTML = game_result["full_time"][0]["score"];
        //cas odpovede
        placement = document.getElementById("timer_short").children;
        placement[1].innerHTML = game_result["short_time"][0]["name"];
        placement[2].innerHTML = game_result["short_time"][0]["score"];
    }
    



    
}