function chooser() 
{
    var e = document.getElementById("userChooser");
    var value = e.options[e.selectedIndex].value;
    switch (value) 
    {
        case ("sAdmin"):
            window.location.href = "view/sAdmin.html";
            break;
        case ("admin"):
            window.location.href = "view/admin.html";
            break;
        case ("basicUser"):
            window.location.href = "view/basicUser.html";
            break;
    }
}
