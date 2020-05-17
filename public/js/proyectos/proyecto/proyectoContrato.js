$(function(){
    $('[name=fecha_contrato]').val(fecha_actual());
    // $('[name=elaborado_por]').val(JSON.parse(sessionStorage.getItem('userSession')).id_usuario);
    $('[name=id_tp_contrato]').val(1).trigger('change.select2');
    $('[name=moneda_con]').val(1).trigger('change.select2');
    $('#listaContratos tbody').html('');
    
    $("#form-contrato").on("submit", function(e){
        e.preventDefault();
        guardar_contrato();
    });

});
function limpiarCampos(){
    $('[name=id_tp_contrato]').val('');
    $('[name=nro_contrato]').val('');
    $('[name=descripcion]').val('');
    $('[name=fecha_contrato]').val(fecha_actual());
    $('[name=importe_contrato]').val('');
    $('[name=moneda_con]').val(1).trigger('change.select2');
    $('[name=adjunto]').val('');
}
function open_proyecto_contrato(data){
    $('#modal-proyecto_contratoc').modal({
        show: true
    });
    console.log(data);
    $('[name=id_proyecto]').val(data.id_proyecto);
    $('#cod_proyecto').text(data.codigo);
    $('#des_proyecto').text(data.descripcion);
    limpiarCampos();
    listar_contratos_proy(data.id_proyecto);
}
function listar_contratos_proy(id_proyecto){
    console.log(id_proyecto);
    $.ajax({
        type: 'GET',
        url: 'listar_contratos_proy/'+id_proyecto,
        dataType: 'JSON',
        success: function(response){
            $('#listaContratos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_contrato(){
    var id_pro = $('[name=id_proyecto]').val();
    var formData = new FormData($('#form-contrato')[0]);
    $.ajax({
        type: 'POST',
        url: 'guardar_contrato',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Contrato registrado con éxito');
                listar_contratos_proy(id_pro);
                limpiarCampos();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_contrato(id_contrato){
    if (id_contrato !== ''){
        var rspta = confirm("¿Está seguro que desea anular el contrato?")
        if (rspta){
            $.ajax({
                type: 'GET',
                // headers: {'X-CSRF-TOKEN': token},
                url: 'anular_contrato/'+id_contrato,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Contrato anulado con éxito');
                        var id = $('[name=id_proyecto]').val();
                        listar_contratos_proy(id);
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
    
}

