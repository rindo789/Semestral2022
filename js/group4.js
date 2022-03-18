var x = document.getElementsByTagName("fieldset");
let y = x.length;

function AddStudent(){

    var field = document.createElement("fieldset");
    var student_id = document.createElement("input");
    var medzera = document.createElement("br");
    var delete_button = document.createElement("button");
    field.id = 'field'+studentIncrement();

    student_id.type = "text";
    student_id.name = 'student[]';
    student_id.placeholder = "Vložte id študenta";
    student_id.setAttribute("onkeyup", "showName(this.value)");

    delete_button.setAttribute("onclick", "DeleteStudent(this.value)");
    delete_button.value = y;
    delete_button.type = "button";
    delete_button.innerText = "x";

    
    document.getElementById("group_form").insertAdjacentElement("afterbegin", field);
    field.insertAdjacentElement("beforeend",student_id);
    field.insertAdjacentElement("beforeend",delete_button);
}

function DeleteStudent(value){
    document.getElementById("field"+value).remove();
}
function studentIncrement(){
    y++;
    return y;
}
function studentDecrese(){
    y--;
}