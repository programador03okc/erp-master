function guardar_transformado(id){
    var id_trans = $('[name=id_transformacion]').val();
    var data =  'id_producto='+id+
            '&id_transformacion='+id_trans+
            '&cantidad=1'+
            '&valor_unitario=1'+
            '&valor_total=1';
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_transformado',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_transformados(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_transformados(id_transformacion){
    $('#listaProductoTransformado tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_transformados/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            $('#listaProductoTransformado tbody').html(response);
            total_transformado();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_transformado(id_transformado){
    $("#tra-"+id_transformado+" td").find("input[name=tra_cantidad]").removeAttr('disabled');
    $("#tra-"+id_transformado+" td").find("input[name=tra_valor_unitario]").removeAttr('disabled');

    $("#tra-"+id_transformado+" td").find("i.blue").removeClass('visible');
    $("#tra-"+id_transformado+" td").find("i.blue").addClass('oculto');
    $("#tra-"+id_transformado+" td").find("i.green").removeClass('oculto');
    $("#tra-"+id_transformado+" td").find("i.green").addClass('visible');
}
function update_transformado(id_transformado){
    var cant = $("#tra-"+id_transformado+" td").find("input[name=tra_cantidad]").val();
    var unit = $("#tra-"+id_transformado+" td").find("input[name=tra_valor_unitario]").val();
    var tota = $("#tra-"+id_transformado+" td").find("input[name=tra_valor_total]").val();

    var data = 'id_transformado='+id_transformado+
            '&cantidad='+cant+
            '&valor_unitario='+unit+
            '&valor_total='+tota;
    console.log(data);

    $.ajax({
        type: 'POST',
        url: 'update_transformado',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item actualizado con éxito');
                $("#tra-"+id_transformado+" td").find("input").attr('disabled',true);
                $("#tra-"+id_transformado+" td").find("i.blue").removeClass('oculto');
                $("#tra-"+id_transformado+" td").find("i.blue").addClass('visible');
                $("#tra-"+id_transformado+" td").find("i.green").removeClass('visible');
                $("#tra-"+id_transformado+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_transformado(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            url: 'anular_transformado/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#tra-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function calcula_transformado(id_transformado){
    var cant = $('#tra-'+id_transformado+' input[name=tra_cantidad]').val();
    var unit = $('#tra-'+id_transformado+' input[name=tra_valor_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#tra-'+id_transformado+' input[name=tra_valor_total]').val(cant * unit);
    } else {
        $('#tra-'+id_transformado+' input[name=tra_valor_total]').val(0);
    }
    total_transformado();
}
function total_transformado(){
    var total = 0;
    $("input[name=tra_valor_total]").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    console.log('total='+total);
    $('[name=total_transformado]').val(total);
}
