var rutaListaElaborados,
    rutaGetIdEmpresa,
    rutaSedeByEmpresa,
    rutaGrupoBySede,
    rutaVerFlujos,
    rutaExplorarRequerimiento;

function inicializarRutasListado(
    _rutaListaElaborados,
    _rutaGetIdEmpresa,
    _rutaSedeByEmpresa,
    _rutaGrupoBySede,
    _rutaVerFlujos,
    _rutaExplorarRequerimiento,
) {

    rutaListaElaborados = _rutaListaElaborados;
    rutaGetIdEmpresa = _rutaGetIdEmpresa;
    rutaSedeByEmpresa = _rutaSedeByEmpresa;
    rutaGrupoBySede = _rutaGrupoBySede;
    rutaVerFlujos = _rutaVerFlujos;
    rutaExplorarRequerimiento = _rutaExplorarRequerimiento;

    let fisrtRolUsuario = roles.slice(0, 1).shift();

    let fisrtIdRolUsuario = fisrtRolUsuario.id_grupo;
    if (fisrtIdRolUsuario > 0) {
        listarTablaReq(null, null, fisrtIdRolUsuario, null);
    } else {
        listarTablaReq(null, null, null, null);

    }

    vista_extendida();

}

var userSession = [];
var disabledBtn = true;
$(function () {
    var vardataTables = funcDatatables();

    // vista_extendida();

    $.ajax({
        type: 'GET',
        url: '/session-rol-aprob',
        success: function (response) {
            // console.log(response); 
            userSession = response;
            userSession.roles.forEach(element => {
                // console.log(element.nombre_area);
                if (element.nombre_area == 'logistica' || element.nombre_area == 'LOGISTICA') {
                    disabledBtn = false;
                }


            });
        }
    });


});








function listar_requerimientos_elaborados(name = null) {
    let data = { nombre: name };
    $.ajax({
        type: 'GET',
        bDestroy: true,

        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: rutaGetIdEmpresa,
        dataType: 'JSON',
        data: data,
        success: function (response) {
            // document.querySelector('div[type="lista_requerimiento"] select[id="id_empresa_select"]').value = response;
            listarTablaReq(response, null, null)
            getDataSelectSede(response);
        }
    });
    return false;
}



function handleChangeFilterEmpresaListReqByEmpresa(e) {
    let id_sede = document.querySelector('div[type="lista_requerimiento"] select[id="id_sede_select"]').value;
    let id_grupo = document.querySelector('div[type="lista_requerimiento"] select[id="id_grupo_select"]').value;
    let id_prioridad = document.querySelector('div[type="lista_requerimiento"] select[id="id_prioridad_select"]').value;

    listarTablaReq(e.target.value, id_sede, id_grupo, id_prioridad);
    getDataSelectSede(e.target.value);
}


function getDataSelectSede(id_empresa = null) {
    if (id_empresa > 0) {

        $.ajax({
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: rutaSedeByEmpresa + '/' + id_empresa,
            dataType: 'JSON',
            success: function (response) {
                // console.log(response);
                llenarSelectSede(response);

            }
        });
    }
    return false;
}

function llenarSelectSede(data) {
    let selectSede = document.querySelector('div[type="lista_requerimiento"] select[id="id_sede_select"]');
    let html = '<option value="0">Todas</option>';
    data.forEach(element => {
        html += '<option value="' + element.id_sede + '">' + element.codigo + '</option>'
    });

    selectSede.innerHTML = html;
    document.querySelector('div[type="lista_requerimiento"] select[id="id_sede_select"]').removeAttribute('disabled');


}


function handleChangeFilterSedeListReqByEmpresa(e) {
    let id_empresa = document.querySelector('div[type="lista_requerimiento"] select[id="id_empresa_select"]').value;

    listarTablaReq(id_empresa, e.target.value)
    getDataSelectGrupo(e.target.value);
}

function getDataSelectGrupo(id_sede) {
    if (id_sede > 0) {

        $.ajax({
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: rutaGrupoBySede + '/' + id_sede,
            dataType: 'JSON',
            success: function (response) {
                // console.log(response);
                llenarSelectGrupo(response);

            }
        });
    }
    return false;
}

