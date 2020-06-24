function open_transferencia_detalle(data){
    if (data !== null){
        $('#modal-transferencia_detalle').modal({
            show: true
        });
        $('#cod_trans').text(data.codigo);
        $('#guia').text(data.guia_ven);
        $('[name=id_transferencia]').val(data.id_transferencia);
        $('[name=id_guia_ven]').val(data.id_guia_ven);
        $('[name=guia_ingreso_compra]').val(data.guia_ingreso_compra);
        $('[name=id_almacen_destino]').val(data.id_almacen_destino);
        $('[name=almacen_destino]').val(data.alm_destino_descripcion);
        $('[name=responsable_destino]').val(data.responsable_destino);
        $('[name=estado]').val(data.estado);
        if (data.estado == 14 || data.estado == 7){
            $('#nombre_boton').text('Cerrar');
        } else {
            $('#nombre_boton').text('Recibir');
        }
        listarItems(data.id_transferencia);
    }
}

function listarItems(id_transferencia){
    console.log(id_transferencia);
    $('#listaTransferenciaDetalle tbody').html('');
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_transferencia_detalle/'+id_transferencia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaTransferenciaDetalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function recibir(){
    var estado = $('[name=estado]').val();
    console.log('estado: '+estado);
    //Si es diferente de 14-Recibido
    if (estado !== '14' && estado !== '7'){
        var id_transferencia = $('[name=id_transferencia]').val();
        var id_guia_ven = $('[name=id_guia_ven]').val();
        var fecha_almacen = $('[name=fecha_almacen]').val();
        var id_almacen_destino = $('[name=id_almacen_destino]').val();
        var responsable_destino = $('[name=responsable_destino]').val();
        var guia_ingreso_compra = $('[name=guia_ingreso_compra]').val();
        // var id_guia_ven_det = [];
        // var cantidad_recibida = [];
        // var ubicaciones = [];
        // var observacion = [];
        // var falta_ubi = false;
        // var r = 0;
        var detalle = [];
            
        $("input[type=checkbox]:checked").each(function(){
            var nuevo = {
                id_guia_ven_det: $(this).parent().parent()[0].id,
                cantidad_recibida: $(this).closest('td').siblings().find("input[name=cantidad_recibida]").val(),
                observacion: $(this).closest('td').siblings().find("input[name=observacion]").val(),
                ubicacion: $(this).closest('td').siblings().find("select[name=id_posicion]").val()
            }
            detalle.push(nuevo);
            // var cant = $(this).closest('tr').find('td:eq(3)').text();
            // console.log(cant+' - '+cantidad_recibida[r]);
            // if (ubicaciones[r] == 0){
            //     console.log(ubicaciones[r]);
            //     falta_ubi = true;
            // }
            // r++;
        });
    
        // if (!falta_ubi){
            var data = 'id_transferencia='+id_transferencia+
                    '&id_guia_ven='+id_guia_ven+
                    '&fecha_almacen='+fecha_almacen+
                    '&responsable_destino='+responsable_destino+
                    '&id_almacen_destino='+id_almacen_destino+
                    '&guia_ingreso_compra='+guia_ingreso_compra+
                    '&detalle='+JSON.stringify(detalle);
                    // '&id_guia_ven_det='+id_guia_ven_det+
                    // '&cantidad_recibida='+cantidad_recibida+
                    // '&ubicaciones='+ubicaciones+
                    // '&observacion='+observacion;
            console.log(data);
            $.ajax({
                type: 'POST',
                url: 'guardar_ingreso_transferencia',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Ingreso generado con éxito');
                        // var id = encode5t(response);
                        // window.open('imprimir_ingreso/'+id);
                        $('#modal-transferencia_detalle').modal('hide');
                        listarTransferenciasPendientes();
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        // } else {
        //     alert('Es necesario que le asigne ubicaciones!');
        // }
    } else {
        $('#modal-transferencia_detalle').modal('hide');
    }
}