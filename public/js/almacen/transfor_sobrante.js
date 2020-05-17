function guardar_sobrante(id){
    var id_trans = $('[name=id_transformacion]').val();
    var data =  'id_producto='+id+
            '&id_transformacion='+id_trans+
            '&cantidad=1'+
            '&valor_unitario=1'+
            '&valor_total=1';
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_sobrante',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_sobrantes(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_sobrantes(id_transformacion){
    $('#listaSobrantes tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_sobrantes/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            $('#listaSobrantes tbody').html(response);
            total_sobrante();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_sobrante(id_sobrante){
    $("#sob-"+id_sobrante+" td").find("input[name=sob_cantidad]").removeAttr('disabled');
    $("#sob-"+id_sobrante+" td").find("input[name=sob_valor_unitario]").removeAttr('disabled');

    $("#sob-"+id_sobrante+" td").find("i.blue").removeClass('visible');
    $("#sob-"+id_sobrante+" td").find("i.blue").addClass('oculto');
    $("#sob-"+id_sobrante+" td").find("i.green").removeClass('oculto');
    $("#sob-"+id_sobrante+" td").find("i.green").addClass('visible');
}
function update_sobrante(id_sobrante){
    var cant = $("#sob-"+id_sobrante+" td").find("input[name=sob_cantidad]").val();
    var unit = $("#sob-"+id_sobrante+" td").find("input[name=sob_valor_unitario]").val();
    var tota = $("#sob-"+id_sobrante+" td").find("input[name=sob_valor_total]").val();

    var data = 'id_sobrante='+id_sobrante+
            '&cantidad='+cant+
            '&valor_unitario='+unit+
            '&valor_total='+tota;
    console.log(data);

    $.ajax({
        type: 'POST',
        url: 'update_sobrante',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item actualizado con éxito');
                $("#sob-"+id_sobrante+" td").find("input").attr('disabled',true);
                $("#sob-"+id_sobrante+" td").find("i.blue").removeClass('oculto');
                $("#sob-"+id_sobrante+" td").find("i.blue").addClass('visible');
                $("#sob-"+id_sobrante+" td").find("i.green").removeClass('visible');
                $("#sob-"+id_sobrante+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_sobrante(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            url: 'anular_sobrante/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#sob-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function calcula_sobrante(id_sobrante){
    var cant = $('#sob-'+id_sobrante+' input[name=sob_cantidad]').val();
    var unit = $('#sob-'+id_sobrante+' input[name=sob_valor_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#sob-'+id_sobrante+' input[name=sob_valor_total]').val(cant * unit);
    } else {
        $('#sob-'+id_sobrante+' input[name=sob_valor_total]').val(0);
    }
    total_sobrante();
}
function total_sobrante(){
    var total = 0;
    $("input[name=sob_valor_total]").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    console.log('total='+total);
    $('[name=total_sobrantes]').val(total);

    ///////////////////
    var costo_primo = $('[name=costo_primo]').val();
    var total_indirectos = $('[name=total_indirectos]').val();
    var total_sobrantes = $('[name=total_sobrantes]').val();
    var total = 0;

    console.log(costo_primo+' '+total_indirectos+' '+total_sobrantes);
    total = parseFloat(costo_primo) + parseFloat(total_indirectos) - parseFloat(total_sobrantes);
    $('[name=costo_transformacion]').val(formatDecimal(total));
    
}
