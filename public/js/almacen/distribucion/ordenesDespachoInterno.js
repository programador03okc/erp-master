function listarDespachosInternos() {
    var fecha = $('#fecha_programacion').val();
    $.ajax({
        type: 'GET',
        url: 'listarDespachosInternos/' + fecha,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var page = $('.page-main').attr('type');
            response['listaProgramados'].forEach(element => {
                html += `
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h5 style="margin:0px;font-size: 13px;">
                            ${page == 'despachosInternos' ?
                        `<div style="display:block;">
                                    <i class="fas fa-chevron-up" style="cursor: pointer;" data-toggle="tooltip"
                                    data-placement="bottom" title="Subir prioridad" onClick="subirPrioridad(${element.id_od})"></i>
                                    <i class="fas fa-chevron-down" style="cursor: pointer;"  data-toggle="tooltip"
                                    data-placement="bottom" title="Bajar prioridad" onClick="bajarPrioridad(${element.id_od})"></i>
                                </div>` : ''}
                                <i class="fas fa-file-pdf" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                    title="Ver PDF de la Orden de Transformación" onClick="imprimirTransformacion(${element.id_transformacion})"></i>
                                ${element.codigo_transformacion}
                                <i class="fas fa-table" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                    title="Abrir Orden de Transformación" onClick="abrirTransformacion(${element.id_transformacion})"></i>
                            <br>
                            ${element.codigo_req} - ${element.codigo_oportunidad}
                            <a target="_blank" href="https://mgcp.okccloud.com/mgcp/cuadro-costos/detalles/${element.id_oportunidad}" data-toggle="tooltip"
                                data-placement="bottom" title="Ver CDP en mgcp"> <i class="fas fa-th-large"></i></a>
                            <br>
                        </h5>
                        <p style="margin-bottom:0px;">${element.nombre_entidad}</p>
                        ${element.comentario !== null ? '<p style="margin-bottom:0px;font-size: 13px;">' + element.comentario + '</p>' : ""}
                    </div >
                </div> `;
            });

            //     <a href="#" class="small-box-footer" style="cursor: auto;">
            //     ${page == 'tableroTransformaciones' ?
            //     `<i class="fa fa-arrow-circle-right" style="cursor: pointer;" data-toggle="tooltip"
            //     data-placement="bottom" title="Siguiente" onClick="siguiente(${element.estado},${element.id_od},${element.id_transformacion})"></i>` : ''
            // }
            // </a>
            $('#listaProgramados').html(html);

            html = '';
            response['listaPendientes'].forEach(element => {
                html += `
                <div class= "small-box bg-blue">
                    <div class="inner">
                        <h5 style="margin:0px;font-size: 13px;">
                            <i class="fas fa-file-pdf" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                title="Ver PDF de la Orden de Transformación" onClick="imprimirTransformacion(${element.id_transformacion})"></i>
                                ${element.codigo_transformacion}
                            <i class="fas fa-table" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                title="Abrir Orden de Transformación" onClick="abrirTransformacion(${element.id_transformacion})"></i>
                            <br>
                            ${element.codigo_req} - ${element.codigo_oportunidad}
                            <a target="_blank" href="https://mgcp.okccloud.com/mgcp/cuadro-costos/detalles/${element.id_oportunidad}" data-toggle="tooltip"
                                data-placement="bottom" title="Ver CDP en mgcp"><i class="fas fa-th-large"></i></a>
                             - ${formatDate(element.fecha_despacho)}
                            <br>
                        </h5><p style="margin-bottom:0px;">${element.nombre_entidad}</p>
                        ${element.comentario !== null ? '<p style="margin-bottom:0px;font-size: 13px;">' + element.comentario + '</p>' : ""}
                    </div>
                    <a href="#" class="small-box-footer" style="cursor: auto;">
                        ${page == 'tableroTransformaciones' ?
                        `
                            <i class="fa fa-arrow-circle-right"  style="cursor: pointer;" data-toggle="tooltip"
                            data-placement="bottom" title="Siguiente" onClick="siguiente(${element.estado},${element.id_od},${element.id_transformacion})"></i>`
                        : ''}
                    </a>
                </div> `;
                // <i class="fa fa-arrow-circle-left"  style="cursor: pointer;" data-toggle="tooltip"
                //             data-placement="bottom" title="Anterior" onClick="anterior(${element.estado},${element.id_od},${element.id_transformacion})"></i>
            });
            $('#listaPendientes').html(html);

            html = '';
            response['listaProceso'].forEach(element => {
                html += `
                <div class= "small-box bg-orange" >
                    <div class="inner">
                        <h5 style="margin:0px;font-size: 13px;">
                                <i class="fas fa-file-pdf" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                    title="Ver PDF de la Orden de Transformación" onClick="imprimirTransformacion(${element.id_transformacion})"></i>
                                ${element.codigo_transformacion}
                                <i class="fas fa-table" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                    title="Abrir Orden de Transformación" onClick="abrirTransformacion(${element.id_transformacion})"></i>
                            <br>
                            ${element.codigo_req} - ${element.codigo_oportunidad}
                            <a target="_blank" href="https://mgcp.okccloud.com/mgcp/cuadro-costos/detalles/${element.id_oportunidad}" data-toggle="tooltip"
                                data-placement="bottom" title="Ver CDP en mgcp"> <i class="fas fa-th-large"></i></a>
                            - ${formatDate(element.fecha_despacho)}
                            <br>
                        </h5>
                        <p style="margin-bottom:0px;">${element.nombre_entidad}</p>
                        ${element.comentario !== null ? '<p style="margin-bottom:0px;font-size: 13px;">' + element.comentario + '</p>' : ""}
                    </div>
                    <a href="#" class="small-box-footer" style="cursor: auto;">
                    ${page == 'tableroTransformaciones' ?
                        `<i class="fa fa-arrow-circle-left"  style="cursor: pointer;" data-toggle="tooltip"
                        data-placement="bottom" title="Anterior" onClick="anterior(${element.estado},${element.id_od},${element.id_transformacion})"></i>
                        <i class="fa fa-arrow-circle-right"  style="cursor: pointer;" data-toggle="tooltip"
                        data-placement="bottom" title="Siguiente" onClick="siguiente(${element.estado},${element.id_od},${element.id_transformacion})"></i>`
                        : ''}
                    </a>
                </div> `;
            });
            $('#listaProceso').html(html);

            html = '';
            response['listaFinalizadas'].forEach(element => {
                html += `
                <div class= "small-box bg-green" >
                    <div class="inner">
                        <h5 style="margin:0px;font-size: 13px;">
                                <i class="fas fa-file-pdf" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                    title="Ver PDF de la Orden de Transformación" onClick="imprimirTransformacion(${element.id_transformacion})"></i>
                                ${element.codigo_transformacion}
                                <i class="fas fa-table" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" 
                                    title="Abrir Orden de Transformación" onClick="abrirTransformacion(${element.id_transformacion})"></i>
                            <br>
                            ${element.codigo_req} - ${element.codigo_oportunidad}
                            <a target="_blank" href="https://mgcp.okccloud.com/mgcp/cuadro-costos/detalles/${element.id_oportunidad}" data-toggle="tooltip"
                                data-placement="bottom" title="Ver CDP en mgcp"> <i class="fas fa-th-large"></i></a>
                            <br>
                        </h5>
                        <p style="margin-bottom:0px;">${element.nombre_entidad}</p>
                        ${element.comentario !== null ? '<p style="margin-bottom:0px;font-size: 13px;">' + element.comentario + '</p>' : ""}
                    </div>
                    <a href="#" class="small-box-footer" style="cursor: auto;">
                    ${page == 'tableroTransformaciones' ?
                        `<i class="fa fa-arrow-circle-left" style="cursor: pointer;" data-toggle="tooltip"
                        data-placement="bottom" title="Anterior" onClick="anterior(${element.estado},${element.id_od},${element.id_transformacion})"></i>`
                        : ''}
                    </a>
                </div> `;
            });
            $('#listaFinalizadas').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarPendientesAnteriores() {
    var fecha = $('#fecha_programacion').val();
    $('#modal-transformacionesPendientes').modal({
        show: true
    });

    var vardataTables = funcDatatables();

    $('#listaTransformacionesPendientes').dataTable({
        'dom': vardataTables[1],
        'buttons': [],
        'language': vardataTables[0],
        'bDestroy': true,
        ajax: {
            url: 'listarPendientesAnteriores/' + fecha,
            type: 'GET',
        },
        'columns': [
            { 'data': 'id_od' },
            { 'data': 'fecha_despacho' },
            { 'data': 'codigo_req' },
            { 'data': 'codigo_oportunidad' },
            { 'data': 'nombre_entidad' },
            // { 'data': 'estado' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}

function siguiente(estado, id_od, id_transformacion) {
    var siguiente = 0;
    switch (estado) {
        case 1:
            siguiente = 21;
            break;
        case 21:
            siguiente = 24;
            break;
        case 24:
            siguiente = 10;
            break;
        default:
            break;
    }
    cambiaEstado(siguiente, id_od, id_transformacion)
}

function anterior(estado, id_od, id_transformacion) {
    var anterior = 0;
    switch (estado) {
        case 21:
            anterior = 1;
            break;
        case 24:
            anterior = 21;
            break;
        case 10:
            anterior = 24;
            break;
        default:
            break;
    }
    cambiaEstado(anterior, id_od, id_transformacion);
}

function cambiaEstado(nuevo_estado, id_od, id_transformacion) {
    $.ajax({
        type: 'POST',
        url: 'cambiaEstado',
        data: {
            'estado': nuevo_estado,
            'id_od': id_od,
            'id_transformacion': id_transformacion,
        },
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.tipo == 'success') {
                listarDespachosInternos();
            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function subirPrioridad(id_od) {
    $.ajax({
        type: 'GET',
        url: 'subirPrioridad/' + id_od,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.tipo == 'success') {
                listarDespachosInternos();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function bajarPrioridad(id_od) {
    $.ajax({
        type: 'GET',
        url: 'bajarPrioridad/' + id_od,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.tipo == 'success') {
                listarDespachosInternos();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function pasarProgramadasAlDiaSiguiente() {
    Swal.fire({
        title: "¿Está seguro de pasar todas las programadas para mañana?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#3085d6", //"#00a65a", //
        cancelButtonColor: "#d33",
        cancelButtonText: "Aún No.",
        confirmButtonText: "Si, Pasar para mañana"
    }).then(result => {
        if (result.isConfirmed) {
            var fecha = $('#fecha_programacion').val();
            $.ajax({
                type: 'GET',
                url: 'pasarProgramadasAlDiaSiguiente/' + fecha,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    Lobibox.notify(response.tipo, {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                    if (response.tipo == 'success') {
                        listarDespachosInternos();
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
}

function imprimirTransformacion(id_transformacion) {
    if (id_transformacion !== null) {
        window.open('imprimir_transformacion/' + id_transformacion);
    } else {
        Swal.fire({
            title: "Debe seleccionar una Hoja de Transformación!",
            icon: "error",
        });
    }
}

function abrirTransformacion(id_transformacion) {
    console.log('abrir_transformacion' + id_transformacion);
    localStorage.setItem("id_transfor", id_transformacion);
    var win = window.open("/cas/customizacion/hoja-transformacion/index", '_blank');
    win.focus();
}