$(function(){
    $("#form-mtto_detalle").on("submit", function(e){
        e.preventDefault();
        console.log('submit');
        guardar_detalle();
    });
});
function mtto_detalleModal(){
    var id_mtto = $('[name=id_mtto]').val();
    if (id_mtto !== ''){
        $('#modal-mtto_detalle').modal({
            show:true
        });
        $('[name=id_mtto_padre]').val(id_mtto);
        $('[name=tp_mantenimiento]').val('');
        $('[name=descripcion]').val('');
        $('[name=cantidad]').val('');
        $('[name=precio_unitario]').val('');
        $('[name=precio_total]').val('');
        $('[name=resultado]').val('');
        $('[name=id_partida]').val('');
        $('[name=cod_partida]').val('');
        $('[name=des_partida]').val('');

        var id_equipo = $('[name=id_equipo]').val();
        console.log('id_equipo:'+id_equipo);
        if (id_equipo !== ''){
            $.ajax({
                type: 'GET',
                url: 'select_programaciones/'+id_equipo,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    $('[name=id_programacion]').html(response);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

    } else {
        alert('Es necesario que guarde un mantenimiento');
    }
}
function editar_detalle(id_mtto_det){
    mtto_detalleModal();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_mtto_detalle/'+id_mtto_det,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_mtto_det]').val(response[0].id_mtto_det);
            $('[name=tp_mantenimiento]').val(response[0].tp_mantenimiento);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=cantidad]').val(response[0].cantidad);
            $('[name=precio_unitario]').val(response[0].precio_unitario);
            $('[name=precio_total]').val(response[0].precio_total);
            $('[name=resultado]').val(response[0].resultado);
            $('[name=id_partida]').val(response[0].id_partida);
            $('[name=cod_partida]').val(response[0].cod_partida);
            $('[name=des_partida]').val(response[0].des_partida);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guardar_detalle(){
    var formData = new FormData($('#form-mtto_detalle')[0]);
    console.log(formData);
    var id = $('[name=id_mtto_det]').val();
    var baseUrl = '';
    if (id !== ''){
        baseUrl = 'update_mtto_detalle';
    } else {
        baseUrl = 'guardar_mtto_detalle';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Detalle registrado con éxito');
                $('#modal-mtto_detalle').modal('hide');
                var id_mtto = $("[name=id_mtto_padre]").val();
                console.log(id_mtto);
                listar_mtto_detalle(id_mtto);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function calcula_total(){
    var cant = $('[name=cantidad]').val();
    var unit = $('[name=precio_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== ''){
        var total = Math.round((cant * unit) * 100) / 100;
        $('[name=precio_total]').val(total);
    }
}
