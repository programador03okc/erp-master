function setTextInfoAnimation(texto){
    let textStatus=document.getElementsByName('text-status');
    for(var i = 0; i < textStatus.length; i++) {
        let t= i;
        textStatus[i].innerHTML= texto;
        textStatus[i].classList.add('transition');
        var delayInMilliseconds = 1000; //1 second
        setTimeout(function()
        {            
            if(textStatus[t] != undefined){
                for(var j = 0;j < textStatus[t].classList.length;j++){
                    if(textStatus[t].classList[j] =='transition'){
                        textStatus[t].classList.remove(textStatus[t].classList.item(j));
                        textStatus[t].innerHTML='';
                    }
                }
            }
        }
        , delayInMilliseconds);
    }
}
