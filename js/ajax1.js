function showName(str){
    if (str.length == 0) {
        document.getElementById("txtHint").innerHTML = "";
        return;
      } else {
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
          document.getElementById("txtHint").innerHTML = this.responseText;
        }
      xmlhttp.open("GET", "../../teacher/include/ajax.php?q=" + str);
      xmlhttp.send();
      }
}