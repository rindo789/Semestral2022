var counter = 0;
var real_counter = 0;

function CreateQuestion(qType) {
    //zvacsi cislo otakzky
    questionIncrement();

    //konštrutori
    const CisloOtazky = questionReturnNum();
    var questionType = qType;
    var Container = document.createElement("fieldset");
    var Opis = document.createElement("input");
    var Space = document.createElement("br");
    var MoreOptions = document.createElement("button");
    var DeleteQuestion = document.createElement("button");
    var Hidden = document.createElement("input");
    var optionCounter = document.createElement("input");

    
    //vytvorenie fieldsetu
    Container.id = "fieldset"+CisloOtazky;
    document.getElementById("test_form").insertAdjacentElement("beforeend",Container);

    //vytvorenie opisu otázky
    Opis.type = "text";
    Opis.placeholder = "Polož otázku";
    Opis.name = /*"question"+CisloOtazky;*/ "test["+CisloOtazky+"][QuestionText]";
    Container.insertAdjacentElement("afterbegin",Opis);

    //vytvorenie Enterovania
    Container.insertAdjacentElement("beforeend",Space);

    //vytvor tlacidlo na pocitanie moznosti
    optionCounter.type = "hidden";
    optionCounter.value  = "0";
    optionCounter.id = "optionCount"+CisloOtazky;
    Container.insertAdjacentElement("beforeend",optionCounter);
    
    //vytvor jednu možnosť pre určitý typ otázky
    switch (questionType){
        case "one":
            CreateOption(qType,CisloOtazky);
            MoreOptions.setAttribute("onclick","CreateOption('one',this.value)");
            Hidden.value = "radio";
            break;
        case "multi":
            CreateOption(qType,CisloOtazky);
            MoreOptions.setAttribute("onclick","CreateOption('multi',this.value)");
            Hidden.value = "checkbox";
            break;
        case "text":
          CreateOption(qType,CisloOtazky);
          MoreOptions.style.display = "none";
          Hidden.value = "text";
    }
    
    //vytvor tlacitko na vytvorenie dalšej možnosti
    MoreOptions.innerText = "Ďaľšia možnosť";
    MoreOptions.type = "button";
    MoreOptions.value = CisloOtazky;
    MoreOptions.id = "more"+CisloOtazky;
    Container.insertAdjacentElement("beforeend",MoreOptions);
    
    //vytvor tlacidlo na vymazanie otazky
    DeleteQuestion.innerText = "Vymaž otázku";
    DeleteQuestion.type = "button";
    DeleteQuestion.value = CisloOtazky;
    DeleteQuestion.setAttribute("onclick","DeleteQuestion(this.value)");
    Container.insertAdjacentElement("beforeend",DeleteQuestion);

    //vytvor tlacidlo na zistenie typu inputu
    Hidden.type = "hidden";
    Hidden.name = "test["+CisloOtazky+"][type]";
    Container.insertAdjacentElement("beforeend",Hidden);    
  }

  function CreateOption(qType, qNumber){
    //konštruktory    
    const CisloOtazky = qNumber;
    var SpravnyField = document.getElementById("fieldset"+CisloOtazky);
    var Option = document.createElement("input");
    var OptionName = document.createElement("input");
    var Space = document.createElement("br");
    var optionCounter = parseInt(document.getElementById("optionCount"+CisloOtazky).value);
    //pridaj hodnotu poctu moznosti
    optionCounter = optionCounter+1;
    document.getElementById("optionCount"+CisloOtazky).value = optionCounter;

    //vyber typu otázky
    switch (qType){
        case "one":
            Option.type = "radio";
            break;
        case "multi":
            Option.type = "checkbox";
            break;
        case "text":
          Option.type = "text";
          Option.style.display = "none";
          Option.value = "on";
    }
    
    if (qType == "multi")
    {
      Option.name ="test["+CisloOtazky+"][moznost][correct"+optionCounter+"]";
    } else 
    Option.name = /*"question"+CisloOtazky;*/ /*"test["+CisloOtazky+"][correct]"*/ /*"correct"+CisloOtazky*/ "test["+CisloOtazky+"][moznost][correct]";

    OptionName.type = "text";
    OptionName.placeholder = "možnosť";
    OptionName.name = /*"question"+CisloOtazky;*/ "test["+CisloOtazky+"][moznost]["+optionCounter+"]";

    if(document.body.contains(document.getElementById("more"+CisloOtazky))){
        var parentNode = document.getElementById("more"+CisloOtazky).parentNode;
        var childNonde = document.getElementById("more"+CisloOtazky);
        parentNode.insertBefore(Option,childNonde);
        parentNode.insertBefore(OptionName,childNonde);
        parentNode.insertBefore(Space,childNonde);
    } else{
      SpravnyField.insertAdjacentElement("beforeend",Option);
      SpravnyField.insertAdjacentElement("beforeend",OptionName);
      SpravnyField.insertAdjacentElement("beforeend",Space);
    }    
}

function DeleteQuestion(value){
    var Field = document.getElementById("fieldset"+value);
    questionDecrement();
    Field.remove();
  }

  function questionIncrement() {
    counter += 1;
    real_counter +=1;
  }
  
  function questionReturnNum(){
      return counter;
      
  }

  function questionDecrement() {
    real_counter -= 1;
  }

  function showStart() {
var x = document.getElementsByTagName("fieldset");
let y = x.length;
for (let i = 0; i < y; i++) {
  questionIncrement();
}
    //window.alert(counter);
  }