function llenarSelectGrupo(data) {
    let selectGrupo = document.querySelector('div[type="lista_requerimiento"] select[id="id_grupo_select"]');
    let html = '<option value="0">Todas</option>';
    data.forEach(element => {
        html += '<option value="' + element.id_grupo + '">' + element.descripcion + '</option>'
    });

    selectGrupo.innerHTML = html;
    document.querySelector('div[type="lista_requerimiento"] select[id="id_grupo_select"]').removeAttribute('disabled');
}

function handleChangeFilterGrupoListReqByEmpresa(e) {
    let id_empresa = document.querySelector('div[type="lista_requerimiento"] select[id="id_empresa_select"]').value;
    let id_sede = document.querySelector('div[type="lista_requerimiento"] select[id="id_sede_select"]').value;
    let id_prioridad = document.querySelector('div[type="lista_requerimiento"] select[id="id_prioridad_select"]').value;

    listarTablaReq(id_empresa, id_sede, e.target.value, id_prioridad);
}
function handleChangeFilterPrioridad(e) {
    let id_empresa = document.querySelector('div[type="lista_requerimiento"] select[id="id_empresa_select"]').value;
    let id_sede = document.querySelector('div[type="lista_requerimiento"] select[id="id_sede_select"]').value;
    let id_grupo = document.querySelector('div[type="lista_requerimiento"] select[id="id_grupo_select"]').value;
    listarTablaReq(id_empresa, id_sede, id_grupo, e.target.value);
}

function vista_extendida() {
    let body = document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse");
}

