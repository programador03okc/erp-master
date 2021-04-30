$(function(){
//     $('[name=fecha]').val(fecha_actual());
    // tipo_cambio();
    vista_extendida();
    listarSaldos();
});

function mostrarSaldos(){
    var almacen = $('[name=almacen]').val();
    if (almacen !== null && almacen !== ''){
        if (almacen == 0){
            var url = 'listar_saldos_todo';
        } else {
            var url = 'listar_saldos/'+almacen;
        }
        listarSaldos(url);
    }
}

function listarSaldos(url){

    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy':true,
        'ajax': url,
        // 'ajax': {
            //     url:'listar_saldos/'+almacen,
            //     dataSrc:''
            // },
        // "scrollX": true,
        'columns': [
            {'data': 'id_prod_ubi'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'stock', 'class': 'right'},
            // {'data': 'cantidad_reserva', 'class': 'right'},
            {'render':
                function (data, type, row){
                    if(row['cantidad_reserva'] !== null){
                        return '<h5 style="margin-top: 0px;margin-bottom: 0px;"><span class="ver label label-danger" data-id="'+row['id_producto']+'" data-almacen="'+row['id_almacen']+'" >'+row['cantidad_reserva']+' </span></h5>';
                        // return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" '+
                        // 'data-placement="bottom" title="Ver Requerimientos" '+
                        // '<i class="fas fa-list-ul"></i></button>';
                    } else {
                        return '';
                    }
                }
            },
            {'data': 'almacen_descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        "order": [[3, "asc"]]
    });
}

$('#listaSaldos tbody').on("click","span.ver", function(){
    // var data = $('#listaSaldos').DataTable().row(this).data();
    // console.log(data);
    let id = $(this).data('id');
    let almacen = $(this).data('almacen');
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos que generan la Reserva');
    console.log(id+','+ almacen);
    verRequerimientosReservados(id, almacen);
});

function openReservados(id_producto, id_almacen){
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos que generan la Reserva');
    console.log(id_producto+','+ id_almacen);
    verRequerimientosReservados(id_producto, id_almacen);
}

function verRequerimientosReservados(id_producto,id_almacen){
    let baseUrl = 'verRequerimientosReservados/'+id_producto+'/'+id_almacen;
    console.log(baseUrl);
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_requerimiento+'">'+
                '<td><label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+element.id_requerimiento+')">'+element.codigo+'</label></td>'+
                '<td>'+element.concepto+'</td>'+
                '<td>'+element.almacen_descripcion+'</td>'+
                '<td>'+(element.stock_comprometido!==null?element.stock_comprometido:element.cantidad)+'</td>'+
                '<td>'+element.nombre_corto+'</td>'+
                '</tr>';
                i++;
            });
            $('#listaRequerimientosEstado tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function tipo_cambio(){
//     $.ajax({
//         type: 'GET',
//         url: 'tipo_cambio_compra/'+fecha_actual(),
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             $('[name=tipo_cambio]').val(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}