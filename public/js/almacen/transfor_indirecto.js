function guardar_indirecto(id){
    var id_trans = $('[name=id_transformacion]').val();
    var data =  'cod_item='+id+
            '&id_transformacion='+id_trans+
            '&tasa=1'+
            '&parametro=1'+
            '&valor_unitario=1'+
            '&valor_total=1';
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_indirecto',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_indirectos(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_indirectos(id_transformacion){
    $('#listaCostosIndirectos tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_indirectos/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            $('#listaCostosIndirectos tbody').html(response);
            total_indirecto();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_indirecto(id_indirecto){
    $("#ind-"+id_indirecto+" td").find("input[name=ind_tasa]").removeAttr('disabled');
    $("#ind-"+id_indirecto+" td").find("input[name=ind_parametro]").removeAttr('disabled');
    $("#ind-"+id_indirecto+" td").find("input[name=ind_valor_unitario]").removeAttr('disabled');

    $("#ind-"+id_indirecto+" td").find("i.blue").removeClass('visible');
    $("#ind-"+id_indirecto+" td").find("i.blue").addClass('oculto');
    $("#ind-"+id_indirecto+" td").find("i.green").removeClass('oculto');
    $("#ind-"+id_indirecto+" td").find("i.green").addClass('visible');
}
function update_indirecto(id_indirecto){
    var tasa = $("#ind-"+id_indirecto+" td").find("input[name=ind_tasa]").val();
    var para = $("#ind-"+id_indirecto+" td").find("input[name=ind_parametro]").val();
    var unit = $("#ind-"+id_indirecto+" td").find("input[name=ind_valor_unitario]").val();
    var tota = $("#ind-"+id_indirecto+" td").find("input[name=ind_valor_total]").val();

    var data = 'id_indirecto='+id_indirecto+
            '&tasa='+tasa+
            '&parametro='+para+
            '&valor_unitario='+unit+
            '&valor_total='+tota;
    console.log(data);

    $.ajax({
        type: 'POST',
        url: 'update_indirecto',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item actualizado con éxito');
                $("#ind-"+id_indirecto+" td").find("input").attr('disabled',true);
                $("#ind-"+id_indirecto+" td").find("i.blue").removeClass('oculto');
                $("#ind-"+id_indirecto+" td").find("i.blue").addClass('visible');
                $("#ind-"+id_indirecto+" td").find("i.green").removeClass('visible');
                $("#ind-"+id_indirecto+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_indirecto(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            url: 'anular_indirecto/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#ind-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function calcula_total(id_indirecto){
    var cant = $('#ind-'+id_indirecto+' input[name=ind_tasa]').val();
    var unit = $('#ind-'+id_indirecto+' input[name=ind_valor_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#ind-'+id_indirecto+' input[name=ind_valor_total]').val(cant * unit);
    } else {
        $('#ind-'+id_indirecto+' input[name=ind_valor_total]').val(0);
    }
    total_indirecto();
}
function total_indirecto(){
    var total = 0;
    $("input[name=ind_valor_total]").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    console.log('total='+total);
    $('[name=total_indirectos]').val(total);
}