function listarTablaReq(id_empresa = null, id_sede = null, id_grupo = null, id_prioridad = null) {

    var vardataTables = funcDatatables();
    $('#ListaReq').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'order': [[1, 'desc']],
        'serverSide': true,
        'destroy': true,
        'ajax': {
            // url:'/logistica/requerimiento/lista/'+id_empresa+'/'+id_sede+'/'+id_grupo,
            url: rutaListaElaborados,
            type: 'POST',
            // data: {_token: "{{csrf_token()}}"}
            data: { id_empresa, id_sede, id_grupo, id_prioridad }
        },
        'columns': [
            { 'data': 'priori', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
            { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
            { 'data': 'concepto', 'name': 'concepto' },
            { 'data': 'fecha_entrega', 'name': 'fecha_entrega', 'className': 'text-center' },
            { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
            { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
            { 'data': 'grupo', 'name': 'adm_grupo.descripcion' },
            { 'data': 'nombre_usuario', 'name': 'nombre_usuario' },
            { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc' }
        ],
        'columnDefs': [
            {
                'render': function (data, type, row) {
                    if (row['priori'] == 'Normal') {
                        return '<center> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal" ></i></center>';
                    } else if (row['priori'] == 'Media') {
                        return '<center> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"  ></i></center>';
                    } else if (row['Alta']) {
                        return '<center> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítico"  ></i></center>';
                    } else {
                        return '';
                    }
                }, targets: 0
            },
            {
                'render': function (data, type, row) {
                    let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                    let containerCloseBrackets = '</div></center>';
                    let btnEditar = '';
                    if (row.id_usuario == auth_user.id_usuario && row.estado == 3) {
                        btnEditar = '<button type="button" class="btn btn-xs bg-default" title="Editar" onClick="editarListaReq(' + row['id_requerimiento'] + ');"><i class="fas fa-edit fa-xs"></i></button>';
                    }
                    let btnDetalleRapido = '<button type="button" class="btn btn-xs btn-info" title="Ver detalle" onClick="viewFlujo(' + row['id_requerimiento'] + ', ' + row['id_doc_aprob'] + ');"><i class="fas fa-eye fa-xs"></i></button>';
                    let btnTracking = '<button type="button" class="btn btn-xs bg-primary" title="Explorar Requerimiento" onClick="tracking_requerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-globe fa-xs"></i></button>';
                    return containerOpenBrackets + btnDetalleRapido + btnEditar + btnTracking + containerCloseBrackets;
                }, targets: 9
            },
        ],
        "createdRow": function (row, data, dataIndex) {
            if (data.estado == 2) {
                $(row).css('color', '#4fa75b');
            }
            if (data.estado == 3) {
                $(row).css('color', '#ee9b1f');
            }
            if (data.estado == 7) {
                $(row).css('color', '#d92b60');
            }
        },
        'initComplete': function () {
        }
    });
    // $('#ListaReq').dataTable({
    //     'dom': 'frtip',
    //     'language' : vardataTables[0],
    //     'processing': true,
    //     'bDestroy': true,
    //     'ajax': '/logistica/listar_requerimientos/'+id_empresa+'/'+id_sede+'/'+id_grupo,
    //     'order' : [],
    //     "columnDefs": [
    //         { className: "text-right", "targets": [ 3 ] }
    //     ],
    //     'initComplete': function () {
    //         $('#ListaReq_filter label input').focus();
    //         }
    // });
    $('#ListaReq').DataTable().on("draw", function () {
        resizeSide();
    });

}

// function check(cbx){
//     var idcb = cbx.id;
//     var id_req = $(cbx).attr('data-primary');
//     var id_det = $(cbx).attr('data-secundary');
//     if($(cbx).is(":checked")){
//         $("#"+idcb).attr('checked', 'checked');
//         openModalObserva(id_req, id_det, idcb);
//     }else{
//         $("#"+idcb).removeAttr('checked');
//     }
// }

// function openModalObserva(req, detalle, check){
//     var ask = confirm('¿Desea agregar una observación al Item?');
//     if (ask == true){
//         $('#modal-obs-motivo [name=id_requerimiento]').val(req);
//         $('#modal-obs-motivo [name=id_detalle_requerimiento]').val(detalle);
//         // $('#modal-obs-motivo [name=value_check]').val(check);
//         $("#"+check).attr('disabled', true);
//         $('#modal-obs-motivo').modal({show: true, backdrop: 'static', keyboard: false});
//     }else{
//         $("#"+check).removeAttr('checked');
//         return false;
//     }
// }

function editarListaReq(id) {
    localStorage.setItem("id_req", id);
    location.assign('../elaboracion/index');
}

function crearCoti(req) {
    localStorage.setItem("idReqCot", req);
    location.assign('../cotizacion/gestionar');
}

function atender_requerimiento(id, doc, flujo, type) {
    if (id == 0 || doc == 0 || flujo == 0 || type == '') {
        // console.warn(id,doc,flujo,type);

        alert('ERROR, no se encontro un flujo de aprobación, contactar con el administrador.')
    } else {
        $('#form-aprobacion').attr('type', type);
        if (type == 'aprobar') {

            $.ajax({
                type: 'GET',
                url: '/logistica/get_requerimiento/' + id + '/' + 0,
                dataType: 'JSON',
                success: function (response) {
                    // console.log(response);
                    document.querySelector("div[id='modal-aprobacion-docs'] form[id='form-aprobacion'] input[name='codigo']").value = response.requerimiento[0].codigo;
                    document.querySelector("div[id='modal-aprobacion-docs'] form[id='form-aprobacion'] input[name='id_area']").value = response.requerimiento[0].id_area;
                }
            });

            $('[name=id_documento]').val(id);
            $('[name=doc_aprobacion]').val(doc);
            $('[name=flujo]').val(flujo);
            openModalAprob();
        } else if (type == 'observar') {
            openModalObs(id, doc, flujo);
        } else if (type == 'denegar') {
            document.querySelector("form[id='form-aprobacion'] h3[class='modal-title']").textContent = 'Denegar Requerimiento';
            $('[name=id_documento]').val(id);
            $('[name=doc_aprobacion]').val(doc);
            $('[name=flujo]').val(flujo);
            openModalAprob();
        }
    }

}

function viewFlujo(req, doc) {
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: rutaVerFlujos + '/' + req + '/' + doc,
        dataType: 'JSON',
        beforeSend: function () {
            $(document.body).append('<span class="loading"><div></div></span>');
        },
        success: function (response) {
            // console.log(response.siguiente);
            $('.loading').remove();
            if (response.cont > 0) {
                $('#flujo-detalle').removeClass('oculto');
                $('#flujo-proximo').removeClass('oculto');
            } else {
                $('#flujo-detalle').addClass('oculto');
                $('#flujo-proximo').addClass('oculto');
            }

            $('#req-detalle').html(response.requerimiento);
            $('#flujo-detalle').html(response.flujo);
            $('#flujo-proximo').html(response.siguiente);
            $('#modal-flujo-aprob').modal({ show: true, backdrop: 'static' });
        }
    });
    return false;
}

function imprimirReq(id) {
    window.open('/logistica/imprimir-requerimiento-pdf/' + id + '/' + 0, 'Requerimiento', 'width=864, height=650');
}

function verArchivosAdjuntosRequerimiento(id) {
    $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });

    $('#section_upload_files').addClass('invisible');

    adjuntos_requerimiento = [];
    baseUrl = '/logistica/mostrar-adjuntos/' + id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);
            if (response.length > 0) {
                for (x = 0; x < response.length; x++) {
                    adjuntos_requerimiento.push({
                        'id_adjunto': response[x].id_adjunto,
                        'id_detalle_requerimiento': response[x].id_detalle_requerimiento,
                        'archivo': response[x].archivo,
                        'fecha_registro': response[x].fecha_registro,
                        'estado': response[x].estado,
                        'file': []
                    });
                }
                llenar_tabla_archivos_adjuntos(adjuntos_requerimiento);

            } else {
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData = row.insertCell(0);
                tdSinData.setAttribute('colspan', '5');
                tdSinData.setAttribute('class', 'text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function llenar_tabla_archivos_adjuntos(adjuntos) {
    limpiarTabla('listaArchivos');
    htmls = '<tr></tr>';
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    for (var a = 0; a < adjuntos.length; a++) {

        var row = table.insertRow(a + 1);
        var tdIdArchivo = row.insertCell(0);
        tdIdArchivo.setAttribute('class', 'hidden');
        tdIdArchivo.innerHTML = adjuntos[a].id_adjunto ? adjuntos[a].id_adjunto : '0';
        var tdIdDetalleReq = row.insertCell(1);
        tdIdDetalleReq.setAttribute('class', 'hidden');
        tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento ? adjuntos[a].id_detalle_requerimiento : '0';
        row.insertCell(2).innerHTML = a + 1;
        row.insertCell(3).innerHTML = adjuntos[a].archivo ? adjuntos[a].archivo : '-';
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
            '<a' +
            '    class="btn btn-primary btn-sm "' +
            '    name="btnAdjuntarArchivos"' +
            '    href="/files/logistica/detalle_requerimiento/' + adjuntos[a].archivo + '"' +
            '    target="_blank"' +
            '    data-original-title="Descargar Archivo"' +
            '>' +
            '    <i class="fas fa-file-download"></i>' +
            '</a>' +
            '</div>';

    }
    return null;
}

function limpiarTabla(idElement) {
    // console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for (var i = table.rows.length - 1; i > 0; i--) {
        table.deleteRow(i);
    }
    return null;
}


