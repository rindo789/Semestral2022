var test_type: string

function showSchedule (type: string){
    test_type = type;
    
    var schedule_modal = document.getElementById("schedule_window");
    schedule_modal.style.display = "block";
}

function hideSchedule(){
    var schedule_modal = document.getElementById("schedule_window");
    schedule_modal.style.display = "none";
}

function setSchedule(){

    var date_start = document.getElementById("date_on").value;
    var date_end = document.getElementById("date_off").value;

    if (!dateCheck(date_start, date_end)){
        document.getElementById("wrong_dates")!.style.display = "block";
        return;
    }
    

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function() {
        console.log(this.responseText);
    };
    xmlhttp.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){
            console.log("status: "+ this.statusText);

            if (this.responseText == "bad request"){
                statusModal("red");
                nullElements();
                return;
            }
            
            statusModal("green");
        } else if (this.status == 401 || this.status == 404){
            console.log("status: "+ this.statusText);

            statusModal("red");
            return;
        }
    }
    xmlhttp.open("GET", "../../teacher/include/schedule.inc.php?date_start="+date_start+"&date_end="+date_end+"&test_type="+test_type, false);
    xmlhttp.send();

}

function dateCheck(date_start, date_end){

 if (Date.parse(date_end) < Date.parse(date_start)){
     return false;
 }else return true;
}

function statusModal(color: string){
    document.getElementById("schedule_window").style.display = "none";

    var status_window = document.getElementById("schedule_response");
    status_window.style.display = "block";
    status_window.style.backgroundColor = color;

    if(color == "green"){
        document.getElementById("schedule_ok").style.display = "block";
    } else {
        document.getElementById("schedule_error").style.display = "block";
    }

    var intervalID = setTimeout(hideResponse, 5000);
    
    function hideResponse(){
        document.getElementById("schedule_response").style.display = "none";
        clearInterval(intervalID);
    }
}

function nullElements(){
    (<HTMLParagraphElement>document.getElementById("wrong_dates")).style.display = "none";
}