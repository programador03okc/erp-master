function open_transferencia_detalle(data){
    if (data !== null){
        $('#modal-transferencia_detalle').modal({
            show: true
        });
        // $('#cod_trans').text(data.codigo);
        $('#guia').text(data.guia_ven);
        // $('[name=id_transferencia]').val(data.id_transferencia);
        $('[name=id_guia_ven]').val(data.id_guia_ven);
        // $('[name=id_requerimiento]').val((data.id_requerimiento_directo !== null) ? data.id_requerimiento_directo : '');
        // $('[name=guia_ingreso_compra]').val(data.guia_ingreso_compra);
        $('[name=id_almacen_destino]').val(data.id_almacen_destino);
        $('[name=almacen_destino]').val(data.alm_destino_descripcion);
        $('[name=responsable_destino]').val(usuario_session);
        $('[name=estado]').val(data.estado);
        $("#submit_transferencia").removeAttr("disabled");

        if (data.estado == 14 || data.estado == 7){
            $('#submit_transferencia').text('Cerrar');
        } else {
            $('#submit_transferencia').text('Recibir');
        }
        listar_guia_transferencia_detalle(data.id_guia_ven);
    }
}

function listar_guia_transferencia_detalle(id_guia_ven){
    console.log(id_guia_ven);
    $('#listaTransferenciaDetalleRecibir tbody').html('');
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guia_transferencia_detalle/'+id_guia_ven,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html='';
            var i = 1;
            response.forEach(element => {
                html+=`<tr id="${element.id_guia_ven_det}">
                <td><input type="checkbox" checked/></td>
                <td style="background-color: LightCyan;">${element.codigo_trans}</td>
                <td style="background-color: LightCyan;">${element.codigo_req}</td>
                <td style="background-color: LightCyan;">${element.concepto}</td>
                <td>${element.codigo}</td>
                <td style="background-color: navajowhite;">${element.part_number}</td>
                <td style="background-color: navajowhite;">${element.descripcion}</td>
                <td><input type="number" class="input-data right" style="width:80px;" name="cantidad_recibida" value="${element.cantidad}" max="${element.cantidad}"/></td>
                <td>${element.abreviatura}</td>
                <td><input type="text" class="input-data" name="observacion"/></td>
                <td>${(element.series ? `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Ver Series" onClick="listarSeriesVen(${element.id_guia_ven_det});"></i>` : '')}</td>
                </tr>`;
                // onClick="agrega_series('.$det->id_detalle_orden.');"
                i++;
            });
            $('#listaTransferenciaDetalleRecibir tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarSeriesVen(id_guia_ven_det){
    $('#modal-ver_series').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: 'listarSeriesVen/'+id_guia_ven_det,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var tr = '';
            var i = 1;
            response.forEach(element => {
                tr+=`<tr id="${element.id_prod_serie}">
                        <td class="numero">${i}</td>
                        <td class="serie">${element.serie}</td>
                        <td>${element.guia_ven}</td>
                        </tr>`;
                    });
                    // <td><i class="btn btn-danger fas fa-trash fa-lg" ></i>
            // onClick="eliminar_serie('+"'"+response[i].id_prod_serie+"'"+');"
            $('#listaSeries tbody').html(tr);
            $('[name=serie_prod]').focus();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function listarItems(id_transferencia){
//     console.log(id_transferencia);
//     $('#listaTransferenciaDetalle tbody').html('');
//     $.ajax({
//         type: 'GET',
//         // headers: {'X-CSRF-TOKEN': token},
//         url: 'listar_transferencia_detalle/'+id_transferencia,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             $('#listaTransferenciaDetalle tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function recibir(){
    var estado = $('[name=estado]').val();
    console.log('estado: '+estado);
    //Si es diferente de 14-Recibido
    if (estado !== '14' && estado !== '7'){
        var id_transferencia = $('[name=id_transferencia]').val();
        var id_guia_ven = $('[name=id_guia_ven]').val();
        var id_req = $('[name=id_requerimiento]').val();
        var fecha_almacen = $('[name=fecha_almacen]').val();
        var id_almacen_destino = $('[name=id_almacen_destino]').val();
        var responsable_destino = $('[name=responsable_destino]').val();
        var guia_ingreso_compra = $('[name=guia_ingreso_compra]').val();
        var detalle = [];
            
        $("input[type=checkbox]:checked").each(function(){
            console.log($(this).parent().parent()[0]);
            var nuevo = {
                id_guia_ven_det: $(this).parent().parent()[0].id,
                cantidad_recibida: $(this).closest('td').siblings().find("input[name=cantidad_recibida]").val(),
                observacion: $(this).closest('td').siblings().find("input[name=observacion]").val(),
                // ubicacion: $(this).closest('td').siblings().find("select[name=id_posicion]").val()
            }
            detalle.push(nuevo);
        });
    
        var data = 'id_transferencia='+id_transferencia+
                '&id_guia_ven='+id_guia_ven+
                '&id_requerimiento='+id_req+
                '&fecha_almacen='+fecha_almacen+
                '&responsable_destino='+responsable_destino+
                '&id_almacen_destino='+id_almacen_destino+
                '&guia_ingreso_compra='+guia_ingreso_compra+
                '&detalle='+JSON.stringify(detalle);
        console.log(data);
        $("#submit_transferencia").attr('disabled','true');
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
    } else {
        $('#modal-transferencia_detalle').modal('hide');
    }
}