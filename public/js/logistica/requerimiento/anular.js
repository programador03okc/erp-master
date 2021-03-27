function nuevo_req(){
    data_item=[];
    data=[];
    adjuntos=[];
    adjuntosRequerimiento=[];
    onlyAdjuntosRequerimiento=[];
    $('#form-requerimiento')[0].reset();
    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="12"> No hay datos registrados</td></tr>');
    $('#body_adjuntos_requerimiento').html('<tr id="default_tr"><td></td><td colspan="3"> No hay datos registrados</td></tr>');
    $('#body_lista_trazabilidad_requerimiento').html('<tr id="default_tr"><td></td><td colspan="5"> No hay datos registrados</td></tr>');
    $('#estado_doc').text('');
    $('[name=id_usuario_req]').val('');
    $('[name=id_estado_doc]').val('');
    $('[name=id_requerimiento]').val('');
 


}


function anular_requerimiento(id_req){
    if(id_req > 0){
        baseUrl = rutaAnularRequerimiento+'/'+id_req;
        $.ajax({
            type: 'PUT',
            url: baseUrl,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if(response.status_requerimiento ==200 && response.status_transferencia ==200){
                    alert("Requerimiento Anulado y se revertió la transferencia.");
                    nuevo_req();
                }else if((response.status_requerimiento ==200 && response.status_transferencia == 0) || (response.status_requerimiento ==200 && response.status_transferencia == 400)){
                    alert("Requerimiento Anulado.");
                    mostrar_requerimiento(id_req);
                }else if(response.status_requerimiento ==400){
                    alert("Hubo un problema, No se puede Anular el Requerimiento");
                }else{
                    alert("No se pudo Anular el Requerimiento.");
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
   
}