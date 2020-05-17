$(function(){
    $('#listaProgramaciones tbody').html('');
    $("#form-mtto_programacion").on("submit", function(e){
        e.preventDefault();
        guardar_programacion();
    });
});
function open_programacion(data){
    $('#modal-mtto_programacion').modal({
        show: true
    });
    console.log(data);
    $('[name=id_equipo]').val(data.id_equipo);
    $('#cod_equipo_mtto').text(data.codigo);
    $('#des_equipo_mtto').text(data.descripcion);
    listar_programaciones(data.id_equipo);
}
function listar_programaciones(id_equipo){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_programaciones/'+id_equipo,
        dataType: 'JSON',
        success: function(response){
            console.log(response['kactual']);
            $('#listaProgramaciones tbody').html(response['html']);
            $('#kactual').text(response['kactual']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guardar_programacion(){
    $('[name=usuario]').val(auth_user.id_usuario);
    var formData = new FormData($('#form-mtto_programacion')[0]);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_programacion',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Programacion registrada con éxito');
                var id_equipo = $('[name=id_equipo]').val();
                listar_programaciones(id_equipo);
                $('[name=descripcion]').val('');
                $('[name=kilometraje_inicial]').val('');
                $('[name=kilometraje_rango]').val('');
                $('[name=fecha_inicial]').val('');
                $('[name=tiempo]').val('');
                $('[name=unid_program]').val('');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_programacion(id_programacion){
    if (id_programacion !== ''){
        var rspta = confirm("¿Está seguro que desea anular ésta Programación?")
        if (rspta){
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_programacion/'+id_programacion,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Programación anulada con éxito');
                        var id = $('[name=id_equipo]').val();
                        listar_programaciones(id);
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
function cambio_segun(){
    var s = $('[name=segun]').val();
    if (s == "2"){
        $('#tiempo').removeClass('oculto');
        $('#kilom').addClass('oculto');
    } else {
        $('#tiempo').addClass('oculto');
        $('#kilom').removeClass('oculto');
    }
}