function get_data_tracking(id_req) {
    baseUrl = rutaExplorarRequerimiento + '/' + id_req;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);
            llenar_tabla_historial_aprobaciones(response.historial_aprobacion);
            llenar_tabla_flujo_aprobacion(response.flujo_aprobacion);
            llenar_tabla_cotizaciones(response.solicitud_cotizaciones);
            llenar_tabla_cuadro_comparativo(response.cuadros_comparativos);
            llenar_tabla_ordenes(response.ordenes);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function llenar_tabla_historial_aprobaciones(data) {
    limpiarTabla('listaHistorialAprobacion');
    htmls = '<tr></tr>';
    $('#listaHistorialAprobacion tbody').html(htmls);
    var table = document.getElementById("listaHistorialAprobacion");
    if (data.length > 0) {
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
            row.insertCell(0).innerHTML = data[a].estado ? data[a].estado.toUpperCase() : '-';
            row.insertCell(1).innerHTML = data[a].nombre_corto ? data[a].nombre_corto : '-';
            row.insertCell(2).innerHTML = data[a].obs ? data[a].obs : '-';
            row.insertCell(3).innerHTML = data[a].fecha ? data[a].fecha : '-';
        }
    }
}
function llenar_tabla_flujo_aprobacion(data) {
    // console.log(data);
    limpiarTabla('listaFlujoAprobacion');
    htmls = '<tr></tr>';
    $('#listaFlujoAprobacion tbody').html(htmls);
    var table = document.getElementById("listaFlujoAprobacion");
    if (data.length > 0) {
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
            row.insertCell(0).innerHTML = data[a].orden ? data[a].orden : '-';
            row.insertCell(1).innerHTML = data[a].nombre_fase ? data[a].nombre_fase : '-';
            row.insertCell(2).innerHTML = data[a].nombre_responsable ? data[a].nombre_responsable : '-';
            row.insertCell(3).innerHTML = data[a].criterio_monto.length > 0 ? data[a].criterio_monto.map(item => item.descripcion) : '';
            row.insertCell(4).innerHTML = data[a].criterio_prioridad.length > 0 ? data[a].criterio_prioridad.map(item => item.descripcion) : '';
        }
    }
}
function llenar_tabla_cotizaciones(data) {
    limpiarTabla('listaCotizaciones');
    htmls = '<tr></tr>';
    $('#listaCotizaciones tbody').html(htmls);
    var table = document.getElementById("listaCotizaciones");

    let cantidad_cotizaciones = data.length;
    document.getElementById('cantidad_cotizaciones').innerHTML = cantidad_cotizaciones;

    if (cantidad_cotizaciones > 0) {
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
            row.insertCell(0).innerHTML = a + 1;
            row.insertCell(1).innerHTML = data[a].codigo_cotizacion ? data[a].codigo_cotizacion : '-';
            row.insertCell(2).innerHTML = data[a].razon_social ? data[a].razon_social : '-' + data[a].nombre_doc_identidad ? data[a].nombre_doc_identidad : '-' + data[a].nro_documento ? data[a].nro_documento : '-';
            row.insertCell(3).innerHTML = data[a].email_proveedor ? data[a].email_proveedor : '-';
            row.insertCell(4).innerHTML = data[a].razon_social_empresa ? data[a].razon_social_empresa : '-' + data[a].nombre_doc_idendidad_empresa ? data[a].nombre_doc_idendidad_empresa : '-' + data[a].nro_documento_empresa ? data[a].nro_documento_empresa : '-';
            row.insertCell(5).innerHTML = data[a].fecha_registro ? data[a].fecha_registro : '-';
            row.insertCell(6).innerHTML = data[a].estado_envio ? data[a].estado_envio : '-';
            if (disabledBtn == false) {
                row.insertCell(7).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                    '<button type="button"' +
                    '    class="btn btn-sm btn-log bg-maroon"' +
                    '    name="btnVerDetalleCotizacion"' +
                    '    title="Ver detalle"' +
                    '   onClick="detalleCotizacionModal(' + data[a].id_cotizacion + ');"' +
                    '   >' +
                    '    <i class="fas fa-eye fa-xs"></i>' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-success"' +
                    '    name="btnDownloadExcelDirectSolicitudCotizacion"' +
                    '    title="Descargar en Excel"' +
                    '   onClick="downloadDirectSolicitudCotizacion(' + data[a].id_cotizacion + ');"' +
                    '   >' +
                    '    <i class="fas fa-file-excel fa-xs"></i>' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-default"' +
                    '    name="btnIrDirectSolicitudCotizacion"' +
                    '    title="Ir a Gestión de Solicitudes de Cotización"' +
                    '   onClick="irDirectSolicitudCotizacion(' + data[a].requerimiento[0].id_requerimiento + ');"' +
                    '   >' +
                    '    <i class="fas fa-compass fa-xs"></i>' +
                    '</button>' +
                    '</div>';
            } else {
                row.insertCell(7).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                    '<button type="button"' +
                    '    class="btn btn-sm btn-log bg-maroon"' +
                    '    name="btnVerDetalleCotizacion"' +
                    '    title="Ver detalle"' +
                    '   onClick="detalleCotizacionModal(' + data[a].id_cotizacion + ');"' +
                    '   >' +
                    '    <i class="fas fa-eye fa-xs"></i>' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-success"' +
                    '    name="btnDownloadExcelDirectSolicitudCotizacion"' +
                    '    title="Descargar en Excel"' +
                    '   onClick="downloadDirectSolicitudCotizacion(' + data[a].id_cotizacion + ');"' +
                    '   >' +
                    '    <i class="fas fa-file-excel fa-xs"></i>' +
                    '</button>' +
                    '</div>';
            }
        }
    }
}
function detalleCotizacionModal(id_cotizacion) {
    $('#modal-detalle-cotizacion').modal({
        show: true,
        backdrop: 'static'
    });
    get_data_detalle_cotizacion(id_cotizacion);
}

