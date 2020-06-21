function hiddeElement(option,formId,divIdList){
    switch (option) {
        case 'mostrar':
            divIdList.forEach(element => {
                document.querySelector("form[id='"+formId+"'] div[id='"+element+"']").removeAttribute('hidden');
            });
            break;
    
        case 'ocultar':
            divIdList.forEach(element => {
                document.querySelector("form[id='"+formId+"'] div[id='"+element+"']").setAttribute('hidden',true);
            });
            break;
    
        default:
            break;
    }
}