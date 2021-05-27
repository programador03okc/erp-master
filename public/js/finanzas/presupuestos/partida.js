$("#listaPartidas tbody").on('click', ".agregar-partida", function(){
    var cod = $(this).data('codigo');
    var des = $(this).data('descripcion');
    
    var i = 1;
    var filas = document.querySelectorAll('#listaPartidas tbody tr');
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        console.log(colum.length);
        
        if (colum.length > 5){
            var padre = colum[5].innerText;
            if (padre == cod){
                i++;
            }
        }
    });

    $('#partidaCreate').modal({
        show: true
    });
    $('#submit-partidaCreate').removeAttr('disabled');

    $('[name=codigo]').val(cod+'.'+leftZero(2,i));
    $('[name=cod_padre]').val(cod);
    $('[name=id_partida]').val('');
    $('[name=descripcion]').val('');
    $('[name=importe_total]').val('');
    $('#cod_padre').text(cod);
    $('#descripcion_padre').text(des);

});

$("#form-partidaCreate").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    var id = $('[name=id_partida]').val();
    var url = '';
    $('#submit-partidaCreate').attr('disabled','true');
    
    if (id == ''){
        url = 'guardar-partida';
    } else {
        url = 'actualizar-partida';
    }
    guardar_partida(data, url);

    $('#partidaCreate').modal('hide');
});

function guardar_partida(data, url){
    console.log(data);
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': csrf_token},
        url: url,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            mostrarPartidas(response.id_presup);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#listaPartidas tbody").on('click', ".editar-partida", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var des = $(this).data('des');
    var total = $(this).data('total');
    var codp = $(this).data('codpadre');
    var desp = $(this).data('despadre');

    $('#partidaCreate').modal({
        show: true
    });
    $('#submit-partidaCreate').removeAttr('disabled');

    $('[name=id_partida]').val(id);
    $('[name=codigo]').val(cod);
    $('[name=cod_padre]').val(codp);
    $('[name=descripcion]').val(des);
    $('[name=importe_total]').val(total);
    $('#cod_padre').text(codp);
    $('#descripcion_padre').text(desp);

});

$("#listaPartidas tbody").on('click', ".anular-partida", function(){
    var id = $(this).data('id');
    var rspta = confirm('¿Está seguro que desea anular?');
    if (rspta){
        anular_partida(id);
    }
});

function anular_partida(id){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': csrf_token},
        url: "anular-partida/"+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var id_pres = $('[name=id_presup]').val();
            mostrarPartidas(id_pres);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}