function open_orden_detalle(id_oc){
    $('#modal-guia_detalle').modal({
        show: true
    });
    listarDetalleOC(id_oc);
}
function listarDetalleOC(id){
    var alm = $('[name=id_almacen]').val();
    console.log('almacen: '+alm);
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_oc_det/'+id+'/'+alm,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            $('#listaDetalleOC tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guia_ocs(id){
    $('#oc tbody').html('');
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'guia_ocs/'+id,
        dataType: 'JSON',
        success: function(response){
            var td = '';
            for (var i=0;i<response.length;i++){
                td += '<tr id="'+response[i].id_oc+'"><td>'+
                response[i].codigo+'</td><td>'+
                formatDate(response[i].fecha)+'</td><td>'+
                response[i].razon_social+'</td><td>'+
                response[i].nombre_trabajador+'</td><td>'+
                // (response[i].forma_pago_credito!==null ? (response[i].condicion+' '+response[i].forma_pago_credito+' días') : response[i].condicion)+'</td><td>'+
                // response[i].fecha_entrega+'</td><td>'+response[i].lugar_entrega+'</td><td>
                '<i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular OC" onClick="anular_oc('+response[i].id_oc+');"></i></td></tr>';
            }
            $('#oc tbody').append(td);

            var est = $('[name=cod_estado]').val();
            if (est !== '1'){
                $('.boton').addClass('desactiva');
            } else {
                $('.boton').removeClass('desactiva');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function listar_ordenes(id_proveedor){
//     console.log('id_proveedor'+id_proveedor);
//     $.ajax({
//         type: 'GET',
//         url: 'listar_ordenes/'+id_proveedor,
//         dataType: 'JSON',
//         success: function(response){
//             var option = '';
//             for (var i=0;i<response.length;i++){
//                 option+='<option value="'+response[i].id_orden_compra+'">'+response[i].orden+'</option>';
//             }
//             $('[name=id_orden_compra]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// function agrega_oc(){
//     var orden = $('select[name="id_orden_compra"] option:selected').text();
//     var id_oc = $('[name=id_orden_compra]').val();
//     var tipo = $('[name=id_tp_doc_almacen]').val();
//     console.log(orden);
//     console.log(id_oc);

//     if (tipo == 6){//Hoja de importación
//         console.log('tipo:'+tipo);
//         ocModal();
//     } else {
//         if (id_oc !== null){
//             open_orden_detalle(id_oc);
//         } else {
//             alert('Es necesario que seleccione una orden');
//         }
//     }
// }
function anular_oc(id_oc){
    var id_guia = $('[name=id_guia]').val();
    var anula = confirm("¿Esta seguro que desea anular ésta OC?\nSe quitará también la relación de sus Items");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_oc/'+id_oc+'/'+id_guia,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('OC anulado con éxito');
                    $("#"+id_oc).remove();
                    listar_detalle(id_guia);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function guardar_detalle_oc(){
    var id_oc_det = [];
    var id_prod = [];
    var id_posicion = [];
    var cantidad = [];
    var id_unid_med = [];
    var unitario = [];
    var total = [];
    var r = 0;
    var msj = 0;

    $("input[type=checkbox]:checked").each(function(){
        id_oc_det[r] = $(this).closest('td').siblings()[0].firstChild.value;
        id_prod[r] = $(this).closest('td').siblings()[1].firstChild.value;
        id_posicion[r] = $(this).closest('td').siblings()[3].firstChild.nextSibling.value;
        cantidad[r] = $(this).closest('td').siblings()[4].firstChild.value;
        id_unid_med[r] = $(this).closest('td').siblings()[5].firstChild.value;
        unitario[r] = $(this).closest('td').siblings()[6].firstChild.value;
        total[r] = $(this).closest('td').siblings()[7].firstChild.value;
        
        if (cantidad[r] == '' || id_posicion[r] == 0){
            ++msj;
        }
        ++r;
    });
    
    if (r == 0){
        alert('Debe seleccionar por lo menos un item');
    } else {
        if (msj > 0){
            alert('Es necesario que ingrese todos los campos!');
        } 
        else {
            console.log(id_oc_det);
            console.log(id_prod);
            console.log(id_posicion);
            console.log(cantidad);
            console.log(id_unid_med);
            console.log(unitario);
            console.log(total);
            var token = $('#token').val();
            var id_guia = $("[name=id_guia]").val();
            
            var data =  'id_guia_com='+id_guia+
                        '&id_oc_det='+id_oc_det+
                        '&id_producto='+id_prod+
                        '&id_posicion='+id_posicion+
                        '&cantidad='+cantidad+
                        '&id_unid_med='+id_unid_med+
                        '&unitario='+unitario+
                        '&total='+total;
            console.log(data);
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: 'guardar_detalle_oc',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log('response'+response);
                    if (response > 0){
                        alert('detalle registrado con éxito');
                        $('#listaDetalle tbody tr').remove();
                        listar_detalle(id_guia);
                        $('#modal-guia_detalle').modal('hide');
                        guia_ocs(id_guia);
                        $('[name=id_orden_compra]').val('0').trigger('change.select2');
                        changeStateButton('guardar');
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
