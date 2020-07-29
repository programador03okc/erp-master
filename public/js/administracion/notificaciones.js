
function inicializar(){
    listaNotificacionesNoLeidas();
    listaNotificacionesLeidas();
}
function listaNotificacionesNoLeidas(){
    var vardataTables = funcDatatables();
    $('#listaNotificacionesNoLeidas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar-notificaciones-no-leidas',
        'columns': [
            {'render':
                function (data, type, row,meta){
                    return meta.row +1;
                }
            },           
            {'data': 'mensaje'},
            {'data': 'fecha'},
            {'render':
                function (data, type, row){
                    htmlAction = '<center>' +
                    '<div class="btn-group" role="group" style="margin-bottom: 5px; width:200px;">' +
                    '<button type="button" class="btn btn-sm btn-default" title="Marcar como Leída" onclick="marcarLeido('+row.id+');"><i class="fas fa-archive fa-xs"></i></button> ' +
                    '</div>' +
                    '</center>';
                    return htmlAction;
                }
            }
        ],
        // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listaNotificacionesLeidas(){
    var vardataTables = funcDatatables();
    $('#listaNotificacionesLeidas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar-notificaciones-leidas',
        'columns': [
            {'render':
                function (data, type, row,meta){
                    return meta.row +1;
                }
            },           
            {'data': 'mensaje'},
            {'data': 'fecha'},
            {'render':
                function (data, type, row){
                    htmlAction = '<center>' +
                    '<div class="btn-group" role="group" style="margin-bottom: 5px; width:200px;">' +
                    '<button type="button" class="btn btn-sm btn-default" title="Archivar" onclick="marcarNoLeido('+row.id+');"><i class="fas fa-inbox"></i></button> ' +
                    '</div>' +
                    '</center>';
                    return htmlAction;
                }
            }
        ],
        // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function marcarLeido(id){
    if(id > 0){
        $.ajax({
            type: 'PUT',
            url: 'marcar-notificacion-leida/'+id,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if(response.status ==200){
                    $('#listaNotificacionesNoLeidas').DataTable().ajax.reload();
                    $('#listaNotificacionesLeidas').DataTable().ajax.reload();
                    get_notificaciones_sin_leer_interval();

                }else{
                    alert("opps ocurrio un problema, no se pudo marcar como leido.");
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function marcarNoLeido(id){
    if(id > 0){
        $.ajax({
            type: 'PUT',
            url: 'marcar-notificacion-no-leida/'+id,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if(response.status ==200){
                    $('#listaNotificacionesLeidas').DataTable().ajax.reload();
                    $('#listaNotificacionesNoLeidas').DataTable().ajax.reload();
                    get_notificaciones_sin_leer_interval();
                }else{
                    alert("opps ocurrio un problema, no se pudo enviar la notificacion a la badeja de entrada.");
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}