
let documentos = [];
let guias_detalle = [];

function nuevo_prorrateo(){
    $('#listaProrrateos tbody').html('');
    $('#listaGuiaDetalleProrrateo tbody').html('');
    documentos = [];
    guias_detalle = [];

    $('[name=id_prorrateo]').val('');
    $('[name=total_suma]').val('');
    $('[name=total_valor]').val('');
    $('[name=total_items]').val('');
    $('[name=total_adicional]').val('');
    $('[name=total_costo]').val('');
}

function mostrar_prorrateo(id_prorrateo){
    
    $('#listaProrrateos tbody').html('');
    $('#listaGuiaDetalleProrrateo tbody').html('');
    
    documentos = [];
    guias_detalle = [];

    $.ajax({
        type: 'GET',
        url: 'mostrar_prorrateo/'+id_prorrateo,
        dataType: 'JSON',
        success: function(response){
            console.log(response);

            $('[name=codigo]').text(response['prorrateo'].codigo);
            $('#estado_doc').text((response['prorrateo'].estado==1 ? "Activo" : "Inactivo"));

            response['documentos'].forEach(element => {
                
                documentos.push({
                    'id_doc_com'        :element.id_doc_com,
                    'id_tp_prorrateo'   :element.id_tp_prorrateo,
                    'id_tipo_prorrateo' :element.id_tipo_prorrateo,
                    'tipo_prorrateo'    :element.tipo_prorrateo,
                    'tp_prorrateo'      :element.descripcion,
                    'id_proveedor'      :element.id_proveedor,
                    'razon_social'      :element.razon_social,
                    'fecha_emision'     :element.fecha_emision,
                    'id_tp_documento'   :element.id_tp_doc,
                    'serie'             :element.serie,
                    'numero'            :element.numero,
                    'id_moneda'         :element.moneda,
                    'total'             :element.total_a_pagar,
                    'tipo_cambio'       :element.tipo_cambio,
                    // 'importe'           :(element.total_a_pagar * element.tipo_cambio),
                    'importe'           :element.importe_soles,
                    'importe_aplicado'  :element.importe_aplicado,
                });
            });

            mostrar_documentos();

            response['detalles'].forEach(element => {
                var unitario = parseFloat(element.precio_unitario!==null 
                    ? element.precio_unitario 
                    : element.unitario);

                guias_detalle.push({
                    'id_guia_com_det'   :element.id_guia_com_det,
                    'serie'             :element.serie,
                    'numero'            :element.numero,
                    'codigo'            :element.codigo,
                    'part_number'       :element.part_number,
                    'descripcion'       :element.descripcion,
                    'cantidad'          :element.cantidad,
                    'abreviatura'       :element.abreviatura,
                    'fecha_emision'     :element.fecha_emision,
                    'tipo_cambio'       :element.tipo_cambio,
                    'valor_compra'      :(unitario * parseFloat(element.cantidad)),
                    'valor_compra_soles':( element.moneda !== 1 
                                        ? (unitario * parseFloat(element.cantidad) * parseFloat(element.tipo_cambio))
                                        : (unitario * parseFloat(element.cantidad)) ),
                    'adicional_valor'   :element.adicional_valor,
                    'adicional_peso'    :element.adicional_peso,
                    'peso'              :element.peso,
                    'total'             :(parseFloat(element.valor_compra_soles) + parseFloat(element.adicional_valor) + parseFloat(element.adicional_peso)),
                });
            });

            mostrar_guias_detalle();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_prorrateo(data, action){
    console.log(data);
    console.log(action);

    if (action == 'register'){
        baseUrl = 'guardarProrrateo';
    } 
    else if (action == 'edition'){
        baseUrl = 'actualizarProrrateo';
    }
    
    data =  'documentos='+JSON.stringify(documentos)+
            '&guias_detalle='+JSON.stringify(guias_detalle);

    $.ajax({
        type: 'POST',
        url: 'guardarProrrateo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
