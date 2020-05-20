$(function(){
    $("#form-presLeccion").on("submit", function(e){
        e.preventDefault();
        guardar_leccion();
    });
});
function limpiar_campos(){
    $('#listaLecciones tbody').html('');
    $('[name=id_cd_partida]').val('');
    $('[name=id_ci_detalle]').val('');
    $('[name=id_gg_detalle]').val('');
    $('[name=observacion]').val('');
    $('[name=adjunto]').val('');
}
function open_presLeccion(tipo,id_partida){
    console.log('tipo:'+tipo+' - id_partida:'+id_partida);
    $('#modal-presLeccion').modal({
        show: true
    });
    limpiar_campos();
    if (tipo=='cd') {
        $('[name=id_cd_partida]').val(id_partida);
    } 
    else if (tipo=='ci') {
        $('[name=id_ci_detalle]').val(id_partida);
    } 
    else if (tipo=='gg') {
        $('[name=id_gg_detalle]').val(id_partida);
    }

    var filas = document.querySelectorAll('#par-'+id_partida);
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        var cod = colum[0].innerText;
        var des = colum[1].innerText;
        
        $('#cod_partida').text(cod);
        $('#des_partida').text(des);
    });
    if (tipo=='cd') {listar_obs_cd(id_partida);}
    if (tipo=='ci') {listar_obs_ci(id_partida);}
    if (tipo=='gg') {listar_obs_gg(id_partida);}
}
function listar_obs_cd(id_partida){
    $('#listaLecciones tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_obs_cd/'+id_partida,
        dataType: 'JSON',
        success: function(response){
            $('#listaLecciones tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_obs_ci(id_partida){
    $('#listaLecciones tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_obs_ci/'+id_partida,
        dataType: 'JSON',
        success: function(response){
            $('#listaLecciones tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_obs_gg(id_partida){
    $('#listaLecciones tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_obs_gg/'+id_partida,
        dataType: 'JSON',
        success: function(response){
            $('#listaLecciones tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_leccion(){
    var id_par_cd = $('[name=id_cd_partida]').val();
    var id_par_ci = $('[name=id_ci_detalle]').val();
    var id_par_gg = $('[name=id_gg_detalle]').val();

    var formData = new FormData($('#form-presLeccion')[0]);
    console.log(formData);
    
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_obs_partida',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Lección registrada con éxito');
                $('[name=observacion]').val('');
                $('[name=adjunto]').val('');
                console.log(id_par_cd);
                console.log(id_par_ci);
                console.log(id_par_gg);
                if (id_par_cd !== ''){
                    listar_obs_cd(id_par_cd);
                } 
                else if (id_par_ci !== ''){
                    listar_obs_ci(id_par_ci);
                } 
                else if (id_par_gg !== ''){
                    listar_obs_gg(id_par_gg);
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_obs(id_obs){
    if (id_obs !== ''){
        var rspta = confirm("¿Está seguro que desea anular ésta lección?")
        if (rspta){
            var id_par_cd = $('[name=id_cd_partida]').val();
            var id_par_ci = $('[name=id_ci_detalle]').val();
            var id_par_gg = $('[name=id_gg_detalle]').val();
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_obs_partida/'+id_obs,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Lección anulada con éxito');
                        if (id_par_cd !== ''){
                            listar_obs_cd(id_par_cd);
                        } 
                        else if (id_par_ci !== ''){
                            listar_obs_ci(id_par_ci);
                        } 
                        else if (id_par_gg !== ''){
                            listar_obs_gg(id_par_gg);
                        }
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

