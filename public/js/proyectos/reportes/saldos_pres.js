function listar_estructura(id_presup, mnd){
    if (id_presup !== ''){
        console.log('id_presup: '+id_presup);
        $.ajax({
            type: 'GET',
            url: 'listar_saldos_presupuesto/'+id_presup,
            dataType: 'JSON',
            success: function(response){
                $('#listaEstructura tbody').html(response['html']);
                var html = '<tr style="font-size: 16px;">'+
                '<th class="right blue">Total Presupuestado:</th>'+
                '<th class="right blue">'+mnd+' '+formatNumber.decimal(response['total'],'',-2)+'</th>'+
                '<th class="right red">Total Consumido:</th>'+
                '<th class="right red">'+mnd+' '+formatNumber.decimal(response['total_oc'],'',-2)+'</th>'+
                '<th class="right green">Total Saldo:</th>'+
                '<th class="right green">'+mnd+' '+formatNumber.decimal(response['total'] - response['total_oc'],'',-2)+'</th>'+
                '</tr>';
                $('#totales tbody').html(html);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}