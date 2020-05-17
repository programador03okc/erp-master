function ver_detalle_partida(id_partida, descripcion, importe_partida){
    console.log('id_partida: '+id_partida+' des:'+ descripcion);
    if (id_partida !== '' && descripcion !== ''){
        $('#modal-ver_detalle_partida').modal({
            show: true
        });
        $('#VerPartidaInsumo tbody').html('');
        $('#nombre_partida').text(descripcion);
        clearDataTable();

        var vardataTables = funcDatatables();
        var tabla = $('#VerPartidaInsumo').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'bDestroy': true,
            'retrieve': true,
            'ajax': 'ver_detalle_partida/'+id_partida,
            'columns': [
                {'data': 'id_detalle_requerimiento'},
                {'data': 'cod_req'},
                {'data': 'concepto'},
                {'render': 
                    function (data, type, row){
                        return (formatDate(row['fecha_requerimiento']));
                    }
                },
                {'data': 'descripcion_adicional'},
                {'data': 'cantidad', className: 'text-right'},
                {'data': 'moneda_req'},
                {'render': 
                    function (data, type, row){
                        return (formatNumber.decimal((row['precio_referencial'] * row['cantidad']),'',-2));
                    }, className: 'text-right'
                },
                // {'render': 
                //     function (data, type, row){
                //         return (row['fecha_entrega'] !== null ? formatDate(row['fecha_entrega']) : '');
                //     }
                // },
                {'data': 'cod_orden'},
                {'render': 
                    function (data, type, row){
                        return (row['fecha_orden'] !== null ? formatDate(row['fecha_orden']) : '');
                    }
                },
                {'render': 
                    function (data, type, row){
                        return (row['nro_documento'] !== null ? row['nro_documento'] : '');
                    }
                },
                {'render': 
                    function (data, type, row){
                        return (row['razon_social'] !== null ? row['razon_social'] : '');
                    }
                },
                {'render': 
                    function (data, type, row){
                        return (row['moneda_oc'] !== null ? row['moneda_oc'] : '');
                    }
                },
                {'render': 
                    function (data, type, row){
                        return (row['precio_sin_igv'] !== null ? formatNumber.decimal(row['precio_sin_igv'],'',-4) : '');
                    }
                },
                {'render': 
                    function (data, type, row){
                        return ('<i class="fas fa-file-pdf red visible boton" data-toggle="tooltip" data-placement="bottom" '+
                        'title="Ver Requerimiento" onClick="open_requerimiento('+row['id_requerimiento']+');" ></i>'+
                        '<i class="fas fa-file-pdf purple visible boton" data-toggle="tooltip" data-placement="bottom" '+
                        'title="Ver OC/OS" onClick="open_orden('+row['id_orden_compra']+');" ></i>');
                    }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
        /*
        $.ajax({
            type: 'GET',
            url: 'ver_detalle_partida/'+id_partida,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response.length > 0){
                    var html = '';
                    var total_oc = 0;
                    var i = 1;
                    
                    response.forEach(det => {
                        if (det.precio_sin_igv !== null){
                            total_oc += parseFloat(det.precio_sin_igv);
                        }
                        precio_ref = parseFloat(det.cantidad) * parseFloat(det.precio_referencial);
                        
                        html+='<tr>'+
                        '<td>'+i+'</td>'+
                        '<td>'+det.cod_req+'</td>'+
                        '<td>'+det.concepto+'</td>'+
                        '<td>'+formatDate(det.fecha_requerimiento)+'</td>'+
                        '<td>'+det.descripcion_adicional+'</td>'+
                        '<td class="right">'+det.cantidad+'</td>'+
                        '<td class="right">'+det.moneda_req+'</td>'+
                        '<td class="right">'+formatNumber.decimal(precio_ref,'',-4)+'</td>'+
                        '<td>'+(det.fecha_entrega !== null ? formatDate(det.fecha_entrega) : '')+'</td>'+
                        '<td>'+(det.cod_orden !== null ? det.cod_orden : '')+'</td>'+
                        '<td>'+(det.fecha_orden !==null ? formatDate(det.fecha_orden) : '')+'</td>'+
                        '<td>'+(det.nro_documento !== null ? det.nro_documento : '')+'</td>'+
                        '<td>'+(det.razon_social !== null ? det.razon_social : '')+'</td>'+
                        '<td>'+(det.moneda_oc !== null ? det.moneda_oc : '')+'</td>'+
                        '<td class="right">'+(det.precio_sin_igv !== null ? formatNumber.decimal(det.precio_sin_igv,'',-4) : '')+'</td>'+
                        '<td><i class="fas fa-file-pdf red visible boton" data-toggle="tooltip" data-placement="bottom" '+
                        'title="Ver Requerimiento" onClick="open_requerimiento('+det.id_requerimiento+');" ></i>'+
                        '<i class="fas fa-file-pdf purple visible boton" data-toggle="tooltip" data-placement="bottom" '+
                        'title="Ver OC/OS" onClick="open_orden('+det.id_orden_compra+');" ></i></td>'+
                        '</tr>';
                        i++;
                    });
                    html+='<tr class=" info" style="font-size: 16px;">'+
                    '<th class="right blue" colSpan="5">Total Partida:</th>'+
                    '<th class="right blue" colSpan="3">'+formatNumber.decimal(importe_partida,'',-4)+'</th>'+
                    '<th class="right red" colSpan="2">Total Consumido:</th>'+
                    '<th class="right red" colSpan="2">'+formatNumber.decimal(total_oc,'',-4)+'</th>'+
                    '<th class="right green">Total Saldo:</th>'+
                    '<th class="right green" colSpan="2">'+formatNumber.decimal((importe_partida - total_oc),'',-4)+'</th>'+
                    '<td></td>'+
                    '</tr>';
                    
                    $('#VerPartidaInsumo tbody').html(html);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });*/
    } else {
        alert('No existe el Acu seleccionado!');
    }

}

function open_requerimiento(id){
    console.log('id_requerimiento:'+id);
    if (id !== null && id !== ''){
        window.open('/logistica/imprimir-requerimiento-pdf/'+id+'/0');
    } else {
        alert('No existe un Requerimiento!');
    }
}
function open_orden(id){
    console.log('id_orden:'+id);
    if (id !== null && id !== ''){
        window.open('/generar_orden_pdf/'+id);
    } else {
        alert('No existe una Orden!');
    }
}