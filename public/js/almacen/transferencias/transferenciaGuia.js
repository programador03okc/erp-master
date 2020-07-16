let origen = null;

function openTransferenciaGuia(data){
    origen = 'transferencia_por_orden';
    $('#modal-transferenciaGuia').modal({
        show: true
    });
    
    $('[name=id_almacen_origen]').val(data.id_almacen);
    $('[name=trans_serie]').val('');
    $('[name=trans_numero]').val('');
    $('[name=id_guia_com]').val(data.id_guia_com);
    $('[name=id_sede]').val(data.sede_orden);
    $('[name=id_mov_alm]').val(data.id_mov_alm);
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $("#submit_transferencia").removeAttr("disabled");
    if (data.almacen_requerimiento !== null){
        $('[name=id_almacen_destino]').val(data.almacen_requerimiento);
    } else {
        cargar_almacenes(data.sede_requerimiento, 'id_almacen_destino');
    }
    // var tp_doc_almacen = 2;//guia venta
    // next_serie_numero(data.sede_orden,tp_doc_almacen);
}

function openGenerarGuia(data){
    origen = 'transferencia_por_requerimiento';
    $('#modal-transferenciaGuia').modal({
        show: true
    });
    $('[name=id_almacen_origen]').val(data.id_almacen_origen);
    $('[name=trans_serie]').val('');
    $('[name=trans_numero]').val('');
    $('[name=id_guia_com]').val('');
    $('[name=id_sede]').val(data.id_sede_almacen);
    $('[name=id_mov_alm]').val('');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=id_transferencia]').val(data.id_transferencia);
    $("#submit_transferencia").removeAttr("disabled");
    
    cargar_almacenes(data.id_sede, 'id_almacen_destino');
    if (data.id_almacen_destino !== null){
        $('[name=id_almacen_destino]').val(data.id_almacen_destino);
    }
    // var tp_doc_almacen = 2;//guia venta
    // next_serie_numero(data.sede_orden,tp_doc_almacen);
}

function cargar_almacenes(sede, campo){
    if (sede !== ''){
        $.ajax({
            type: 'GET',
            url: 'cargar_almacenes/'+sede,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var option = '';
                for (var i=0; i<response.length; i++){
                    if (response.length == 1){
                        option+='<option value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    } else {
                        option+='<option value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    }
                }
                $('[name='+campo+']').html(option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function next_serie_numero(id_sede,id_tp_doc){
    if (id_sede !== null && id_tp_doc !== null){
        $.ajax({
            type: 'GET',
            url: 'next_serie_numero_guia/'+id_sede+'/'+id_tp_doc,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response !== ''){
                    $('[name=serie]').val(response.serie);
                    $('[name=numero]').val(response.numero);
                    $('[name=id_serie_numero]').val(response.id_serie_numero);
                } else {
                    $('[name=serie]').val('');
                    $('[name=numero]').val('');
                    $('[name=id_serie_numero]').val('');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

$("#form-transferenciaGuia").on("submit", function(e){
    console.log('submit_transferencia');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);

    if (origen == 'transferencia_por_orden'){
        guardar_transferencia(data);
    } 
    else if (origen == 'transferencia_por_requerimiento'){
        update_transferencia(data);
    }
});

function guardar_transferencia(data){
    var msj = validaCampos();
    if (msj.length > 0){
        alert(msj);
    } else {
        $("#submit_transferencia").attr('disabled','true');
        $.ajax({
            type: 'POST',
            url: 'guardar_guia_transferencia',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Salida Almacén generada con éxito');
                    $('#modal-transferenciaGuia').modal('hide');
                    $('#ordenesEntregadas').DataTable().ajax.reload();
                    // var id = encode5t(response);
                    // window.open('imprimir_salida/'+id);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function update_transferencia(data){
    var msj = validaCampos();
    if (msj.length > 0){
        alert(msj);
    } else {
        $("#submit_transferencia").attr('disabled','true');
        $.ajax({
            type: 'POST',
            url: 'update_guia_transferencia',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Salida Almacén generada con éxito');
                    $('#modal-transferenciaGuia').modal('hide');
                    $('#listaTransferenciasPorEnviar').DataTable().ajax.reload();
                    // var id = encode5t(response);
                    // window.open('imprimir_salida/'+id);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function validaCampos(){
    var serie = $('[name=trans_serie]').val();
    var numero = $('[name=trans_numero]').val();
    var alm_ori = $('[name=id_almacen_origen]').val();
    var alm_des = $('[name=id_almacen_destino]').val();
    var resp = $('[name=responsable_destino_trans]').val();
    var text = '';

    if (serie == '' || serie == '0000'){
        text +='Es necesario que ingrese una Serie\n';
    }
    if (numero == '' || serie == '0000000'){
        text +='Es necesario que ingrese un Número\n';
    }
    if (alm_ori == '' || alm_ori == '0'){
        text +='Es necesario que ingrese un Almacén Origen\n';
    }
    if (alm_des == '' || alm_des == '0'){
        text +='Es necesario que ingrese un Almacén Destino\n';
    }
    if (resp == '' || resp == '0'){
        text +='Es necesario que ingrese un Responsable Destino\n';
    }
    return text;
}

function ceros_numero_trans(numero){
    if (numero == 'numero'){
        var num = $('[name=trans_numero]').val();
        $('[name=trans_numero]').val(leftZero(7,num));
    }
    else if(numero == 'serie'){
        var num = $('[name=trans_serie]').val();
        $('[name=trans_serie]').val(leftZero(4,num));
    }
}
