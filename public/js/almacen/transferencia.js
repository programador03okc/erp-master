function open_transferencia(){
    var id_guia_ven = $('[name=id_guia_ven]').val();

    if (id_guia_ven !== '') {
        var op = $('[name=id_operacion]').val();
        var cont = $('[name=id_contrib]').val();
        console.log('ope: '+op);
        console.log('id_contrib: '+cont);

        if (op == 11 || (op == 1 && cont >= 1 && cont <= 5)){
            $('#modal-transferencia').modal({
                show: true
            });
            var id_almacen = $('[name=id_almacen]').val();
            var usuario = $('[name=usuario]').val();
            $('[name=id_almacen_origen]').val(id_almacen);
            $('[name=responsable_origen]').val(usuario);
            cargar_almacenes_destino(cont);
        } else {
            alert("El tipo de operación de la Guia no es de Transferencia.");
        }
    } else {
        alert('Debe seleccionar una Guia!');
    }
}
function cargar_almacenes_destino(id_contrib){
    // var id_sede = $('[name=id_sede]').val();
    // console.log(id_sede);
    if (id_contrib !== ''){
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'cargar_almacenes_contrib/'+id_contrib,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var option = '';
                for (var i=0; i<response.length; i++){
                    if (response.length){
                        option+='<option value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    } else {
                        option+='<option value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    }
                }
                $('[name=id_almacen_destino]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        // next_serie_numero();
    }
}

$("#form-transferencia").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardar_transferencia(data);
});
function revisar_almacen(text){
    var alm_ori = $('[name=id_almacen_origen]').val();
    var alm_des = $('[name=id_almacen_destino]').val();
    console.log('alm_ori'+alm_ori+' alm_des'+alm_des);

    if (alm_ori !== 0 && alm_des !== 0){
        if (alm_ori == alm_des){
            $('[name=id_almacen_'+text+']').val(0).trigger('change.select2');
            alert('No puede elegir el mismo almacen para el '+text);
        }
    }
}
function guardar_transferencia(data){
    $.ajax({
        type: 'POST',
        url: 'guardar_transferencia',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['id_trans'] > 0){
                $('#modal-transferencia').modal('hide');
                $('#codigo_trans').text(response['codigo']);
                alert('Se generó correctamente la Transferencia.'+response['codigo']);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
