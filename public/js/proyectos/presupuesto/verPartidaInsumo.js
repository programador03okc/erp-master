function ver_partida_insumo(id_pres, id_insumo){
    console.log('id_pres: '+id_pres+' ins:'+ id_insumo);
    if (id_pres !== '' && id_insumo !== ''){
        $('#modal-ver_partida_insumo').modal({
            show: true
        });
        $('#VerPartidaInsumo tbody').html('');
        $('#nombre_insumo').text('');

        $.ajax({
            type: 'GET',
            url: 'partida_insumos_precio/'+id_pres+'/'+id_insumo,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response['cd_insumos'].length > 0){
                    var html = '';
                    var total = 0;
                    var i = 1;
    
                    response['cd_insumos'].forEach(ins => {
                        total += parseFloat(ins.importe_parcial);
                        html+='<tr>'+
                        '<td>'+i+'</td>'+
                        '<td class="right">'+ins.codigo+'</td>'+
                        '<td>'+ins.descripcion+'</td>'+
                        '<td>'+ins.abreviatura.trim()+'</td>'+
                        '<td class="right">'+ins.cantidad+'</td>'+
                        '<td class="right">'+formatNumber.decimal(ins.precio_unit,'',-6)+'</td>'+
                        '<th class="right blue info">'+formatNumber.decimal(ins.importe_parcial,'',-6)+'</th>'+
                        '</tr>';
                        i++;
                    });
                    html+='<tr class="blue info" style="font-size: 16px;">'+
                    '<th class="right" colSpan="6"></th>'+
                    '<th class="right">'+formatNumber.decimal(total,'',-6);+'</th>'+
                    '</tr>';
                    
                    $('#VerPartidaInsumo tbody').html(html);
                    $('#nombre_insumo').text(response['descripcion_insumo']);
                }
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
