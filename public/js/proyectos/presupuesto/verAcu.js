function ver_acu_detalle(id_cu, cantidad){
    console.log('id_cu: '+id_cu);

    if (id_cu !== ''){
        $('#modal-ver_acu').modal({
            show: true
        });

        $.ajax({
            type: 'GET',
            url: 'mostrar_acu/'+id_cu,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                // if (response['nro_pres'] == 0){
                    $('#cod_acu').text(response['acu'][0].codigo);
                    $('#descripcion').text(response['acu'][0].descripcion);
                    $('#rendimiento').text(response['acu'][0].rendimiento);
                    $('#cant_partida_cd').text(cantidad);
                    $('#unid_medida').text(response['acu'][0].abreviatura);
                    $('#observacion').text(response['acu'][0].observacion);
                    var mnd = $('[name=moneda]').val();
                    var tpc = $('[name=tipo_cambio]').val();
                    var html = '';
                    var total_acu = 0;
                    console.log('tipo_cambio:'+tpc);
    
                    response['detalle'].forEach(ins => {
                        var total = 0;
                        if (tpc !== undefined){
                            if (mnd == 1){
                                total = (ins.precio_total * cantidad);
                            } else {
                                total = (ins.precio_total * cantidad * parseFloat(tpc));
                            }
                        }
                        total_acu += total;
    
                        html+='<tr>'+
                        '<td class="right">'+ins.codigo+'</td>'+
                        '<td>'+ins.descripcion+'</td>'+
                        '<td>'+ins.cod_tp_insumo+'</td>'+
                        '<td>'+ins.abreviatura+'</td>'+
                        '<td class="right">'+ins.cuadrilla+'</td>'+
                        '<td class="right">'+ins.cantidad+'</td>'+
                        '<td class="right">'+ins.precio_unit+'</td>'+
                        '<td class="right">'+formatNumber.decimal(ins.precio_total,'',-6)+'</td>'+
                        '<th class="right blue info">'+formatNumber.decimal(total,'',-4)+'</th>'+
                        '</tr>';
                    });
                    var mnd = (tpc !== undefined ? (mnd == 1 ? 'S/. ': '$ ' ) : 'S/.');
                    html+='<tr class="blue info" style="font-size: 16px;">'+
                    '<th class="right" colSpan="7">'+mnd+'</th>'+
                    '<th class="right">'+formatNumber.decimal(response['acu'][0].total,'',-4)+'</th>'+
                    '<th class="right">'+formatNumber.decimal(total_acu, mnd, -4)+'</th>'+
                    '</tr>';
                    $('#VerAcuInsumos tbody').html(html);
                // }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('No existe el Acu seleccionado!');
    }

}