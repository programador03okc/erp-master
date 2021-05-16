function openOrdenDespachoEstado(id,req,cod,est){
    $('#modal-ordenDespachoEstados').modal({
        show: true
    });
    $('[name=id_od]').val(id);
    $('[name=id_requerimiento]').val(req);
    $('[name=codigo_od]').val(cod);
    $('[name=observacion]').val('');
    $('[name=adjunto]').val('');
    $('[name=gasto_extra]').val('');
    $('[name=plazo_excedido]').prop('checked', false);

    var sel = '';
    switch (est) {
        case 25:
            sel = ` <option value="32" default>En Ag. Trans. Provincias</option>
                    <option value="33">Salió hacia Cliente </option>
                    <option value="34">Cliente recoge en Agencia </option>
                    <option value="35">Recibió en custodia </option>
                    <option value="36">Resolver </option>
                    <option value="21">Entregado Conforme </option>`;
            break;
        case 32:
            sel = ` <option value="33">Salió hacia Cliente </option>
                    <option value="34">Cliente recoge en Agencia </option>
                    <option value="35">Recibió en custodia </option>
                    <option value="36">Resolver </option>
                    <option value="21">Entregado Conforme </option>`;
            break;
        case 33: case 34:
            sel = ` <option value="35">Recibió en custodia </option>
                    <option value="36">Resolver </option>
                    <option value="21">Entregado Conforme </option>`;
            break;
        case 35:
            sel = ` <option value="36">Resolver </option>
                    <option value="21">Entregado Conforme </option>`;
            break;
        case 36:
            sel = ` <option value="21">Entregado Conforme </option>`;
            break;
        default:
            break;
    } 
    $('[name=estado]').html(sel);
    $('#submit_ordenDespachoEstados').removeAttr('disabled');

}

$("#form-ordenDespachoEstados").on("submit", function(e){
    e.preventDefault();
    $('#submit_ordenDespachoEstados').attr('disabled','true');
    despacho_estado();
});

function despacho_estado(){
    var formData = new FormData($('#form-ordenDespachoEstados')[0]);
    $.ajax({
        type: 'POST',
        url: 'guardarEstadoTimeLine',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#modal-ordenDespachoEstados').modal('hide');
            $('#pendientesRetornoCargo').DataTable().ajax.reload();
            actualizaCantidadDespachosTabs();
            alert('Se guardó el estado con éxito');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
