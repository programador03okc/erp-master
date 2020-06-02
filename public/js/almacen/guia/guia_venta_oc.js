function onChangeTipo(){
    var tipo = $('[name=tipo]').val();
    var id_guia_ven = $('[name=id_guia_ven]').val();
    console.log('tipo'+tipo);
    if (id_guia_ven !== ''){
        if (tipo == 1){
            var id_almacen = $('[name=id_almacen]').val();
            listar_guias_almacen(id_almacen);
        } 
        else if (tipo == 2){
            var id_sede = $('[name=id_sede]').val();
            listar_req(id_sede);
        } 
        // else if (tipo == 3){
        //     $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>');
        //     $('[name=docs_sustento]').val(0).trigger('change.select2');
        // } 
        else if (tipo == 3){
            var id_sede = $('[name=id_sede]').val();
            var id_cliente = $('[name=id_cliente]').val();
            console.log('id_sede:'+id_sede+' id_cliente:'+id_cliente);

            if (id_sede !== '' && id_cliente !== ''){
                listar_doc_ven(id_sede, id_cliente);
            }
            // $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>');
            // $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    // } else {
    //     alert('Debe seleccionar una Guía!');
    }
}
function agrega_sustento(){
    console.log('agregar_sustento');
    var tipo = $('[name=tipo]').val();
    console.log('sustento:'+tipo);

    // if (tipo == 1){
        $('#modal-guia_detalle_ing').modal({
            show: true
        });
        listar_detalle_doc(tipo);
    // } 
    // else if(tipo == 2) {

    // }
    // else if(tipo == 3) {

    // }
}
function listar_guias_almacen(id_almacen){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guias_almacen/'+id_almacen,
        dataType: 'JSON',
        success: function(response){
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_guia+'"> GR-'+response[i].serie+'-'+response[i].numero+' '+response[i].razon_social+'</option>';
            }
            $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_req(id_sede){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_req/'+id_sede,
        dataType: 'JSON',
        success: function(response){
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_requerimiento+'">'+response[i].codigo+' - '+response[i].concepto+'</option>';
            }
            $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_doc_ven(id_sede, id_cliente){
    $.ajax({
        type: 'GET',
        url: 'listar_doc_ven/'+id_sede+'/'+id_cliente,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_doc_ven+'">'+response[i].abreviatura+'-'+response[i].serie+'-'+response[i].numero+' - '+response[i].razon_social+' - '+response[i].fecha_emision+'</option>';
            }
            $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_detalle_doc(tipo){
    var id = $('[name=docs_sustento]').val();
    var id_almacen = $('[name=id_almacen]').val();
    console.log('listar_detalle_doc/'+id+'/'+tipo+'/'+id_almacen);
    //Guia de Compra 
    if (id !== null){
        console.log(id);
        console.log(tipo);
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'listar_detalle_doc/'+id+'/'+tipo+'/'+id_almacen,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('#listaDetalleIng tbody').html(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }); 
    } else {
        alert('Debe seleccionar un Documento!');
    }
}
function guardar_detalle_ing(){
    var tipo = [];
    var id = [];
    var r = 0;

    $("input[type=checkbox]:checked").each(function(){
        var o = $(this).closest('td').siblings().find("input[name=id]").val();
        var co = o.split("-");
        tipo[r] = co[0];
        id[r] = co[1];
        console.log(co[0]);
        ++r;
    });
    
    if (r == 0){
        alert('Debe seleccionar por lo menos un item');
    } else {
        // var token = $('#token').val();
        var id_guia_ven = $("[name=id_guia_ven]").val();
        var id_almacen = $("[name=id_almacen]").val();
        
        var data =  'id_guia_ven='+id_guia_ven+
                    '&id_almacen='+id_almacen+
                    '&tipo='+tipo+
                    '&id='+id;
        console.log(data);
        $.ajax({
            type: 'POST',
            url: 'guardar_detalle_ing',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Detalle registrado con éxito');
                    $('#listaDetalle tbody tr').remove();
                    listar_detalle(id_guia_ven);
                    $('#modal-guia_detalle_ing').modal('hide');
                    $('[name=docs_sustento]').val('0').trigger('change.select2');
                    changeStateButton('guardar');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
