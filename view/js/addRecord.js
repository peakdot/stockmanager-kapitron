function createForm(){
    //Retrieving form data
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var list = JSON.parse(this.responseText);
            buildForm(list);
        }
    };
    xhttp.open("GET", "../model/stockviewmodel.php?t=1&id=1", true);
    xhttp.send();
}

function buildForm(list){
    var section = document.createElement("section");
    for (i = 0; i < list.length; i++) {
        var article = document.createElement("article");
        var p = document.createElement("p");
        var textNode = document.createTextNode(list[i][0]);
        p.appendChild(textNode);
        var input = document.createElement("input");
        switch (list[i][2]) {
            case ("0"):
            input.type = "number";
            break;
            case ("1"):
            input.type = "text";
            break;
            case ("2"):
            input.type = "text";
            break;
            case ("3"):
            input.type = "date";
            break;
        }
        input.name = list[i][1];
        article.appendChild(p);
        article.appendChild(input);
        section.appendChild(article);
    }

    var a0 = document.createElement("a");
    a0.href = "";
    var button0 = document.createElement("button");
    var textNode0 = document.createTextNode("Нэмэх");
    button0.appendChild(textNode0);
    a0.appendChild(button0);
    section.appendChild(a0);

    var a1 = document.createElement("a");
    a1.href = "";
    var button1 = document.createElement("button");
    var textNode1 = document.createTextNode("Болих");
    button1.appendChild(textNode1);
    a1.appendChild(button1);
    section.appendChild(a1);

    document.getElementsByTagName("form")[0].appendChild(section);
}