function doc_guia(id_prov){
    var id_tp_doc = 2;
    $('[name=id_tp_doc]').val(id_tp_doc).trigger('change.select2');
    $('[name=fecha_emision_doc]').val(fecha_actual());
    $('[name=id_proveedor]').val(id_prov).trigger('change.select2');
    var id_doc = $('[name=id_doc_com]').val();
    console.log('id_doc: '+ id_doc);
    if (id_doc !== ''){
        $('#nombre_boton').text('Actualizar');
    } else {
        $('#nombre_boton').text('Guardar');
    }
}
function guardar_doc_guia(){
    var id_guia = $('[name=id_guia]').val();
    var id_tp_doc = $('[name=id_tp_doc]').val();
    var serie_doc = $('[name=serie_doc]').val();
    var numero_doc = $('[name=numero_doc]').val();
    var id_proveedor = $('[name=id_proveedor]').val();
    var fecha_emision_doc = $('[name=fecha_emision_doc]').val();
    var id_doc = $('[name=id_doc_com]').val();
    // var token = $('#token').val();

    data =  'id_guia='+id_guia+
            '&id_doc_com='+id_doc+
            '&id_tp_doc='+id_tp_doc+
            '&serie='+serie_doc+
            '&numero='+numero_doc+
            '&id_proveedor='+id_proveedor+
            '&fecha_emision='+fecha_emision_doc;
    console.log(data);

    var baseUrl = '';

    if (id_doc !== ''){
        console.log('actualiza');
        baseUrl = 'actualizar_doc_guia';
    } else {
        console.log('guarda');
        baseUrl = 'guardar_doc_guia';
    }

    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['id_doc'] > 0){
                alert('Comprobante registrado con éxito');
                $('[name=id_doc_com]').val(response['id_doc']);
                //Copiar serie numero en label de la guia
                $('#tp_doc').text(response['tp_doc']);
                $('#doc_serie').text(response['doc_serie']);
                $('#doc_numero').text(response['doc_numero']);
                $('#modal-doc_guia').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero_doc(){
    var num = $('[name=numero_doc]').val();
    $('[name=numero_doc]').val(leftZero(6,num));
}