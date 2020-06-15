$(function(){
    /* Seleccionar valor del DataTable */
    $('#ListaRequerimientosVentaDirecta tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#ListaRequerimientosVentaDirecta').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // var codi = $(this)[0].childNodes[1].innerHTML;
        // $('[name=id_requerimiento]').val(myId);
        // console.log(myId);
        obtenerVentaDirecta(myId);

        $('#modal-obtener-requerimiento').modal('hide');
    });
});


function obtenerRequerimientoModal(){
    $('#modal-obtener-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });

    listar_requerimientos_venta_directa();
    document.getElementById('btnNuevo').setAttribute("disabled","true");
}

function listar_requerimientos_venta_directa(){
    var vardataTables = funcDatatables();
    $('#ListaRequerimientosVentaDirecta').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'ajax': '/listar_requerimientos_venta_directa',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'render':
                function ( data, type, row ) {
                    if(row.id_cliente > 0){
                        return ( row.razon_social_empresa);
                    }else if(row.id_persona > 0){
                        return ( row.nombre_persona);
                    }else{
                        return '';
                    }
                }
            },
            {'render':
                function ( data, type, row ) {
                    if(row.id_cliente > 0){
                        return ('RUC: '+row.cliente_ruc);
                    }else if(row.id_persona > 0){
                        return ('DNI: '+row.dni_persona);
                    }else{
                        return '';
                    }
                }
            },
        {'data': 'name_ubigeo'},
        {'data': 'direccion_entrega'},
        {'render':
        function ( data, type, row ) {
            return ( row.razon_social_empresa);
            
        }
        },
        {'data': 'fecha_registro'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function obtenerVentaDirecta(id){
    $.ajax({
        type: 'GET',
        url: '/get_requerimiento_venta_directa/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            let codigo = response.requerimiento[0].codigo;
            let tipo_cliente = response.requerimiento[0].tipo_cliente;
            let id_persona =  response.requerimiento[0].id_persona;
            let dni_persona = response.requerimiento[0].dni_persona;
            let nombre_persona = response.requerimiento[0].nombre_persona;
            let id_cliente = response.requerimiento[0].id_cliente;
            let cliente_razon_social = response.requerimiento[0].cliente_razon_social;
            let cliente_ruc =response.requerimiento[0].cliente_ruc;

            document.querySelector("div[id='input-group-proveedor'] h5").textContent = 'Cliente';
            $('[name=id_tipo_doc]').val(2).trigger('change.select2');
            $('[id=codigo]').val(codigo);
            if(tipo_cliente ==1){
                $('[name=id_proveedor]').val(id_persona);
                // $('[name=id_contrib]').val();
                $('[name=razon_social]').val(nombre_persona+' [ DNI:'+dni_persona+']');
            }else if(tipo_cliente ==2){
                $('[name=id_proveedor]').val(id_cliente);
                // $('[name=id_contrib]').val();
                $('[name=razon_social]').val(cliente_razon_social+' [RUC:'+cliente_ruc+']');
            }

            listar_detalle_orden_venta_directa(response.det_req);
            // actualiza_totales();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_detalle_orden_venta_directa(data){
    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').dataTable({
        bDestroy: true,
        order: [[0, 'asc']],
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        data:data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            { data: 'codigo_item' },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion_adicional;
                }
            },
            { data: 'unidad_medida' },
            { data: 'cantidad' },
            { data: 'precio_referencial' },
            {'render':
                function (data, type, row, meta){
                    return '0';
                }
            },
            {'render':
                function (data, type, row, meta){
                    return (row.cantidad*row.precio_referencial);
                }
            },
            {'render':
                function (data, type, row, meta){
                    return '0';
                }
            },
            {'render':
                function (data, type, row, meta){
                    return '';
                }
            },
            {'render': 
                function (data, type, row) {
                    let btn =
                    '<div class="btn-group btn-group-sm" role="group">'+
                        '<button class="btn btn-primary btn-sm" name="btnActualizarItemVentaDirecta" title="Actualizar" onclick="actualizarItemVentaDirecta(event,'+row.id_detalle_requerimiento+');">'+
                            '<i class="far fa-edit"></i>'+
                        '</button>'+
                    '</div>';
                    return btn;
                },
            }
        ],
        // columnDefs: [{ aTargets: [0], sClass: 'invisible' }]
    })

    let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;


}