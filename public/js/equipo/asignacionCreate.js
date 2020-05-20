$(function(){
    $('[name=fecha_asignacion]').val(fecha_actual());
    $("#form-asignacion").on("submit", function(e){
        e.preventDefault();
        console.log('submit');
        guardar_asignacion();
    });
});
function asignacionModal(id_equipo, cod, des){
    if (id_equipo !== ''){
        $('#modal-asignacion').modal({
            show:true
        });
        // if (data !== null){
            $('#cod_equipo').text(cod);
            $('#des_equipo').text(des);
        //     $('[name=area_solicitud]').val(area);
        //     $('[name=trabajador]').val(trab);
            $('[name=id_equipo]').val(id_equipo);
        //     $('[name=id_solicitud]').val(id_solicitud);
            var fini = $('[name=fecha_inicio]').val();
            var ffin = $('[name=fecha_fin]').val();
            console.log('fini'+fini+' ffin'+ffin);

            $.ajax({
                type: 'GET',
                url: 'kilometraje_actual/'+id_equipo,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    $('[name=kilometraje]').val(response);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        // }
    } else {
        alert('Debe seleccionar un Equipo!');
    }
}
function guardar_asignacion(){
    $('[name=usuario]').val(auth_user.id_usuario);
    var formData = new FormData($('#form-asignacion')[0]);
    console.log(formData);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_asignacion',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Asignación registrada con éxito');
                $('#modal-asignacion').modal('hide');
                clearDataTable();
                listar_sol_todas();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}