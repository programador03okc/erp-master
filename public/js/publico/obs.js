function open_obs(titulo, obligatorio, mensaje){
    $('#modal-obs').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $('#titulo').text(titulo);
    $('[name=mensaje]').val(mensaje);
    $('[name=obligatorio]').val(obligatorio);
}
function enviarObs(){
    var obli = $('[name=obligatorio]').val();
    var msj = $('[name=mensaje]').val();
    var obs = $('[name=observacion]').val();
    var guardar = false;

    if (obli == 'true'){
        if (obs !== ''){
            guardar = true;
        } else {
            alert(msj);
        }
    } else {
        guardar = true;
    }
    
    if (guardar){
        var codigo = $('[name=codigo]').val();
        var id_solicitud = $('[name=id_solicitud]').val();
        guardar_sustento(codigo,id_solicitud,obs);
        $('#modal-obs').modal('hide');
    }
}
