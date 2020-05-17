$(function(){
    listar_sol_todas();
    $('#listaSolTodas tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSolTodas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var trab = $(this)[0].childNodes[3].innerHTML;
        var area = $(this)[0].childNodes[4].innerHTML;
        var fini = $(this)[0].childNodes[7].innerHTML;
        var ffin = $(this)[0].childNodes[8].innerHTML;
        $('[name=id_solicitud]').val(id);
        $('[name=trabajador]').val(trab);
        $('[name=area_solicitud]').val(area);
        $('[name=fecha_inicio]').val(fini);
        $('[name=fecha_fin]').val(ffin);
        console.log('id'+id);
    });    
});
function listar_sol_todas(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaSolTodas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        ajax:{
            url:"listar_aprob_sol",
            dataSrc:""
        },
        'columns': [
            {'data': 'id_solicitud'},
            // {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ('<label class="lbl-codigo" title="Abrir Solicitud" onClick="abrir_solicitud('+row['id_solicitud']+')">'+row['codigo']+'</label>');
                }
            },
            {'data': 'fecha_solicitud'},
            {'data': 'nombre_trabajador'},
            {'data': 'area_descripcion'},
            {'data': 'observaciones'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
            {'data': 'des_categoria'},
            {'data': 'asignaciones_pendientes'},
            // {'render':
            //     function (data, type, row){
            //         return (formatDate(row['fecha_fin']));
            //     }
            // },
            {'render':
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'render': 
                function (data, type, row){
                    var html = '<button type="button" class="flujos btn btn-info btn-sm btn-log" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver">'+
                        '<i class="fas fa-search-plus"></i></button>'+
                    '<button type="button" class="ver btn bg-maroon btn-flat margin btn-sm" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver pdf" style="margin:0;">'+
                        '<i class="fas fa-file-pdf"></i></button>'+
                    '<button type="submit" class="excel btn bg-purple btn-flat margin btn-sm" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Descargar Excel" style="margin:0;">'+
                        '<i class="fas fa-file-excel"></i></button>';

                    if (row['aprueba'] == 'true'){
                        html+= '<button type="button" class="denegar btn btn-danger btn-sm btn-log" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Denegar" >'+
                            '<i class="fas fa-trash"></i></button>'+
                        '<button type="button" class="aprobar btn btn-success btn-sm btn-log" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Aprobar" >'+
                            '<i class="fas fa-check fa-xs"></i></button>'+
                        '<button type="button" class="observar btn btn-warning btn-sm btn-log" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Observar" >'+
                            '<i class="fas fa-exclamation-triangle fa-xs"></i></button>';
                    }
                    if (row['asigna'] == 'true'){
                        html+= '<button type="button" class="asignar btn btn-success btn-sm btn-log" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Asignar" >'+
                        '<i class="fas fa-share"></i></button>';
                    }
                    return html;
                }
            }
        ],
        "order": [[ 2, "desc" ]],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaSolTodas tbody',tabla);
    vista_extendida();
}
function botones(tbody, tabla){
    $(tbody).on("click","button.aprobar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        var rspta = confirm('Esta seguro que desea aprobar la solicitud '+data.codigo);
        
        if (rspta){
            var obs = prompt("Si desea ingrese alguna observación", "Ok");
            console.log(obs);
            var id_sol = data.id_solicitud;
            guardar_aprobacion(data,1,obs);
            var aprobado = 2;
            actualiza_estado(id_sol,aprobado);
        }
    });
    $(tbody).on("click","button.denegar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        var rspta = confirm('Esta seguro que desea denegar la solicitud '+data.codigo);
        
        if (rspta){
            var obs = prompt("Ingrese su motivo", "");
            if (obs.trim().length > 0){
                var id_sol = data.id_solicitud;
                guardar_aprobacion(data,2,obs);
                var denegado = 4;
                actualiza_estado(id_sol,denegado);
            }
            else {
                alert('Es necesario que ingrese un motivo!');
            }
        }
    });
    $(tbody).on("click","button.observar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        var rspta = confirm('Esta seguro que desea observar la solicitud '+data.codigo);
        
        if (rspta){
            var obs = prompt("Ingrese su observación", "");
            if (obs.trim().length > 0){
                var id_sol = data.id_solicitud;
                guardar_aprobacion(data,3,obs);
                var observado = 3;
                actualiza_estado(id_sol,observado);
            } else {
                alert('Es necesario que ingrese una observación!');
            }
        }
    });
    $(tbody).on("click","button.flujos", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        open_flujos(data);
    });
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        open_ver(data.id_solicitud);
    });
    $(tbody).on("click","button.excel", function(){
        var data = tabla.row($(this).parents("tr")).data();
        if (data.id_solicitud !== ''){
            console.log(data);
            open_fechas(data);
        } else {
            alert('Debe seleccionar una Asignación de Equipo.');
        }
    });
    $(tbody).on("click","button.asignar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        $('[name=id_solicitud]').val(data.id_solicitud);
        $('[name=area_solicitud]').val(data.area_solicitud);
        $('[name=trabajador]').val(data.trabajador);
        $('[name=fecha_inicio]').val(data.fecha_inicio);
        $('[name=fecha_fin]').val(data.fecha_fin);
        console.log(data);
        $('#modal-asignacion_equipos').modal({
            show:true
        });
        listar_equipos(data.id_categoria);
        
        // asignacionModal(data, id_sol, area, trab, fini, ffin);
    });
}
function guardar_aprobacion(solicitud,vobo,obs){
    var id_flujo = solicitud.id_flujo;
    var id_doc_aprob = solicitud.id_doc_aprob;

    var data = 'id_flujo='+id_flujo+
            '&id_doc_aprob='+id_doc_aprob+
            '&id_vobo='+vobo+
            '&id_usuario='+auth_user.id_usuario+
            '&id_area='+auth_user.id_area+
            '&detalle_observacion='+obs+
            '&id_rol='+auth_user.id_rol;
    console.log(data);

    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_aprobacion',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Aprobación registrada con éxito');
                // $('#modal-equipo_create').modal('hide');
                // listar_sol_todas();
                $('#listaSolTodas').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function actualiza_estado(id_solicitud,estado){
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'solicitud_cambia_estado/'+id_solicitud+'/'+estado,
        // data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function open_flujos(data){
    $('#modal-aprob_flujos').modal({
        show: true
    });

    $('#codigo').text(data.codigo);
    $('#fecha_solicitud').text(data.fecha_solicitud);
    $('#fecha_inicio').text(data.fecha_inicio);
    $('#fecha_fin').text(data.fecha_fin);
    $('#nombre_trabajador').text(data.nombre_trabajador);
    $('#nombre_empresa').text(data.nombre_empresa);
    $('#area_descripcion').text(data.area_descripcion);
    $('#observaciones').text(data.observaciones);
    $('#des_categoria').text(data.des_categoria);
    $('#cantidad').text(data.cantidad);

    listar_flujos(data.id_doc_aprob,data.id_solicitud);
}
function listar_flujos(id_doc_aprob,id_solicitud){
    $('#listaSolFlujos tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'solicitud_flujos/'+id_doc_aprob+'/'+id_solicitud,
        dataType: 'JSON',
        success: function(response){
            $('#listaSolFlujos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function open_ver(id_solicitud){
    if (id_solicitud !== ''){
        var id = encode5t(id_solicitud);
        window.open('imprimir_solicitud/'+id);
    } else {
        alert('Debe seleccionar una solicitud.');
    }
}
function open_fechas(data){
    $('#modal-fechas').modal({
        show: true
    });
    $('[name=f_fecha_inicio]').val(data.fecha_inicio);
    $('[name=f_fecha_fin]').val(data.fecha_fin);
}
function enviar_fechas(){
    var id_solicitud = $('[name=id_solicitud]').val();
    var fini = $('[name=f_fecha_inicio]').val();
    var ffin = $('[name=f_fecha_fin]').val();
    var id = encode5t(id_solicitud);
    window.open('download_control_bitacora/'+id+'/'+fini+'/'+ffin);
    $('#modal-fechas').modal('hide');
}
function abrir_solicitud(id_solicitud){
    console.log('abrir_solicitud()');
    localStorage.setItem("id_solicitud",id_solicitud);
    location.assign("equi_sol");
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}