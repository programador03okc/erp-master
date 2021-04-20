function open_series(id_producto,id_od_detalle,cantidad){
    $('#modal-guia_ven_series').modal({
        show: true
    });
    listarSeries(id_producto);
    $('[name=id_od_detalle]').val(id_od_detalle);
    $('[name=id_producto]').val(id_producto);
    $('[name=cant_items]').val(cantidad);
    $('[name=seleccionar_todos]').prop('checked', false);;
}

function listarSeries(id_producto){
    $.ajax({
        type: 'GET',
        url: 'listar_series_guia_ven/'+id_producto,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var tr = '';
            var i = 1;
            response.forEach(element => {
                tr+=`<tr>
                <td><input type="checkbox" name="sel" data-serie="${element.serie}" data-id="${element.id_prod_serie}"/></td>
                <td class="numero">${i}</td>
                <td class="serie">${element.serie}</td>
                <td>${element.guia_com}</td>
                </tr>`;
                i++;
            });
            $('#listaSeries tbody').html(tr);
            // $('[name=serie_prod]').focus();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_series(){

    var serie = null;
    var id_prod_serie = null;
    var json_series = [];

    $("#listaSeries input[type=checkbox]:checked").each(function(){
        serie = $(this).data('serie');
        id_prod_serie = $(this).data('id');
        console.log(serie);
        json_series.push({'serie':serie, 'id_prod_serie':id_prod_serie});
    });

    var id_od_detalle = $('[name=id_od_detalle]').val();
    var cant = $('[name=cant_items]').val();

    console.log('cant'+parseInt(cant)+' length'+json_series.length);
    var rspta = false;
    
    if (json_series.length == 0){
        rspta = confirm('¿Está seguro que desea quitar las series?');
    }
    else if (parseInt(cant) == json_series.length){
        rspta = true;
    }
    else if (parseInt(cant) > json_series.length){
        alert('La cantidad del item es de '+cant+', aún le falta seleccionar '+(parseInt(cant) - json_series.length)+' serie(s).');
    }
    else if (parseInt(cant) < json_series.length){
        alert('La cantidad del item es de '+cant+', ud. ha seleccionado '+(json_series.length - parseInt(cant))+' serie(s) de más.');
    }

    if (rspta && id_od_detalle !== ''){
            
        var json = detalle.find(element => element.id_od_detalle == id_od_detalle);
        
        if (json !== null){
            json.series = json_series;
        }
        console.log(json);
        console.log(detalle);
        mostrar_detalle();
        $('#modal-guia_ven_series').modal('hide');
    }
}

$("[name=seleccionar_todos]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        $("#listaSeries tbody tr").each(function(){
            $(this).find("td input[type=checkbox]").prop('checked', true);
        });
    } else {
        $("#listaSeries tbody tr").each(function(){
            $(this).find("td input[type=checkbox]").prop('checked', false);
        });
    }
});

// function handleKeyPress(event){
//     var exeptuados = ['/','"',"'",'*','+','#','$','%','&','(',')','=','?','¿','¡','!','.','¨','^','´','`','_',',',';','>','<','|','°','¬']
//     if (event.which == 13) {
//         buscar_serie();
//     } else if (exeptuados.includes(event.key)){
//         event.returnValue = false;
//         alert('Valor No Permitido: '+event.key);
//     }
// }

// function buscar_serie(){
//     var serie = $('[name=serie_prod]').val();
//     $.ajax({
//         type: 'GET',
//         url: 'buscar_serie/'+serie,
//         dataType: 'JSON',
//         success: function(response){
//             console.log('response'+response);
//             // if (!jQuery.isEmptyObject(response)){
//             if (Object.entries(response).length === 0){
//                 alert('No existe dicha Serie');
//             } else {
//                 if (response.id_guia_ven_det == null){
//                     if (!exist(response.serie)){
//                         agregar_serie(response.id_prod_serie, response.serie, response.guia_com);
//                     } else {
//                         alert('Dicha serie ya fue ingresada!');
//                     }
//                 } else {
//                     alert('La serie ya fue asignada a otra Guía:'+response.guia_ven);
//                 }
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

// function agregar_serie(id_prod_serie,serie,guia_com){
//     if (serie !== '') {
//         var items = $('[name=cant_items]').val();
//         var cant = $('#listaSeries tbody tr').length + 1;
//         console.log('cant:'+cant+' items:'+items);
//         if (cant <= items){
//             var td = '<tr id="'+id_prod_serie+'"><td class="numero">'+cant+'</td><td class="serie">'+serie+'</td><td>'+guia_com+'</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('+"'"+id_prod_serie+"'"+');"></i></td></tr>';
//             $('#listaSeries tbody').append(td);
//             $('[name=serie_prod]').val('');
//             $('[name=serie_prod]').focus();
//         } else {
//             alert('Ha superado la cantidad del producto!\nYa no puede agregar mas series.');
//         }
//     } else {
//         alert('El campo serie esta vacío!');
//     }
// }

// function eliminar_serie(id_prod_serie){
//     var elimina = confirm("¿Esta seguro que desea eliminar ésta serie?");
//     if (elimina){
//         var id = $("#"+id_prod_serie)[0].firstChild.innerHTML;
//         console.log('id:'+id);
//         console.log('id_prod_serie'+id_prod_serie);

//         var a = $('[name=anulados]').val();
//         if (a == ''){
//             a += id_prod_serie;
//         } else {
//             a += ','+id_prod_serie;
//         }
//         $('[name=anulados]').val(a);
    
//         $("#"+id_prod_serie).remove();

//         var i = 1;
//         $(".numero").each(function(){
//             console.log('dentro');
//             console.log($(this).html());
//             $(this).html(i);
//             i++;
//         });
//     }
// }

// function exist(serie){
//     var exist = false;
//     $(".serie").each(function(){
//         exist = (serie == $(this).html());
//     });
//     return exist;
// }

// function guardar_series(){
//     var ids = [];
//     $("#listaSeries tbody tr").each(function(){
//         console.log($(this)[0].id);
//         ids.push($(this)[0].id);
//     });
    
//     var id_guia_ven_det = $("[name=id_guia_ven_det]").val();
//     var anulados = $('[name=anulados]').val();
//     var data =  'id_guia_ven_det='+id_guia_ven_det+
//                 '&ids='+ids+
//                 '&anulados='+anulados;
//     console.log(data);
//     $.ajax({
//         type: 'POST',
//         url: 'update_series',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log('response:'+response);
//             if (response > 0){
//                 alert('Series registradas con éxito');
//                 $('#modal-guia_ven_series').modal('hide');
//                 var id_guia_ven = $("[name=id_guia_ven]").val();
//                 listar_detalle(id_guia_ven);
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
