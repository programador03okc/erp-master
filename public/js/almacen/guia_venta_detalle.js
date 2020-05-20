function listar_detalle(guia){
    var id_almacen = $('[name=id_almacen]').val();
    console.log('id_almacen: '+id_almacen);
    
    $('#listaDetalle tbody').html('');

    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guia_ven_det/'+guia,
        dataType: 'JSON',
        success: function(response){
            $('#listaDetalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_detalle(id){
    $("#reg-"+id+" td").find("select").attr('disabled',false);
    $("#reg-"+id+" td").find("input[name=cantidad]").attr('disabled',false);
    $("#reg-"+id+" td").find("i.blue").removeClass('visible');
    $("#reg-"+id+" td").find("i.blue").addClass('oculto');
    $("#reg-"+id+" td").find("i.green").removeClass('oculto');
    $("#reg-"+id+" td").find("i.green").addClass('visible');
}
function update_detalle(id){
    var idPo = $("#reg-"+id+" td").find("select").val();
    var cant = $("#reg-"+id+" td").find("input[name=cantidad]").val();
    var data =  'id_guia_ven_det='+id+
                '&cantidad='+cant+
                '&id_posicion='+idPo;
                // '&unitario='+unit+
                // '&total='+total;
    console.log(data);
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'update_guia_ven_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item actualizado con éxito');
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
function anular_detalle(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'anular_guia_ven_detalle/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#reg-"+id).remove();
                    onChangeTipo();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function guardar_guia_detalle(id,unid_med,id_posicion){
    var id_guia = $('[name=id_guia_ven]').val();

    var data =  'id_producto='+id+
            '&id_guia_ven='+id_guia+
            '&id_posicion='+id_posicion+
            '&cantidad=1'+
            '&id_unid_med='+unid_med;
            // '&usuario='+auth_user.id_usuario;

    console.log(data);
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_guia_ven_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                onChangeTipo();
                listar_detalle(id_guia);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
