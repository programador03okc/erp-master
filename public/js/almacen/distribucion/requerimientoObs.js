let origen = null;

function openRequerimientoObs(id, cod, ori){
    $('#modal-requerimiento_obs').modal({
        show: true
    });
    origen = ori;
    $('[name=obs_id_requerimiento]').val(id);
    $('[name=obs_motivo]').val('');
    $('#cabecera_req').text(cod+' - Anular Requerimiento');
}

$("#form-requerimiento_obs").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_requerimiento(data);
});

function anular_requerimiento(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_anular_requerimiento',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response){
                $('#modal-requerimiento_obs').modal('hide');
                if (origen !== null && origen == 'despacho'){
                    $('#requerimientosPendientes').DataTable().ajax.reload();
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
