/*function listarItems(id){
    console.log('listarItems');
    var vardataTables = funcDatatables();
    var tabla = $('#listaTransferenciaDetalle').DataTable({
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_transferencia_detalle/'+id,
        'columns': [
            {'data': 'id_guia_ven_det'},
            {'defaultContent':'<input type="checkbox" checked/>'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            // {'data': 'cod_posicion'},
            {'data': 'cantidad'},
            {'render':
                function (data, type, row){
                    return ('<input type="number" class="input-data right" name="cantidad_recibida" value="'+row['cantidad']+'"/>');
                }
            },
            {'data': 'abreviatura'},
            {'defaultContent': 
                '<button type="button" class="ver btn btn-warning boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Agregar Observaciones" >'+
                    '<i class="fas fa-clipboard-list"></i></button>'
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    ver("#listaTransferenciaDetalle tbody", tabla);
}
function ver(tbody, tabla){
    console.log("obs");
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
    });
}*/
function open_transferencia_detalle(data){
    console.log(data);
    if (data !== null){
        $('#modal-transferencia_detalle').modal({
            show: true
        });
        $('#cod_trans').text(data.codigo);
        $('#guia').text(data.guia_ven);
        $('[name=id_transferencia]').val(data.id_transferencia);
        $('[name=id_guia_ven]').val(data.id_guia_ven);
        console.log(data.id_almacen_destino);
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
        var id_guia_ven_det = [];
        var cantidad_recibida = [];
        var ubicaciones = [];
        var observacion = [];
        var falta_ubi = false;
        var r = 0;
            
        $("input[type=checkbox]:checked").each(function(){
            id_guia_ven_det[r] = $(this).parent().parent()[0].id;
            cantidad_recibida[r] = $(this).closest('td').siblings().find("input[name=cantidad_recibida]").val();
            observacion[r] = $(this).closest('td').siblings().find("input[name=observacion]").val();
            ubicaciones[r] = $(this).closest('td').siblings().find("select[name=id_posicion]").val();
            var cant = $(this).closest('tr').find('td:eq(3)').text();
            console.log(cant+' - '+cantidad_recibida[r]);
            if (ubicaciones[r] == 0){
                console.log(ubicaciones[r]);
                falta_ubi = true;
            }
            r++;
        });
    
        if (!falta_ubi){
            var data = 'id_transferencia='+id_transferencia+
                    '&id_guia_ven='+id_guia_ven+
                    '&fecha_almacen='+fecha_almacen+
                    '&responsable_destino='+responsable_destino+
                    '&id_almacen_destino='+id_almacen_destino+
                    '&id_guia_ven_det='+id_guia_ven_det+
                    '&cantidad_recibida='+cantidad_recibida+
                    '&ubicaciones='+ubicaciones+
                    '&observacion='+observacion;
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
                        $('#modal-transferencia_detalle').modal('hide');
                        listarTransferenciasPendientes();
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        } else {
            alert('Es necesario que le asigne ubicaciones!');
        }
    } else {
        $('#modal-transferencia_detalle').modal('hide');
    }
}