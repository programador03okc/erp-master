$(function(){
    resizeSide();
});

function crearReporteCumple(){
    resizeSide();
    var vardataTables = funcDatatables();
    var filtro = $('#filtro').val();

    if (filtro > 0){
        baseUrl = 'buscar_cumple/' + filtro;
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseUrl,
            dataType: 'JSON',
            success: function(response){
                thead = '<thead><tr><th width="60">DNI</th><th width="250">Datos personales</th><th>Empresa</th><th>Sede</th><th>Cargo</th><th width="90">Onomástico</th></tr></thead>';
                dataSet = response.result;
                columns = [
                    {'data': 'nro_documento'},
                    {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                    {'data': 'empresa'},
                    {'data': 'sede'},
                    {'data': 'cargo'},
                    {'data': 'onomastico'}
                ];

                $('#my-cumple-table').html(thead);
                $('#my-cumple-table').dataTable({
                    'dom': 'rtip',
                    'language' : vardataTables[0],
                    "pageLength": 15,
                    'processing': true,
                    'bDestroy': true,
                    'data': dataSet,
                    'columns': columns
                });
            }
        });
    }
}

function crearReporte(){
    resizeSide();
    var vardataTables = funcDatatables();
    var filtro = $('#filtro').val();
    var desc = $('#descripcion').val();

    if (filtro > 0){
        if (desc.length > 0){
            baseUrl = 'buscar_postulantes/' + filtro + '/' + desc;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    var thead = '';
                    var dataSet = '';
                    var columns = [];
                    if (filtro == 1){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Profesion</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'carrera'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }else if(filtro == 2){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Nivel de estudios</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'nivel_estudio'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }else if(filtro == 3){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Institución</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'institucion'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }else if(filtro == 4){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Provinvia</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'provincia'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }else if(filtro == 5){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Distrito</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'distrito'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }else if(filtro == 6){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Cargo ocupado</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'cargo_ocupado'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }else if(filtro == 7){
                        thead = '<thead><tr><th>DNI</th><th width="180">Datos personales</th><th>Telefono</th><th>Ubigeo</th><th>Correo</th><th>Funciones</th><th>-</th></tr></thead>';
                        dataSet = response;
                        columns = [
                            {'data': 'nro_documento'},
                            {'data': function (data){return (data['nombres'] + ' ' + data['apellido_paterno'] + ' ' + data['apellido_materno']);}},
                            {'data': 'telefono'},
                            {'data': 'ubigeo'},
                            {'data': 'correo'},
                            {'data': 'funciones'},
                            {'render': function (data, type, row, meta){return ('<button type="button" class="btn btn-sm bg-red btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Ver detalles" onclick="seeDetailReport('+row['id_postulante']+');"><i class="fas fa-eye"></i></button></div>');}}
                        ];
                    }
                    $('.dataTable').dataTable().fnDestroy();
                    console.log(response);

                    $('#my-report-table').html(thead);
                    $('#my-report-table').dataTable({
                        'dom': 'rtip',
                        'language' : vardataTables[0],
                        "pageLength": 15,
                        'data': dataSet,
                        'columns': columns
                    });
                }
            }).fail( function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert('Debe ingresar la descripción');
        }
    }else{
        alert('Debe seleccionar un valor del filtro');
    }
}

function crearReporteGrupoTrab(){
    var empresa = $('#id_empresa').val();
    var grupo = $('#id_grupo').val();

    if (grupo > 0){
        window.open('buscar_grupo_trabajador/'+empresa+'/'+grupo);
    }else{
        alert('Debe seleccionar un Grupo');
    }
}

function seeDetailReport(id){
    baseUrl = 'cargar_detalle_postulante/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#info-detail').html(response);
            $('#modal-informacion-reporte').modal({show: true, backdrop: 'static'});
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cambiarEmpresa(value){
    baseUrl = 'mostrar_combos_emp/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var sedes = response.sedes;
            var htmls = '<option value="0" selected disabled>Elija una opción</option>';
            Object.keys(sedes).forEach(function (key){
                htmls += '<option value="'+sedes[key]['id_sede']+'">'+sedes[key]['descripcion']+'</option>';
            })
            $('#id_sede').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function cambiarSede(value){
    $('#id_grupo').empty();
    baseUrl = 'mostrar_grupo_sede/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var htmls = '<option value="0" selected disabled>Elija una opción</option>';
            $('#id_grupo').append(htmls);
            Object.keys(response).forEach(function(key){
                var opt = '<option value="'+response[key].id_grupo+'">'+response[key].descripcion+'</option>';
                $('#id_grupo').append(opt);
            });
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function crearReporteDatosGrl(){
    resizeSide();
    var vardataTables = funcDatatables();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'buscar_datos_generales/'+1,
        dataType: 'JSON',
        success: function(response){
            thead =
            '<thead>'+
            '<tr>'+
                '<th>Planilla</th>'+
                '<th width="80">Sede</th>'+
                '<th width="60">DNI</th>'+
                '<th width="250">Apellidos y Nombres</th>'+
                '<th>Area</th>'+
                '<th>Estado Civil</th>'+
                '<th>Fecha Ingreso</th>'+
                '<th>Cargo</th>'+
                '<th width="80">Sueldo</th>'+
                '<th width="80">Asignación Familiar</th>'+
                '</tr>'+
            '</thead>';
            dataSet = response.result;
            columns = [
                {'data': 'empresa'},
                {'data': 'sede'},
                {'data': 'nro_documento'},
                {'data': function (data){return (data['apellido_paterno'] + ' ' + data['apellido_materno'] + ' ' + data['nombres']);}},
                {'data': 'area'},
                {'data': 'estado_civil'},
                {'data': 'fecha_ingreso'},
                {'data': 'cargo'},
                {'data': 'remuneracion'},
                {'data': 'asignacion'}
            ];

            $('#my-datos-grl-table').html(thead);
            $('#my-datos-grl-table').dataTable({
                'dom': 'rtip',
                'language' : vardataTables[0],
                "pageLength": 15,
                'processing': true,
                'bDestroy': true,
                'data': dataSet,
                'columns': columns
            });
        }
    });
}

function reporteExcelAfp(){
    window.open('buscar_reporte_afp');
}