function get_data_detalle_cotizacion(id_cotizacion) {
    baseUrl = '/logistica/cuadro_comparativos/valorizacion/lista_item/' + id_cotizacion;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);
            llenar_tabla_detalle_cotizacion(response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function llenar_tabla_detalle_cotizacion(data) {
    limpiarTabla('listaDetalleCotizacion');
    htmls = '<tr></tr>';
    $('#listaDetalleCotizacion tbody').html(htmls);
    var table = document.getElementById("listaDetalleCotizacion");
    let cantidad = data.item_cotizacion.length;
    if (cantidad > 0) {
        document.getElementById('razon_social_proveedor').innerHTML = data.item_cotizacion[0].razon_social;

        for (var a = 0; a < data.item_cotizacion.length; a++) {
            let precio_total = parseFloat(data.item_cotizacion[a].cantidad_cotizada * data.item_cotizacion[a].precio_cotizado);
            var row = table.insertRow(a + 1);
            row.insertCell(0).innerHTML = a + 1;
            row.insertCell(1).innerHTML = data.item_cotizacion[a].codigo ? data.item_cotizacion[a].codigo : '-';
            row.insertCell(2).innerHTML = data.item_cotizacion[a].descripcion ? data.item_cotizacion[a].descripcion : data.item_cotizacion[a].descripcion_adicional;
            row.insertCell(3).innerHTML = data.item_cotizacion[a].cantidad ? data.item_cotizacion[a].cantidad : '0';
            row.insertCell(4).innerHTML = data.item_cotizacion[a].precio_referencial ? data.item_cotizacion[a].precio_referencial : '0';
            row.insertCell(5).innerHTML = data.item_cotizacion[a].unidad_medida ? data.item_cotizacion[a].unidad_medida : '-';
            row.insertCell(6).innerHTML = data.item_cotizacion[a].cantidad_cotizada ? data.item_cotizacion[a].cantidad_cotizada : '0';
            row.insertCell(7).innerHTML = data.item_cotizacion[a].precio_cotizado ? data.item_cotizacion[a].precio_cotizado : '0';
            row.insertCell(8).innerHTML = precio_total ? precio_total : '0';

        }
    }
}

function downloadDirectSolicitudCotizacion(id_cotizacion) {
    window.open('/solicitud_cotizacion_excel/' + id_cotizacion);
}
function irDirectSolicitudCotizacion(id_requerimiento) {
    localStorage.setItem("idReqCot", id_requerimiento);
    location.assign('../cotizacion/gestionar');
}

function llenar_tabla_cuadro_comparativo(data) {
    // console.log(data);

    limpiarTabla('listaCuadroComparativo');
    htmls = '<tr></tr>';
    $('#listaCuadroComparativo tbody').html(htmls);
    var table = document.getElementById("listaCuadroComparativo");

    let cantidad_cuadros = data.length;
    let cantidad_buena_pro = 0;
    document.getElementById('cantidad_cuadros_comparativos').innerHTML = cantidad_cuadros;
    if (cantidad_cuadros > 0) {
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
            row.insertCell(0).innerHTML = a + 1;
            row.insertCell(1).innerHTML = data[a].codigo_grupo ? data[a].codigo_grupo : '-';
            row.insertCell(2).innerHTML = data[a].cotizaciones.map((item, index) => {
                cantidad_buena_pro += item.total_buena_pro;
                return item.codigo_cotizacion + ' [ ' + item.razon_social + ' - ' + item.nombre_doc_identidad + ': ' + item.nro_documento + ' ]'

            });
            row.insertCell(3).innerHTML = cantidad_buena_pro ? cantidad_buena_pro : '-';
            row.insertCell(4).innerHTML = data[a].fecha_inicio ? data[a].fecha_inicio : '-';
            if (disabledBtn == false) {
                row.insertCell(5).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-success"' +
                    '    name="btnDownloadExcelDirectCuadroComparativo"' +
                    '    title="Descargar en Excel"' +
                    '   onClick="downloadDirectCuadroComparativo(' + data[a].id_grupo_cotizacion + ');"' +
                    '  >' +
                    '    <i class="fas fa-file-excel"></i>' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-default"' +
                    '    name="btnIrDirectSolicitudCotizacion"' +
                    '    title="Ir a Cuadro Comparativo"' +
                    '   onClick="irDirectCuadroComparativo(3,' + data[a].id_grupo_cotizacion + ');"' +
                    '>' +
                    '    <i class="fas fa-compass fa-xs"></i>' +
                    '</button>' +
                    '</div>';
            } else {
                row.insertCell(5).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-success"' +
                    '    name="btnDownloadExcelDirectCuadroComparativo"' +
                    '    title="Descargar en Excel"' +
                    '   onClick="downloadDirectCuadroComparativo(' + data[a].id_grupo_cotizacion + ');"' +
                    '  >' +
                    '    <i class="fas fa-file-excel"></i>' +
                    '</button>' +
                    '</div>';
            }
        }
    }
}

