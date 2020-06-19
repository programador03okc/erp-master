
$(function(){
    /* Seleccionar valor del DataTable */
    $('#ListaRequerimientos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#ListaRequerimientos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // var codi = $(this)[0].childNodes[1].innerHTML;
        // $('[name=id_requerimiento]').val(myId);
        // console.log(myId);
        obtenerRequerimiento(myId);

        $('#modal-obtener-requerimiento').modal('hide');
    });
});


function obtenerRequerimientoModal(){
    $('#modal-obtener-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });

    listar_requerimientos_elaborados();
    document.getElementById('btnNuevo').setAttribute("disabled","true");
}

function listar_requerimientos_elaborados(){
    var vardataTables = funcDatatables();
    $('#ListaRequerimientos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[0, 'desc']],
        'destroy' : true,
        'ajax': '/listar_requerimientos_elaborados',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'}
 
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function obtenerRequerimiento(id){
    $.ajax({
        type: 'GET',
        url: '/get_requerimiento_orden/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            console.log(response.requerimiento.codigo_sede_empresa);
            
            document.querySelector("div[id='group-requerimiento_seleccionado']").removeAttribute('hidden');
            document.querySelector("div[id='input-group-sede']").removeAttribute('hidden');
            document.querySelector("form[id='form-orden'] input[name='codigo_requerimiento']").value = response.requerimiento.codigo;
            document.querySelector("form[id='form-orden'] input[name='concepto_requerimiento']").value = response.requerimiento.concepto;
            // document.querySelector("form[id='form-orden'] input[name='sede_requerimiento']").value = response.requerimiento.codigo_sede_empresa;
            document.querySelector("form[id='form-orden'] input[name='fecha_requerimiento']").value = response.requerimiento.fecha_requerimiento;
            document.querySelector("form[id='form-orden'] input[name='id_requerimiento']").value = response.requerimiento.id_requerimiento;
            document.querySelector("form[id='form-orden'] select[name='sede']").value = response.requerimiento.id_sede;
            document.querySelector("form[id='form-orden'] input[name='igv_porcentaje']").value = 18;
            $('[name=id_tipo_doc]').val(2).trigger('change.select2');

            // llenar_tabla_requerimiento_seleccionado(payload);
            detalleRequerimientoSelected=response.det_req;
            // console.log(response);
            let id_requerimiento = response.requerimiento.id_requerimiento;
             // let tipo_cliente = response.requerimiento.tipo_cliente;
            // let id_persona =  response.requerimiento.id_persona;
            // let dni_persona = response.requerimiento.dni_persona;
            // let nombre_persona = response.requerimiento.nombre_persona;
            // let id_cliente = response.requerimiento.id_cliente;
            // let cliente_razon_social = response.requerimiento.cliente_razon_social;
            // let cliente_ruc =response.requerimiento.cliente_ruc;
            // let total =response.requerimiento.total;

            // document.querySelector("div[id='input-group-proveedor'] h5").textContent = 'Cliente';
             // $('[name=monto_subtotal]').val(total);


            listar_detalle_orden_requerimiento(response.det_req);
            calcularTotales();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

 


function actualiza_totales_by_subtotal(){
    var sub_total = parseFloat($('[name=monto_subtotal]').val());
    var pigv = parseFloat($('[name=igv_porcentaje]').val());
    var igv = sub_total * parseFloat(pigv) / 100;
    $('[name=monto_igv]').val(formatDecimal(igv));
    var total_a_pagar = sub_total + igv;
    $('[name=monto_total]').val(formatDecimal(total_a_pagar));

    //     var pigv = parseFloat($('[name=igv_porcentaje]').val());
    // console.log(pigv);
    
    //     var monto_total = parseFloat($('[name=monto_total]').val());
    //     var monto_subtotal = monto_total/(1+(pigv/100));
    //     $('[name=monto_subtotal]').val(formatDecimal(monto_subtotal));
    //     var monto_igv = monto_subtotal*(pigv/100);
    //     $('[name=monto_igv]').val(formatDecimal(monto_igv));
}

function listar_detalle_orden_requerimiento(data){
    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').dataTable({
        bDestroy: true,
        order: [[1, 'desc']],
        info:     false,
        paging:   false,
        searching: false,
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
            {'render':
                function (data, type, row, meta){
                    return '<input type="text" name="cantidad_item" index="'+meta.row+'" style="width: 60px;" data-id-detalle-requerimiento="'+row.id_detalle_requerimiento+'" value="'+row.cantidad+'" oninput="handleKeyPrecio(event,this);" disabled>';
                }
            },
            {'render':
                function (data, type, row, meta){
                    return '<input type="text" name="precio_item" index="'+meta.row+'" style="width: 60px;" data-id-detalle-requerimiento="'+row.id_detalle_requerimiento+'" value="'+row.precio_referencial+'" oninput="handleKeyPrecio(event,this);">';
                }
            },
            {'render':
                function (data, type, row, meta){
                    return '0';
                }
            },
            {'render':
                function (data, type, row, meta){
                    let montoTotalItem = (row.cantidad*row.precio_referencial).toFixed(2);
                    return '<input type="text" name="monto_total_item" index="'+meta.row+'" style="width: 60px;" data-id-detalle-requerimiento="'+row.id_detalle_requerimiento+'" value="'+montoTotalItem+'" disabled>';

                    
                }
            },
            {'render':
                function (data, type, row, meta){
                    return '';
                }
            },
            {'render':
                function (data, type, row, meta){
                    return '';
                }
            },
            {'render': 
                function (data, type, row) {
                    // let btn =
                    // '<div class="btn-group btn-group-sm" role="group">'+
                    //     '<button class="btn btn-primary btn-sm" name="btnActualizarItem" title="Actualizar" onclick="actualizarItemV(event,'+row.id_detalle_requerimiento+');">'+
                    //         '<i class="far fa-edit"></i>'+
                    //     '</button>'+
                    // '</div>';
                    // return btn;
                    return '';
                },
            }
        ]
    })

    let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;


}