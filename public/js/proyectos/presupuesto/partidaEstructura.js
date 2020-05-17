function guardar_partida(id){
    var id_pres = $('[name=id_presup]').val();
    var cod = $('[name=codigo]').val();
    var pad = $('[name=cod_padre]').val();

    var data = 'id_presup='+id_pres+
            '&codigo='+cod+
            '&id_pardet='+id+
            '&cod_padre='+pad;
    
    console.log(data);
    var baseUrl = 'guardar_partida';
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Partida registrada con éxito');
                listar_presupuesto(id_pres);
                $('#modal-partidaEstructura').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_partida(id_partida){
    $("#par-"+id_partida+" td").find("input[name=relacionado]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("input[name=relacionado]").focus();
    $("#par-"+id_partida+" td").find("i.blue").removeClass('visible');
    $("#par-"+id_partida+" td").find("i.blue").addClass('oculto');
    $("#par-"+id_partida+" td").find("i.green").removeClass('oculto');
    $("#par-"+id_partida+" td").find("i.green").addClass('visible');
}

function update_partida(id_partida){
    var des = $("#par-"+id_partida+" td").find("input[name=relacionado]").val();
    var data =  'id_partida='+id_partida+
                '&relacionado='+des;
    var id_pres = $('[name=id_presup]').val();

    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'update_partida',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Título actualizado con éxito');
                $("#par-"+id_partida+" td").find("input[name=relacionado]").attr('disabled',true);
                $("#par-"+id_partida+" td").find("i.blue").removeClass('oculto');
                $("#par-"+id_partida+" td").find("i.blue").addClass('visible');
                $("#par-"+id_partida+" td").find("i.green").removeClass('visible');
                $("#par-"+id_partida+" td").find("i.green").addClass('oculto');
                listar_presupuesto(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_partida(id_partida){
    var id_pres = $('[name=id_presup]').val();
    if (id_pres !== ''){
        $msj = confirm('¿Está seguro que desea anular ésta partida?');
        if ($msj){
            $.ajax({
                type: 'GET',
                url: 'anular_partida/'+id_partida,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        listar_presupuesto(id_pres);
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
}