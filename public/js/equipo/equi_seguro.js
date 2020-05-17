$(function(){
    $('#listaSeguros tbody').html('');
    $("#form-seguro").on("submit", function(e){
        e.preventDefault();
        guardar_seguro();
    });
});
function open_seguro(data){
    $('#modal-equi_seguro').modal({
        show: true
    });
    console.log(data);
    $('[name=id_equipo]').val(data.id_equipo);
    $('#cod_equipo').text(data.codigo);
    $('#des_equipo').text(data.descripcion);
    
    listar_seguros(data.id_equipo);
}
function listar_seguros(id_equipo){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_seguros/'+id_equipo,
        dataType: 'JSON',
        success: function(response){
            $('#listaSeguros tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guardar_seguro(){
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();

    if (ffin > fini){
        var formData = new FormData($('#form-seguro')[0]);
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_seguro',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Seguro registrado con éxito');
                    var id_equipo = $('[name=id_equipo]').val();
                    listar_seguros(id_equipo);
                    $('[name=id_tp_seguro]').val('');
                    $('[name=nro_poliza]').val('');
                    $('[name=id_proveedor]').val('');
                    $('[name=fecha_inicio]').val('');
                    $('[name=fecha_fin]').val('');
                    $('[name=importe]').val('');
                    $('[name=adjunto]').val('');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('La Fecha Fin debe ser mayor que la Fecha de Inicio');
    }
}
function anular_seguro(id_seguro){
    if (id_seguro !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste seguro?")
        if (rspta){
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_seguro/'+id_seguro,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Seguro anulado con éxito');
                        var id = $('[name=id_equipo]').val();
                        listar_seguros(id);
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
function agregar_tipo(){
    var nombre = prompt('Ingrese el Nombre del nuevo tipo','');
    console.log(nombre);
    if (nombre !== null){
        var rspta = confirm("¿Está seguro que desea agregar éste tipo: "+nombre+"?")
        if (rspta){
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'guardar_tipo_doc/'+nombre,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    // alert('Tipo '+nombre+' agregado con éxito');
                    $('[name=id_tp_seguro]').html('');
                    var html = '<option value="0" disabled>Elija una opción</option>'+response;
                    $('[name=id_tp_seguro]').html(html);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    // } else {
    //     alert('Debe ingresar un caracter válido!');
    }
}