function llenar_tabla_ordenes(data) {
    // console.log(data);

    limpiarTabla('listaOrdenes');
    htmls = '<tr></tr>';
    $('#listaOrdenes tbody').html(htmls);
    var table = document.getElementById("listaOrdenes");

    let cantidad = data.length;
    document.getElementById('cantidad_ordenes').innerHTML = cantidad;
    let cantidad_buena_pro = 0;
    if (cantidad > 0) {
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
            row.insertCell(0).innerHTML = a + 1;
            row.insertCell(1).innerHTML = data[a].codigo ? data[a].codigo : '-';
            row.insertCell(2).innerHTML = data[a].razon_social_proveedor ? data[a].razon_social_proveedor : '-' + ' [' + tipo_doc_proveedor ? tipo_doc_proveedor : '-' + ' ' + nro_documento_proveedor ? nro_documento_proveedor : '-' + ' ]';
            row.insertCell(3).innerHTML = data[a].cotizaciones.map((item, index) => {
                cantidad_buena_pro += item.total_buena_pro;
                return '[ ' + item.razon_social_empresa + ' - ' + item.tipo_documento_empresa + ': ' + item.nro_documento_empresa + ' ]'

            });
            row.insertCell(4).innerHTML = data[a].monto_total ? data[a].monto_total : '-';
            row.insertCell(5).innerHTML = data[a].fecha ? data[a].fecha : '-';
            if (disabledBtn == false) {
                row.insertCell(6).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-danger"' +
                    '    name="btnDownloadExcelDirectOrden"' +
                    '    title="Descargar"' +
                    '   onClick="downloadDirectOrden(' + data[a].id_orden_compra + ');"' +
                    '>' +
                    '    <i class="fas fa-file-pdf"></i>' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-default"' +
                    '    name="btnIrDirectOrden"' +
                    '    title="Ir a Orden"' +
                    '   onClick="irDirectOrden(' + data[a].id_orden_compra + ');"' +
                    ' >' +
                    '    <i class="fas fa-compass fa-xs"></i>' +
                    '</button>' +
                    '</div>';
            } else {
                row.insertCell(6).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                    '</button>' +
                    '<button type="button"' +
                    '    class="btn btn-xs btn-danger"' +
                    '    name="btnDownloadExcelDirectOrden"' +
                    '    title="Descargar"' +
                    '   onClick="downloadDirectOrden(' + data[a].id_orden_compra + ');"' +
                    '>' +
                    '    <i class="fas fa-file-pdf"></i>' +
                    '</button>' +
                    '</div>';
            }
        }
    }
}

function tracking_requerimiento(id_req) {
    $('#modal-tracking-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });
    get_data_tracking(id_req);
}



function downloadDirectCuadroComparativo(id_grupo_cotizacion) {
    window.open('/logistica/cuadro_comparativo/exportar_excel/' + id_grupo_cotizacion);

}

function irDirectCuadroComparativo(tipo, id_grupo_cotizacion) {
    localStorage.setItem("idGrupo", id_grupo_cotizacion);
    localStorage.setItem("TipoCodigo", tipo);
    location.assign('../cotizacion/cuadro-comparativo');
}

function downloadDirectOrden(id_orden_compra) {
    // var id = encode5t(id_orden_compra);
    window.open('/generar_orden_pdf/' + id_orden_compra);
}
function irDirectOrden(id_orden_compra) {
    localStorage.setItem("idOrden", id_orden_compra);
    location.assign('../../generar_orden');
}

