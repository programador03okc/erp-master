
$("#form-orden_despacho_transportista").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    $('#submit_od_transportista').attr('disabled','true');
    despacho_transportista(data);
    $('#modal-orden_despacho_transportista').modal('hide');
});

function despacho_transportista(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_transportista',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#gruposDespachados').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$('[name=transporte_propio]').on('change', function(){
    console.log($(this).is(':checked'));
    if( $(this).is(':checked') ) {
        $('#agencia').hide();
    } else {
        $('#agencia').show();
    }
});

function despacho_no_conforme(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_no_conforme',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#pendientesRetornoCargo').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
