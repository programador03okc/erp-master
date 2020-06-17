function openTransferenciaGuia(data){
    $('#modal-transferenciaGuia').modal({
        show: true
    });
    $('[name=id_almacen_origen]').val(data.id_almacen);
    $('[name=id_guia_com]').val(data.id_guia);
    $('[name=id_sede]').val(data.sede_orden);
    $('[name=id_mov_alm]').val(data.id_mov_alm);
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $("#submit_transferencia").removeAttr("disabled");
    cargar_almacenes(data.sede_requerimiento, 'id_almacen_destino');
    var tp_doc_almacen = 2;//guia venta
    next_serie_numero(data.sede_orden,tp_doc_almacen);
}

function cargar_almacenes(sede, campo){
    if (sede !== ''){
        $.ajax({
            type: 'GET',
            url: 'cargar_almacenes/'+sede,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var option = '';
                for (var i=0; i<response.length; i++){
                    if (response.length == 1){
                        option+='<option value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    } else {
                        option+='<option value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    }
                }
                $('[name='+campo+']').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function next_serie_numero(id_sede,id_tp_doc){
    if (id_sede !== null && id_tp_doc !== null){
        $.ajax({
            type: 'GET',
            url: 'next_serie_numero_guia/'+id_sede+'/'+id_tp_doc,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response !== ''){
                    $('[name=serie]').val(response.serie);
                    $('[name=numero]').val(response.numero);
                    $('[name=id_serie_numero]').val(response.id_serie_numero);
                } else {
                    $('[name=serie]').val('');
                    $('[name=numero]').val('');
                    $('[name=id_serie_numero]').val('');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

$("#form-transferenciaGuia").on("submit", function(e){
    console.log('submit_transferencia');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardar_transferencia(data);
});

function guardar_transferencia(data){
    $("#submit_transferencia").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'guardar_guia_transferencia',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Salida Almacén generada con éxito');
                $('#modal-transferenciaGuia').modal('hide');
                $('#ordenesEntregadas').DataTable().ajax.reload();
                // var id = encode5t(response);
                // window.open('imprimir_salida/'+id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
