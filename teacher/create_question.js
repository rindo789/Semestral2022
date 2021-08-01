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

    //vytvorenie fieldsetu
    Container.id = "fieldset"+CisloOtazky;
    document.getElementById("test_form").insertAdjacentElement("beforeend",Container);

    //vytvorenie opisu otázky
    Opis.type = "text";
    Opis.placeholder = "Polož otázku";
    Container.insertAdjacentElement("afterbegin",Opis);

    //vytvorenie Enterovania
    Container.insertAdjacentElement("beforeend",Space);
    
    //vytvor jednu možnosť pre určitý typ otázky
    switch (questionType){
        case "one":
            CreateOption(qType,CisloOtazky);
            MoreOptions.setAttribute("onclick","CreateOption('one',this.value)");
            break;
        case "multi":
            CreateOption(qType,CisloOtazky);
            MoreOptions.setAttribute("onclick","CreateOption('multi',this.value)");
            break;
        case "text":
          CreateOption(qType,CisloOtazky);
          MoreOptions.style.display = "none";
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
  }

  function CreateOption(qType, qNumber){
    //konštruktory    
    const CisloOtazky = qNumber;
    var SpravnyField = document.getElementById("fieldset"+CisloOtazky);
    var Option = document.createElement("input");
    var OptionName = document.createElement("input");
    var Space = document.createElement("br");

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
          OptionName.style.display = "none";
    }
    
    
    Option.name = /*"question"+CisloOtazky;*/ "test["+CisloOtazky+"][type]";
    Option.value = Option.type;

    OptionName.type = "text";
    OptionName.placeholder = "možnosť";
    OptionName.name = /*"question"+CisloOtazky;*/ "test["+CisloOtazky+"][moznost]";

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