function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}


function limpiarTabla(idElement){
    // console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}

function disabledControl(element,value){   
    // console.log(element,value); 
    var i;
    for (i = 0; i < element.length; i++) {
        if(value === false){
            element[i].removeAttribute("disabled");
            element[i].classList.remove("disabled");

        }else{
            element[i].setAttribute("disabled","true");
        }
    }
    return null;
}
