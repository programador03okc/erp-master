
$("#form-od_adjunto").on("submit", function(e){
    e.preventDefault();
    var nro = $('#listaAdjuntos tbody tr').length;
    $('[name=numero]').val(nro+1);
    guardar_od_adjunto();
});

function listarAdjuntos(id){
    $.ajax({
        type: 'GET',
        url: 'listarAdjuntosOrdenDespacho/'+id,
        dataType: 'JSON',
        success: function(response){
            $('#listaAdjuntos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_od_adjunto(){
    var formData = new FormData($('#form-od_adjunto')[0]);
    var id = $('[name=id_od]').val();
    var adjunto = $('[name=archivo_adjunto]').val();
    var desc = $('[name=descripcion]').val();
    var nro = $('[name=numero]').val();
    console.log(adjunto);
    console.log(desc);
    if (desc == '' && adjunto == null){
        alert('Debe seleccionar un archivo o ingresar una descripción!');
    } else {
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_od_adjunto',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    // alert('Adjunto registrado con éxito');
                    listarAdjuntos(id);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_adjunto(id_od_adjunto){
    if (id_od_adjunto !== ''){
        var rspta = confirm("¿Está seguro que desea anular el adjunto?")
        if (rspta){
            var id = $('[name=id_od]').val();
            $.ajax({
                type: 'GET',
                url: 'anular_od_adjunto/'+id_od_adjunto,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Adjunto anulado con éxito');
                        listarAdjuntos(id);
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
