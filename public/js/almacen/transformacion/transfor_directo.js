function guardar_directo(id){
    var id_trans = $('[name=id_transformacion]').val();
    var data =  'id_servicio='+id+
            '&id_transformacion='+id_trans+
            '&cantidad=1'+
            '&valor_unitario=1'+
            '&valor_total=1';
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_directo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_directos(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_directos(id_transformacion){
    $('#listaServiciosDirectos tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_directos/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            $('#listaServiciosDirectos tbody').html(response);
            total_directo();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_directo(id_directo){
    $("#dir-"+id_directo+" td").find("input[name=dir_cantidad]").removeAttr('disabled');
    $("#dir-"+id_directo+" td").find("input[name=dir_valor_unitario]").removeAttr('disabled');

    $("#dir-"+id_directo+" td").find("i.blue").removeClass('visible');
    $("#dir-"+id_directo+" td").find("i.blue").addClass('oculto');
    $("#dir-"+id_directo+" td").find("i.green").removeClass('oculto');
    $("#dir-"+id_directo+" td").find("i.green").addClass('visible');
}
function update_directo(id_directo){
    var cant = $("#dir-"+id_directo+" td").find("input[name=dir_cantidad]").val();
    var unit = $("#dir-"+id_directo+" td").find("input[name=dir_valor_unitario]").val();
    var tota = $("#dir-"+id_directo+" td").find("input[name=dir_valor_total]").val();

    var data = 'id_directo='+id_directo+
            '&cantidad='+cant+
            '&valor_unitario='+unit+
            '&valor_total='+tota;
    console.log(data);

    $.ajax({
        type: 'POST',
        url: 'update_directo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item actualizado con éxito');
                $("#dir-"+id_directo+" td").find("input").attr('disabled',true);
                $("#dir-"+id_directo+" td").find("i.blue").removeClass('oculto');
                $("#dir-"+id_directo+" td").find("i.blue").addClass('visible');
                $("#dir-"+id_directo+" td").find("i.green").removeClass('visible');
                $("#dir-"+id_directo+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_directo(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            url: 'anular_directo/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#dir-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function calcula_directo(id_directo){
    var cant = $('#dir-'+id_directo+' input[name=dir_cantidad]').val();
    var unit = $('#dir-'+id_directo+' input[name=dir_valor_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#dir-'+id_directo+' input[name=dir_valor_total]').val(cant * unit);
    } else {
        $('#dir-'+id_directo+' input[name=dir_valor_total]').val(0);
    }
    total_directo();
}
function total_directo(){
    var total = 0;
    $("input[name=dir_valor_total]").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    console.log('total='+total);
    $('[name=total_directos]').val(total);

    var tot_materias = $('[name=total_materias]').val();
    var costo_primo = parseFloat(tot_materias) + total;
    console.log('costo_primo:'+costo_primo);
    $('[name=costo_primo]').val(costo_primo);

}