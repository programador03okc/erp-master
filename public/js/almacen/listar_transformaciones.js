$(function(){
    // clearDataTable();
    $('[name=id_almacen]').val(1).trigger('change.select2');
    listarTransformaciones();
});
function listarTransformaciones(){
    var almacen = $('[name=id_almacen]').val();
    var vardataTables = funcDatatables();
    var tabla = $('#listaTransformaciones').DataTable({
        'destroy':true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax' : 'listar_todas_transformaciones/'+almacen,
        // 'ajax': {
        //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
        //     dataSrc:''
        // },
        'columns': [
            {'data': 'id_transformacion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_transformacion']));
                }
            },
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ('<label class="lbl-codigo" title="Abrir Transformación" onClick="abrir_transformacion('+row['id_transformacion']+')">'+row['serie']+'-'+row['numero']+'</label>');
                }
            },
            {'data': 'razon_social'},
            {'data': 'descripcion'},
            {'data': 'nombre_responsable'},
            {'data': 'nombre_registrado'},
            {'render':
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'defaultContent': 
                '<button type="button" class="ver btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Ingreso" >'+
                    '<i class="fas fa-search-plus"></i></button>'+
                '<button type="button" class="atender btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Atender" >'+
                    '<i class="fas fa-share"></i></button>'+
                '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Anular" >'+
                    '<i class="fas fa-trash"></i></button>'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    ver("#listaTransformaciones tbody", tabla);
    atender("#listaTransformaciones tbody", tabla);
    anular("#listaTransformaciones tbody", tabla);
    vista_extendida();
}
function ver(tbody, tabla){
    console.log("ver");
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined && data.id_guia_com !== null){
        //     abrir_ingreso(data.id_guia_com);
        // }
    });
}
function atender(tbody, tabla){
    console.log("atender");
    $(tbody).on("click","button.atender", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined){
        //     open_transferencia_detalle(data);
        // }
    });
}
function anular(tbody, tabla){
    console.log("anular");
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined){
        //     if (data.guia_com == '-'){
        //         $.ajax({
        //             type: 'GET',
        //             url: 'anular_transferencia/'+data.id_transferencia,
        //             dataType: 'JSON',
        //             success: function(response){
        //                 if (response > 0){
        //                     alert('Transferencia anulada con éxito');
        //                 }
        //             }
        //         }).fail( function( jqXHR, textStatus, errorThrown ){
        //             console.log(jqXHR);
        //             console.log(textStatus);
        //             console.log(errorThrown);
        //         });
        //     } else {
        //         alert('No se puede anular por que ya tiene Ingreso a Almacén.');
        //     }
        // }
    });
}
function abrir_transformacion(id_transformacion){
    console.log('abrir_transformacion()');
    localStorage.setItem("id_transformacion",id_transformacion);
    location.assign("transformacion");
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}