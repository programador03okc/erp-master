function verInstrucciones(id_detalle_requerimiento){
    $('#modal-od_transformacion').modal({
        show: true
    });
    $('[name=id_detalle_requerimiento]').val(id_detalle_requerimiento);
    $("#submit_od_transformacion").removeAttr("disabled");
    verDetalleInstrucciones(id_detalle_requerimiento);
}

function verDetalleInstrucciones(id_detalle_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verDetalleInstrucciones/'+id_detalle_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            console.log(response.part_no);
            // console.log(response[0].part_no);
            // console.log(response['part_no']);
            $('[name=part_no]').text(response['fila'].part_no);
            $('[name=descripcion]').text(response['fila'].descripcion);

            $('[name=part_no_producto_transformado]').text(response['fila'].part_no_producto_transformado);
            $('[name=descripcion_producto_transformado]').text(response['fila'].descripcion_producto_transformado);
            
            var etiqueta = (response['fila'].etiquetado_producto_transformado?' <span class="label label-warning">Etiquetado</span></a> ':'')+
            (response['fila'].bios_producto_transformado?' <span class="label label-primary">BIOS</span></a> ':'')+
            (response['fila'].office_preinstalado_producto_transformado?' <span class="label label-success">Office Preinstalado</span></a> ':'')+
            (response['fila'].office_activado_producto_transformado?' <span class="label label-info">Office Activado</span></a> ':'');
            
            $('[name=adicionales]').html(etiqueta);

            var html = '';
            response['detalle'].forEach(element => {
                html+=`<tr>
                    <td>${element.ingresa!==null ? element.ingresa : '-'}</td>
                    <td>${element.sale!==null ? element.sale : '-'}</td>
                </tr>`;
            });
            $('#detalleTransformacion tbody').html(html);
            // $('#modal-ver_series').modal({
            //     show: true
            // });
            // var tr = '';
            // var i = 1;
            // response.forEach(element => {
            //     tr+=`<tr id="reg-${element.serie}">
            //             <td class="numero">${i}</td>
            //             <td><input type="text" class="oculto" name="series" value="${element.serie}"/>${element.serie}</td>
            //             <td>${element.serie_guia_com}-${element.numero_guia_com}</td>
            //             <td>${element.serie_guia_ven !== null ? (element.serie_guia_ven+'-'+element.numero_guia_ven) : ''}</td>
            //         </tr>`;
            //     i++;
            // });
            // $('#listaSeries tbody').html(tr);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-od_transformacion").on("submit", function(e){
    e.preventDefault();
    var id_detalle_requerimiento = $('[name=id_detalle_requerimiento]').val();
    var ing = detalle_ingresa.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);
    var data = $(this).serializeArray();
    console.log(data);
    // var indexed_array = {};
    $.map(data, function(n, i){
        ing[n['name']] = n['value'];
        // indexed_array[n['name']] = n['value'];
    });
    // ing.transformacion = indexed_array;
    console.log(detalle_ingresa);
    $('#modal-od_transformacion').modal('hide');
});