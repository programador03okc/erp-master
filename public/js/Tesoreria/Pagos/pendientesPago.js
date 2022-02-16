class RequerimientoPago {
    constructor(permisoVer, permisoEnviar, permisoRegistrar) {
        this.permisoVer = permisoVer;
        this.permisoEnviar = permisoEnviar;
        this.permisoRegistrar = permisoRegistrar;
        this.listarRequerimientos();
        this.listarComprobantes();
        this.listarOrdenes();
    }

    listarRequerimientos() {
        const permisoVer = this.permisoVer;
        const permisoEnviar = this.permisoEnviar;
        const permisoRegistrar = this.permisoRegistrar;

        console.log(permisoEnviar);
        console.log(permisoRegistrar);
        console.log(permisoVer);

        var vardataTables = funcDatatables();

        tableRequerimientos = $('#listaRequerimientos').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'serverSide': true,
            'ajax': {
                url: 'listarRequerimientosPago',
                type: 'POST',
            },
            'columns': [
                { 'data': 'id_requerimiento_pago', 'name': 'requerimiento_pago.id_requerimiento_pago' },
                {
                    'data': 'prioridad', 'name': 'adm_prioridad.descripcion',
                    'render': function (data, type, row) {
                        var imagen = '';
                        if (row['prioridad'] == 'Normal') {
                            imagen = '<i class="fas fa-thermometer-empty green" data-toggle="tooltip" data-placement="right" title="Normal"></i>';
                        }
                        else if (row['prioridad'] == 'Crítica') {
                            imagen = '<i class="fas fa-thermometer-full red" data-toggle="tooltip" data-placement="right" title="Crítica"></i>';
                        }
                        else if (row['prioridad'] == 'Alta') {
                            imagen = '<i class="fas fa-thermometer-half orange" data-toggle="tooltip" data-placement="right" title="Alta"></i>';
                        }
                        return imagen;
                    }, 'className': 'text-center'
                },
                { 'data': 'razon_social_empresa', 'name': 'empresa.razon_social' },
                { 'data': 'codigo', 'name': 'requerimiento_pago.codigo', 'className': 'text-center' },
                { 'data': 'grupo_descripcion', 'name': 'sis_grupo.descripcion' },
                { 'data': 'concepto', 'name': 'requerimiento_pago.concepto' },
                { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                {
                    'render': function (data, type, row) {
                        return (row['fecha_registro'] !== null ? formatDate(row['fecha_registro']) : '');
                    }, 'className': 'text-center', 'searchable': false
                },
                // { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                {
                    'data': 'monto_total', 'name': 'requerimiento_pago.monto_total',
                    'render': function (data, type, row) {
                        return formatNumber.decimal(row['monto_total'], '', -2);
                    }, 'className': 'text-right'
                },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['monto_total']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                    }, 'className': 'text-right celestito'
                },
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                    }
                },
                {
                    'render':
                        function (data, type, row) {
                            return `<div class="btn-group" role="group">
                            ${(row['id_estado'] == 2 && permisoEnviar == '1') ?
                                    `<button type="button" class="enviar btn btn-info boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_requerimiento_pago']}" data-tipo="requerimiento"
                                title="Autorizar pago"> <i class="fas fa-share"></i></button>`
                                    : ''}
                            ${row['id_estado'] == 5 ?
                                    `${permisoEnviar == '1' ?
                                        `<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip" 
                                        data-placement="bottom" data-id="${row['id_requerimiento_pago']}" data-tipo="requerimiento"
                                        title="Revertir autorización"><i class="fas fa-undo-alt"></i></button>`: ''}
                                    ${permisoRegistrar == '1' ?
                                        `<button type="button" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom" 
                                        data-id="${row['id_requerimiento_pago']}" data-cod="${row['codigo']}" data-tipo="requerimiento"
                                        data-total="${row['monto_total']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}"
                                        data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                        data-cta="${row['nro_cuenta']}" data-tpcta="${row['tipo_cuenta']}" title="Registrar Pago"> 
                                    <i class="fas fa-hand-holding-usd"></i> </button>`
                                        : ''}`
                                    : ''
                                }
                            ${row['suma_pagado'] > 0 && permisoVer == '1' ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_requerimiento_pago']}" title="Ver detalle de los pagos" >
                                    <i class="fas fa-chevron-down"></i></button>`: ''
                                }
                            </div > `;

                        }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });

    }

    listarOrdenes() {
        const permisoVer = this.permisoVer;
        const permisoEnviar = this.permisoEnviar;
        const permisoRegistrar = this.permisoRegistrar;

        var vardataTables = funcDatatables();
        tableOrdenes = $('#listaOrdenes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'serverSide': true,
            'ajax': {
                url: 'listarOrdenesCompra',
                type: 'POST'
            },
            'columns': [
                { 'data': 'id_orden_compra' },
                { 'data': 'razon_social_empresa', 'name': 'empresa.razon_social' },
                { 'data': 'codigo' },
                { 'data': 'codigo_softlink' },
                { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                {
                    'render': function (data, type, row) {
                        return (row['fecha'] !== null ? formatDate(row['fecha']) : '');
                    }, 'className': 'text-center', 'searchable': false
                },
                { 'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion' },
                { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                {
                    'render': function (data, type, row) {
                        return (row['suma_total'] !== null ? formatNumber.decimal(row['suma_total'], '', -2) : '0.00');
                    }, 'className': 'text-right'
                },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['suma_total']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                    }, 'className': 'text-right celestito'
                },
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                    }
                },
                {
                    'render':
                        function (data, type, row) {
                            return `< div class= "btn-group" role = "group" >
                ${((row['estado_pago'] == 1 || row['estado_pago'] == 2) && permisoEnviar == '1') ?
                                    `<button type="button" class="enviar btn btn-info boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                title="Autorizar pago" >
                                <i class="fas fa-share"></i></button>`: ''}
                            ${row['estado_pago'] == 5 ?
                                    `${permisoEnviar == '1' ?
                                        `<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                    title="Revertir autorización"><i class="fas fa-undo-alt"></i></button>` : ''}
                                    
                                ${permisoRegistrar == '1' ?
                                        `<button type="button" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_orden_compra']}" data-cod="${row['codigo']}" data-tipo="orden"
                                    data-total="${row['suma_total']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}" 
                                    data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                    data-cta="${row['nro_cuenta']}" data-tpcta="${row['tipo_cuenta']}"
                                    title="Registrar Pago"><i class="fas fa-hand-holding-usd"></i></button>`
                                        : ''}`
                                    : ''
                                }
                            ${row['suma_pagado'] > 0 && permisoVer == '1' ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" title="Ver detalle de los pagos" >
                                <i class="fas fa-chevron-down"></i></button>`: ''
                                }
                        </div > `;
                        }, 'searchable': false
                },
            ],
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible' }
            ],
        });

    }

    listarComprobantes() {
        var vardataTables = funcDatatables();
        // console.time();
        tableComprobantes = $('#listaComprobantes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'serverSide': true,
            'ajax': {
                url: 'listarComprobantesPagos',
                type: 'POST',
                // complete: function(){
                //     console.timeEnd();
                // }
            },
            'columns': [
                { 'data': 'id_doc_com', 'name': 'doc_com.id_doc_com' },
                { 'data': 'tipo_documento', 'name': 'cont_tp_doc.descripcion', 'className': 'text-center' },
                { 'data': 'serie', 'name': 'doc_com.serie', 'className': 'text-center' },
                { 'data': 'numero', 'name': 'doc_com.numero', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                { 'data': 'fecha_emision', 'name': 'doc_com.fecha_emision', 'className': 'text-center' },
                { 'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion', 'className': 'text-center' },
                { 'data': 'fecha_vcmto', 'name': 'doc_com.fecha_vcmto', 'className': 'text-center' },
                { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                { 'data': 'total_a_pagar_format', 'className': 'text-right' },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['total_a_pagar']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                        // return (formatDecimal(formatDecimal(row['total_a_pagar']) - formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0)));
                    }, 'className': 'text-right celestito'
                },
                { 'data': 'span_estado', 'searchable': false, 'className': 'text-center' },
                {
                    'render':
                        function (data, type, row) {
                            return `< div class= "btn-group" role = "group" >
        ${row['estado'] == 1 ?
                                    `<button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_doc_com']}" data-cod="${row['serie'] + '-' + row['numero']}" data-tipo="comprobante"
                                    data-total="${row['total_a_pagar']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}"
                                    data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                    data-cta="${row['nro_cuenta']}" title="Registrar Pago"> 
                                    <i class="fas fa-hand-holding-usd"></i> </button>`: ''
                                }
                            ${row['suma_pagado'] > 0 ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_doc_com']}" title="Ver detalle de los pagos" >
                                    <i class="fas fa-chevron-down"></i></button>`: ''
                                }
                            </div > `;

                        }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });

    }

}

$('#listaRequerimientos tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
});

$('#listaOrdenes tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
});

$('#listaComprobantes tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
});

$('#listaRequerimientos tbody').on("click", "button.enviar", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    enviarAPago(tipo, id);
});

$('#listaOrdenes tbody').on("click", "button.enviar", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    enviarAPago(tipo, id);
});

$('#listaRequerimientos tbody').on("click", "button.revertir", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    revertirEnvio(tipo, id);
});

$('#listaOrdenes tbody').on("click", "button.revertir", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    revertirEnvio(tipo, id);
});

var iTableCounter = 1;
var oInnerTable;
var tableOrdenes;

$('#listaOrdenes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableOrdenes.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagos(iTableCounter, id, row, "orden");
        tr.addClass('shown');
        oInnerTable = $('#listaOrdenes_' + iTableCounter).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounter = iTableCounter + 1;
    }
});

var iTableCounterComp = 1;
var oInnerTableComp;
var tableComprobantes;

$('#listaComprobantes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableComprobantes.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagos(iTableCounter, id, row, "comprobante");
        tr.addClass('shown');
        oInnerTableComp = $('#listaComprobantes_' + iTableCounterComp).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounterComp = iTableCounterComp + 1;
    }
});


var iTableCounterReq = 1;
var oInnerTableReq;
var tableRequerimientos;

$('#listaRequerimientos tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableRequerimientos.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagos(iTableCounter, id, row, "requerimiento");
        tr.addClass('shown');
        oInnerTableReq = $('#listaRequerimientos_' + iTableCounterReq).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounterReq = iTableCounterReq + 1;
    }
});

function formatPagos(table_id, id, row, tipo) {
    console.log(tipo)
    $.ajax({
        type: 'GET',
        url: 'listarPagos/' + tipo + '/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html += '<tr id="' + element.id_pago + '">' +
                        '<td style="border: none;">' + i + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.fecha_pago !== null ? formatDate(element.fecha_pago) : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + element.razon_social_empresa + '</td>' +
                        '<td style="border: none; text-align: center">' + element.nro_cuenta + '</td>' +
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: center">' + element.simbolo + ' ' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/' + element.adjunto + '" target="_blank">' + (element.adjunto !== null ? element.adjunto : '') + '</a></td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + formatDateHour(element.fecha_registro) + '</td>' +
                        '<td style="border: none; text-align: center">' +
                        `< button type = "button" class= "btn btn-danger boton" data - toggle="tooltip" 
                            data - placement="bottom" data - row="${row}"
                            onClick = "anularPago(${element.id_pago},'${tipo}')" title = "Anular pago" >
        <i class="fas fa-trash"></i></button` +
                        '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `< table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Empresa</th>
                        <th style="border: none;">Cuenta origen</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                        <th style="border: none;">Anular</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table > `;
            }
            else {
                var tabla = `< table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
                </table > `;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
/*
function formatPagosComprobante(table_id, id, row) {
    $.ajax({
        type: 'GET',
        url: 'listarPagos/' + "comprobante" + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html += '<tr id="' + element.id_pago + '">' +
                        '<td style="border: none;">' + i + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.fecha_pago !== null ? formatDate(element.fecha_pago) : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: right">' + element.simbolo + '</td>' +
                        '<td style="border: none; text-align: right">' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/' + element.adjunto + '" target="_blank">' + (element.adjunto !== null ? element.adjunto : '') + '</a></td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + element.fecha_registro + '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `< table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table > `;
            }
            else {
                var tabla = `< table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
                </table > `;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function formatPagosRequerimientos(table_id, id, row) {
    $.ajax({
        type: 'GET',
        url: 'listarPagos/' + "requerimiento" + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html += '<tr id="' + element.id_pago + '">' +
                        '<td style="border: none;">' + i + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.fecha_pago !== null ? formatDate(element.fecha_pago) : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: right">' + element.simbolo + '</td>' +
                        '<td style="border: none; text-align: right">' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/' + element.adjunto + '" target="_blank">' + (element.adjunto !== null ? element.adjunto : '') + '</a></td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + element.fecha_registro + '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `< table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table > `;
            }
            else {
                var tabla = `< table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
                </table > `;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}*/