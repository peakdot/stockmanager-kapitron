var c = 2;

        function myFunction() {
            var node = document.createElement("article");
            var h3 = document.createElement("h3");
            var textnode = document.createTextNode("Багана " + c);

            h3.appendChild(textnode);
            node.appendChild(h3);

            var p = document.createElement("p");
            var textnode1 = document.createTextNode("Баганы нэр ");
            p.appendChild(textnode1);
            node.appendChild(p);

            var input = document.createElement("input");
            input.type = "text";
            input.name = "data_name_" + c;
            node.appendChild(input);

            var p1 = document.createElement("p");
            var textnode2 = document.createTextNode("Баганы төрөл");
            p1.appendChild(textnode2);
            node.appendChild(p1);

            var select = document.createElement("select");
            select.name = "data_type_" + c;
            var option0 = document.createElement("option");
            option0.value = 0;
            var tno0 = document.createTextNode("Тоо(int)");
            option0.appendChild(tno0);
            select.appendChild(option0);

            var option1 = document.createElement("option");
            option1.value = 1;
            var tno1 = document.createTextNode("Тогтмол урттай тэмдэгт (char)");
            option1.appendChild(tno1);
            select.appendChild(option1);

            var option2 = document.createElement("option");
            option2.value = 2;
            var tno2 = document.createTextNode("Хувьсах урттай тэмдэгт (varchar)");
            option2.appendChild(tno2);
            select.appendChild(option2);

            var option3 = document.createElement("option");
            option3.value = 3;
            var tno3 = document.createTextNode("Он сар өдөр(date)");
            option3.appendChild(tno3);
            select.appendChild(option3);


            node.appendChild(select);
            var p2 = document.createElement("p");
            var textnode3 = document.createTextNode("Баганы өгөгдлийн урт ");
            p2.appendChild(textnode3);
            node.appendChild(p2);

            var linebreak = document.createElement("br");

            var input1 = document.createElement("input");
            input1.type = "text";
            input1.name = "data_length_" + c;
            node.appendChild(input1);

            node.appendChild(linebreak);
            node.appendChild(linebreak);
            var input2 = document.createElement("input");
            input2.type = "checkbox";
            input2.name = "data_notnull_" + c;
            node.appendChild(input2);

            var p3 = document.createElement("label");
            var textnode4 = document.createTextNode("Хоосон байж болох эсэх/NOT NULL/ ");
            p3.appendChild(textnode4);
            node.appendChild(p3);


            document.getElementById("mySection").appendChild(node);
            c++;
            document.getElementById("data_number").value = c - 1;
        }