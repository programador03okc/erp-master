function guardar_materia(id){
    var id_trans = $('[name=id_transformacion]').val();
    var data =  'id_producto='+id+
            '&id_transformacion='+id_trans+
            '&cantidad=1'+
            '&valor_unitario=1'+
            '&valor_total=1';
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_materia',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_materias(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_materias(id_transformacion){
    $('#listaMateriasPrimas tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_materias/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            $('#listaMateriasPrimas tbody').html(response);
            total_materia();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_materia(id_materia){
    $("#mat-"+id_materia+" td").find("input[name=mat_cantidad]").removeAttr('disabled');
    $("#mat-"+id_materia+" td").find("input[name=mat_valor_unitario]").removeAttr('disabled');

    $("#mat-"+id_materia+" td").find("i.blue").removeClass('visible');
    $("#mat-"+id_materia+" td").find("i.blue").addClass('oculto');
    $("#mat-"+id_materia+" td").find("i.green").removeClass('oculto');
    $("#mat-"+id_materia+" td").find("i.green").addClass('visible');
}
function update_materia(id_materia){
    var cant = $("#mat-"+id_materia+" td").find("input[name=mat_cantidad]").val();
    var unit = $("#mat-"+id_materia+" td").find("input[name=mat_valor_unitario]").val();
    var tota = $("#mat-"+id_materia+" td").find("input[name=mat_valor_total]").val();

    var data = 'id_materia='+id_materia+
            '&cantidad='+cant+
            '&valor_unitario='+unit+
            '&valor_total='+tota;
    console.log(data);

    $.ajax({
        type: 'POST',
        url: 'update_materia',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item actualizado con éxito');
                $("#mat-"+id_materia+" td").find("input").attr('disabled',true);
                $("#mat-"+id_materia+" td").find("i.blue").removeClass('oculto');
                $("#mat-"+id_materia+" td").find("i.blue").addClass('visible');
                $("#mat-"+id_materia+" td").find("i.green").removeClass('visible');
                $("#mat-"+id_materia+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_materia(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            url: 'anular_materia/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#mat-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function calcula_materia(id_materia){
    var cant = $('#mat-'+id_materia+' input[name=mat_cantidad]').val();
    var unit = $('#mat-'+id_materia+' input[name=mat_valor_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#mat-'+id_materia+' input[name=mat_valor_total]').val(cant * unit);
    } else {
        $('#mat-'+id_materia+' input[name=mat_valor_total]').val(0);
    }
    total_materia();
}
function total_materia(){
    var total = 0;
    $("input[name=mat_valor_total]").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    console.log('total='+total);
    $('[name=total_materias]').val(total);
}
