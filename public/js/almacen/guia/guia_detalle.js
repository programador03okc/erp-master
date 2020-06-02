function listar_detalle(guia){
    $('#listaDetalle tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guia_detalle/'+guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaDetalle tbody').html(response['html']);
            $('[name=total_guia_detalle]').val(response['suma']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_detalle(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_detalle/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#reg-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function editar_detalle(id, oc){
    $("#reg-"+id+" td").find("select").attr('disabled',false);
    // $("#reg-"+id+" td").find("input[name=cantidad]").attr('disabled',false);
    $("#reg-"+id+" td").find("input[name=cantidad]").removeAttr('disabled');
    console.log(id);
    if (oc == 0){
        $("#reg-"+id+" td").find("input[name=unitario]").attr('disabled',false);
    }
    $("#reg-"+id+" td").find("i.blue").removeClass('visible');
    $("#reg-"+id+" td").find("i.blue").addClass('oculto');
    $("#reg-"+id+" td").find("i.green").removeClass('oculto');
    $("#reg-"+id+" td").find("i.green").addClass('visible');
}
function update_detalle(id){
    var idPos = $("#reg-"+id+" td").find("select").val();
    var cant = $("#reg-"+id+" td").find("input[name=cantidad]").val();
    var unit = $("#reg-"+id+" td").find("input[name=unitario]").val();
    var total = $("#reg-"+id+" td").find("input[name=total]").val();
    var data =  'id_guia_com_det='+id+
            '&id_posicion='+idPos+
            '&cantidad='+cant+
            '&unitario='+unit+
            '&total='+total;
    console.log(data);
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'update_guia_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item actualizado con éxito');
                $("#reg-"+id+" td").find("select").attr('disabled',true);
                $("#reg-"+id+" td").find("input").attr('disabled',true);
                $("#reg-"+id+" td").find("i.blue").removeClass('oculto');
                $("#reg-"+id+" td").find("i.blue").addClass('visible');
                $("#reg-"+id+" td").find("i.green").removeClass('visible');
                $("#reg-"+id+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guardar_guia_detalle(id,unid_med){
    var id_guia = $('[name=id_guia]').val();

    var data =  'id_producto='+id+
            '&id_guia='+id_guia+
            '&id_posicion='+
            '&cantidad=1'+
            '&id_unid_med='+unid_med+
            '&unitario=0'+
            '&usuario=1'+
            '&total=0';

    console.log(data);
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_guia_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert('Item guardado con éxito');
            console.log('id_guia:'+id_guia);
            listar_detalle(id_guia);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function calcula_total(id_guia_det){
    var cant = $('#reg-'+id_guia_det+' input[name=cantidad]').val();
    var unit = $('#reg-'+id_guia_det+' input[name=unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#reg-'+id_guia_det+' input[name=total]').val(cant * unit);
    } else {
        $('#reg-'+id_guia_det+' input[name=total]').val(0);
    }
}
function calcula_total_oc(id_oc_det){
    var cant = $('#oc-'+id_oc_det+' input[name=cantidad]').val();
    var unit = $('#oc-'+id_oc_det+' input[name=unitario]').val();
    
    if (cant !== '' && unit !== '') {
        $('#oc-'+id_oc_det+' input[name=total]').val(cant * unit);
    } else {
        $('#oc-'+id_oc_det+' input[name=total]').val(0);
    }
}
