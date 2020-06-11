$(function(){
    // clearDataTable();
    $('[name=id_almacen_ori]').val(1);
    listarTransferenciasPendientes();
});
function listarTransferenciasPendientes(){
    var alm_origen = $('[name=id_almacen_ori]').val();
    // var alm_destino = $('[name=id_almacen_des]').val();
    console.log('ori'+alm_origen);
    
    if (alm_origen !== '' && alm_origen !== ''){
        var vardataTables = funcDatatables();
        var tabla = $('#listaTransferenciasPendientes').DataTable({
            'destroy':true,
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'ajax' : 'listar_transferencias_pendientes/'+alm_origen,
            // 'ajax': {
            //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
            //     dataSrc:''
            // },
            'columns': [
                {'data': 'id_transferencia'},
                // {'data': 'codigo_transferencia'},
                {'render':
                    function (data, type, row){
                        return (formatDate(row['fecha_transferencia']));
                    }
                },
                {'data': 'codigo'},
                {'render':
                    function (data, type, row){
                        return ('<label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_venta('+row['id_guia_ven']+')">'+row['guia_ven']+'</label>');
                    }
                },
                {'render':
                    function (data, type, row){
                        return ('<label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_compra('+row['id_guia_com']+')">'+row['guia_com']+'</label>');
                    }
                },
                // {'data': 'guia'},
                {'data': 'fecha_guia'},
                {'data': 'alm_origen_descripcion'},
                {'data': 'alm_destino_descripcion'},
                {'data': 'nombre_origen'},
                {'data': 'nombre_destino'},
                {'data': 'nombre_registro'},
                // {'data': 'estado_doc'},
                {'render':
                    function (data, type, row){
                        return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                    }
                },
                {'defaultContent': 
                    '<button type="button" class="atender btn btn-success boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Atender" >'+
                        '<i class="fas fa-share"></i></button>'+
                    '<button type="button" class="ver btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Ingreso" >'+
                        '<i class="fas fa-search-plus"></i></button>'+
                    '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Anular" >'+
                        '<i class="fas fa-trash"></i></button>'},
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }
    ver("#listaTransferenciasPendientes tbody", tabla);
    atender("#listaTransferenciasPendientes tbody", tabla);
    anular("#listaTransferenciasPendientes tbody", tabla);
    vista_extendida();
}
function ver(tbody, tabla){
    console.log("ver");
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        if (data !== undefined && data.id_guia_com !== null){
            abrir_ingreso(data.id_guia_com);
        }
    });
}
function atender(tbody, tabla){
    console.log("atender");
    $(tbody).on("click","button.atender", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        if (data !== undefined){
            open_transferencia_detalle(data);
        }
    });
}
function anular(tbody, tabla){
    console.log("anular");
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        if (data !== undefined){
            if (data.guia_com == '-'){
                $.ajax({
                    type: 'GET',
                    url: 'anular_transferencia/'+data.id_transferencia,
                    dataType: 'JSON',
                    success: function(response){
                        if (response > 0){
                            alert('Transferencia anulada con éxito');
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            } else {
                alert('No se puede anular por que ya tiene Ingreso a Almacén.');
            }
        }
    });
}
function abrir_guia_venta(id_guia_venta){
    console.log('abrir_guia_venta()');
    localStorage.setItem("id_guia_ven",id_guia_venta);
    location.assign("guia_venta");
}
function abrir_guia_compra(id_guia_compra){
    console.log('abrir_guia_compra()');
    localStorage.setItem("id_guia_com",id_guia_compra);
    location.assign("guia_compra");
}
function abrir_ingreso(id_guia_com){
    $.ajax({
        type: 'GET',
        url: 'ingreso_transferencia/'+id_guia_com,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response[0]['id_mov_alm'] > 0){
                console.log(response[0]['id_mov_alm']);
                var id = encode5t(response[0]['id_mov_alm']);
                window.open('imprimir_ingreso/'+id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });    
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}